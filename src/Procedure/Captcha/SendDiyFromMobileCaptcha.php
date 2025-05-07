<?php

namespace DiyFormBundle\Procedure\Captcha;

use AppBundle\Notifier\Message\SmsTemplateMessage;
use Carbon\Carbon;
use DiyFormBundle\Event\SendMobileCaptchaEvent;
use DiyFormBundle\Repository\FormRepository;
use DiyFormBundle\Service\PhoneNumberService;
use DiyFormBundle\Service\SmsService;
use Psr\SimpleCache\CacheInterface;
use Symfony\Bundle\SecurityBundle\Security;
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

#[MethodTag('动态表单')]
#[MethodDoc('发送短信验证码')]
#[MethodExpose('SendDiyFromMobileCaptcha')]
#[Log]
class SendDiyFromMobileCaptcha extends LockableProcedure
{
    #[MethodParam('表单ID')]
    public string $formId;

    #[MethodParam('手机号码')]
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
        if (!$form) {
            throw new ApiException('找不到表单配置');
        }

        $code = (string) mt_rand(100000, 999999);

        $event = new SendMobileCaptchaEvent();
        $event->setForm($form);
        $event->setPhoneNumber($this->phoneNumber);
        $event->setCode($code);
        $event->setSender(SystemUser::instance());
        $event->setReceiver($this->security->getUser());
        try {
            $this->eventDispatcher->dispatch($event);
        } catch (\Throwable $exception) {
            throw new ApiException('短信发送失败', previous: $exception);
        }

        if (!$event->isSent()) {
            $params = [
                'user' => $this->security->getUser(),
                'now' => Carbon::now(),
                'code' => $code,
            ];

            $sms = new SmsTemplateMessage(
                $this->phoneNumber,
                $this->textFormatter->formatText('您的登录验证码是：{{ code }}', $params),
            );
            $templateParams = [
                'code' => $code,
            ];
            $sms->setTemplateParam($templateParams);

            $this->smsService->send($sms);
        }

        // 将手机号码/表单合并成一个key存储起来
        $captchaKey = $this->phoneNumberService->buildCaptchaCacheKey($form, $this->phoneNumber);
        $this->cache->set($captchaKey, $code, HOUR_IN_SECONDS);

        return [
            '__message' => '发送成功',
        ];
    }
}
