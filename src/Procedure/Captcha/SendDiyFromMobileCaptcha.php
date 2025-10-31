<?php

declare(strict_types=1);

namespace DiyFormBundle\Procedure\Captcha;

use Carbon\CarbonImmutable;
use DiyFormBundle\Entity\Form;
use DiyFormBundle\Event\SendMobileCaptchaEvent;
use DiyFormBundle\Notifier\Message\SmsTemplateMessage;
use DiyFormBundle\Repository\FormRepository;
use DiyFormBundle\Service\PhoneNumberService;
use DiyFormBundle\Service\SmsService;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPCLockBundle\Procedure\LockableProcedure;
use Tourze\JsonRPCLogBundle\Attribute\Log;
use Tourze\TextManageBundle\Service\TextFormatter;
use Tourze\UserIDBundle\Model\SystemUser;

#[MethodTag(name: '动态表单')]
#[MethodDoc(summary: '发送短信验证码')]
#[MethodExpose(method: 'SendDiyFromMobileCaptcha')]
#[Log]
class SendDiyFromMobileCaptcha extends LockableProcedure
{
    #[MethodParam(description: '表单ID')]
    public string $formId;

    #[MethodParam(description: '手机号码')]
    public string $phoneNumber;

    public function __construct(
        private readonly FormRepository $formRepository,
        private readonly SmsService $smsService,
        private readonly TextFormatter $textFormatter,
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
        if (null === $form) {
            throw new ApiException('找不到表单配置');
        }

        // Type assertion to ensure we have the correct type
        if (!$form instanceof Form) {
            throw new ApiException('表单数据类型错误');
        }

        $code = (string) mt_rand(100000, 999999);

        $event = new SendMobileCaptchaEvent();
        $event->setForm($form);
        $event->setPhoneNumber($this->phoneNumber);
        $event->setCode($code);
        $event->setSender(SystemUser::instance());

        // Ensure we have a valid user for the receiver
        $currentUser = $this->security->getUser();
        if (null === $currentUser) {
            throw new ApiException('用户未登录，无法发送验证码');
        }
        $event->setReceiver($currentUser);
        try {
            $this->eventDispatcher->dispatch($event);
        } catch (\Throwable $exception) {
            throw new ApiException('短信发送失败', previous: $exception);
        }

        if (false === $event->isSent()) {
            $params = [
                'user' => $this->security->getUser(),
                'now' => CarbonImmutable::now(),
                'code' => $code,
            ];

            $sms = new SmsTemplateMessage(
                $this->phoneNumber,
                $this->textFormatter->formatText('您的验证码是：{{ code }}', $params),
            );
            $templateParams = [
                'code' => $code,
            ];
            $sms->setTemplateParam($templateParams);

            $this->smsService->send($sms);
        }

        // 将手机号码/表单合并成一个key存储起来
        $captchaKey = $this->phoneNumberService->buildCaptchaCacheKey($form, $this->phoneNumber);
        $this->cache->get($captchaKey, function () use ($code) {
            return $code;
        }, 60 * 60);

        return [
            '__message' => '发送成功',
        ];
    }
}
