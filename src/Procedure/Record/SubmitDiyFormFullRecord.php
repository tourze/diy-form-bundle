<?php

declare(strict_types=1);

namespace DiyFormBundle\Procedure\Record;

use Carbon\CarbonImmutable;
use DiyFormBundle\Entity\Data;
use DiyFormBundle\Entity\Field;
use DiyFormBundle\Entity\Form;
use DiyFormBundle\Entity\Record;
use DiyFormBundle\Enum\FieldType;
use DiyFormBundle\Event\SubmitDiyFormFullRecordEvent;
use DiyFormBundle\Repository\FieldRepository;
use DiyFormBundle\Repository\FormRepository;
use DiyFormBundle\Service\PhoneNumberService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPCLockBundle\Procedure\LockableProcedure;
use Tourze\JsonRPCLogBundle\Attribute\Log;
use Tourze\UserIDBundle\Model\SystemUser;
use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Json\Json;

#[IsGranted(attribute: 'IS_AUTHENTICATED_FULLY')]
#[MethodDoc(summary: '一次性提交完整的答题记录')]
#[MethodTag(name: '动态表单')]
#[MethodExpose(method: 'SubmitDiyFormFullRecord')]
#[Log]
class SubmitDiyFormFullRecord extends LockableProcedure
{
    #[MethodParam(description: '表单ID')]
    public string $formId;

    /** @var array<string, mixed> */
    #[MethodParam(description: '提交数据')]
    public array $data;

    #[MethodParam(description: '开始答题时间')]
    public ?string $startTime = null;

    #[MethodParam(description: '邀请人信息')]
    public ?string $inviter = null;

    public function __construct(
        private readonly FormRepository $formRepository,
        private readonly FieldRepository $fieldRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly UserLoaderInterface $userLoader,
        private readonly CacheInterface $cache,
        private readonly PhoneNumberService $phoneNumberService,
        private readonly Security $security,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function execute(): array
    {
        $form = $this->validateAndGetForm();
        $record = $this->createRecord($form);
        $this->processFormData($form, $record);
        $this->entityManager->flush();

        return $this->dispatchEvent($record);
    }

    private function validateAndGetForm(): Form
    {
        $form = $this->formRepository->findOneBy([
            'id' => $this->formId,
            'valid' => true,
        ]);

        if (null === $form) {
            throw new ApiException('找不到表单配置');
        }

        $this->entityManager->getUnitOfWork()->markReadOnly($form);

        return $form;
    }

    private function createRecord(Form $form): Record
    {
        $record = new Record();
        $record->setForm($form);
        $user = $this->security->getUser();
        if (null !== $user) {
            $record->setUser($user);
        }
        $record->setFinished(true);

        if (null !== $this->inviter) {
            $inviter = $this->userLoader->loadUserByIdentifier($this->inviter);
            $record->setInviter($inviter);
        }

        try {
            $record->setStartTime(null !== $this->startTime ? CarbonImmutable::parse($this->startTime) : CarbonImmutable::now());
        } catch (\Throwable) {
            $record->setStartTime(CarbonImmutable::now());
        }

        // Ensure data is properly typed as array<string, mixed>
        $record->setSubmitData($this->data);
        $record->setFinishTime(CarbonImmutable::now());
        $this->entityManager->persist($record);

        return $record;
    }

    private function processFormData(Form $form, Record $record): void
    {
        foreach ($this->data as $datum) {
            if (!is_array($datum) || !isset($datum['fieldId'], $datum['input'])) {
                continue;
            }

            $field = $this->fieldRepository->findOneBy([
                'form' => $form,
                'id' => $datum['fieldId'],
            ]);

            if (null === $field) {
                continue;
            }

            $input = $this->processFieldInput($field, $datum['input'], $form);
            $this->createDataEntry($record, $field, $input);
        }
    }

    /**
     * @param Field $field
     * @param mixed $input
     * @param Form $form
     * @return mixed
     */
    private function processFieldInput(Field $field, $input, Form $form)
    {
        if (FieldType::CAPTCHA_MOBILE_PHONE === $field->getType()) {
            return $this->processMobilePhoneField($input, $form);
        }

        return $input;
    }

    /**
     * @param mixed $input
     * @param Form $form
     * @return string
     */
    private function processMobilePhoneField($input, $form): string
    {
        // 确保 $input 是数组
        if (!is_array($input)) {
            throw new ApiException('手机号码格式不正确');
        }

        $phoneNumber = ArrayHelper::getValue($input, 'phoneNumber');
        if (null === $phoneNumber) {
            throw new ApiException('请填写手机号码');
        }

        $code = ArrayHelper::getValue($input, 'captcha');
        if (null === $code || '' === $code) {
            throw new ApiException('请填写手机验证码');
        }

        if (!is_string($phoneNumber)) {
            throw new ApiException('手机号码格式不正确');
        }

        $captchaKey = $this->phoneNumberService->buildCaptchaCacheKey($form, $phoneNumber);
        $dbCode = $this->cache->get($captchaKey, function () {
            return null;
        });

        // 确保缓存值存在
        if (null === $dbCode) {
            throw new ApiException('请先接收手机验证码');
        }

        // 比较验证码
        /** @phpstan-ignore cast.string,notIdentical.alwaysTrue,cast.useless,deadCode.unreachable */
        if ((string) $dbCode !== (string) $code) {
            throw new ApiException('手机验证码不正确');
        }

        /** @phpstan-ignore deadCode.unreachable */
        return $phoneNumber;
    }

    private function createDataEntry(Record $record, Field $field, mixed $input): void
    {
        $data = new Data();
        $data->setRecord($record);
        $data->setField($field);

        // Safe conversion to string
        if (is_array($input)) {
            $data->setInput(Json::encode($input));
        } elseif (is_scalar($input) || null === $input) {
            $data->setInput((string) $input);
        } else {
            $data->setInput('[complex_type]');
        }

        $data->setSkip(false);
        $this->entityManager->persist($data);
    }

    /**
     * @return array<string, mixed>
     */
    private function dispatchEvent(Record $record): array
    {
        $event = new SubmitDiyFormFullRecordEvent();
        $event->setRecord($record);

        $sender = $this->security->getUser();
        if (null !== $sender) {
            $event->setSender($sender);
        }

        $event->setReceiver($record->getInviter() ?? SystemUser::instance());
        $event->setJsonRpcResult([
            '__message' => '提交成功',
            'record' => [
                'id' => $record->getId(),
            ],
        ]);
        $this->eventDispatcher->dispatch($event);

        return $event->getJsonRpcResult();
    }
}
