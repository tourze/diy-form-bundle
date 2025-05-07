<?php

namespace DiyFormBundle\Procedure\Record;

use AppBundle\Service\UserService;
use Carbon\Carbon;
use DiyFormBundle\Entity\Data;
use DiyFormBundle\Entity\Record;
use DiyFormBundle\Enum\FieldType;
use DiyFormBundle\Event\SubmitDiyFormFullRecordEvent;
use DiyFormBundle\Repository\FieldRepository;
use DiyFormBundle\Repository\FormRepository;
use DiyFormBundle\Service\PhoneNumberService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\SimpleCache\CacheInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Http\Attribute\IsGranted;
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

#[IsGranted('IS_AUTHENTICATED_FULLY')]
#[MethodDoc('一次性提交完整的答题记录')]
#[MethodTag('动态表单')]
#[MethodExpose('SubmitDiyFormFullRecord')]
#[Log]
class SubmitDiyFormFullRecord extends LockableProcedure
{
    #[MethodParam('表单ID')]
    public string $formId;

    #[MethodParam('提交数据')]
    public array $data;

    #[MethodParam('开始答题时间')]
    public ?string $startTime = null;

    #[MethodParam('邀请人信息')]
    public ?string $inviter = null;

    public function __construct(
        private readonly FormRepository $formRepository,
        private readonly FieldRepository $fieldRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly UserService $userService,
        private readonly CacheInterface $cache,
        private readonly PhoneNumberService $phoneNumberService,
        private readonly Security $security,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function execute(): array
    {
        $form = $this->formRepository->findOneBy([
            'id' => $this->formId,
            'valid' => true,
        ]);
        if (!$form) {
            throw new ApiException('找不到表单配置');
        }
        $this->entityManager->getUnitOfWork()->markReadOnly($form);

        $record = new Record();
        $record->setForm($form);
        $record->setUser($this->security->getUser());
        $record->setFinished(true);

        // 获取邀请人信息
        if ($this->inviter) {
            $inviter = $this->userService->findUserByIdentity($this->inviter);
            $record->setInviter($inviter);
        }

        try {
            $record->setStartTime($this->startTime ? Carbon::parse($this->startTime) : Carbon::now());
        } catch (\Throwable) {
            $record->setStartTime(Carbon::now());
        }

        $record->setSubmitData($this->data);
        $record->setFinishTime(Carbon::now());
        $this->entityManager->persist($record);

        // 答题明细
        foreach ($this->data as $datum) {
            $field = $this->fieldRepository->findOneBy([
                'form' => $form,
                'id' => $datum['fieldId'],
            ]);
            if (!$field) {
                continue;
            }

            $input = $datum['input'];

            // 手机号码需要特别处理
            if (FieldType::CAPTCHA_MOBILE_PHONE === $field->getType()) {
                $phoneNumber = ArrayHelper::getValue($input, 'phoneNumber');
                if (!$phoneNumber) {
                    throw new ApiException('请填写手机号码');
                }
                $code = ArrayHelper::getValue($input, 'captcha');
                if (empty($code)) {
                    throw new ApiException('请填写手机验证码');
                }

                $captchaKey = $this->phoneNumberService->buildCaptchaCacheKey($form, $phoneNumber);
                if (!$this->cache->has($captchaKey)) {
                    throw new ApiException('请先接收手机验证码');
                }
                $dbCode = $this->cache->get($captchaKey);
                if ($dbCode !== $code) {
                    throw new ApiException('手机验证码不正确');
                }
                // 只存储手机号
                $input = $phoneNumber;
            }

            $data = new Data();
            $data->setRecord($record);
            $data->setField($field);
            $data->setInput(is_array($input) ? Json::encode($input) : strval($input));
            $data->setSkip(false);
            $this->entityManager->persist($data);
        }

        $this->entityManager->flush();

        $event = new SubmitDiyFormFullRecordEvent();
        $event->setRecord($record);
        $event->setSender($this->security->getUser());
        $event->setReceiver($record->getInviter() ?: SystemUser::instance());
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
