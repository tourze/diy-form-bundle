<?php

declare(strict_types=1);

namespace DiyFormBundle\Notifier\Message;

use Symfony\Component\Notifier\Message\SmsMessage;

/**
 * 国内比较多用的，带模板的消息类型.
 */
class SmsTemplateMessage extends SmsMessage
{
    /**
     * @var string 短信模板code
     */
    private string $templateCode = '';

    /**
     * @var string 短信签名名称
     */
    private string $signName = '';

    /**
     * @var array<string, mixed> 短信模板变量对应的实际值
     */
    private array $templateParam = [];

    public function getTemplateCode(): string
    {
        return $this->templateCode;
    }

    public function setTemplateCode(string $templateCode): void
    {
        $this->templateCode = $templateCode;
    }

    public function getSignName(): string
    {
        return $this->signName;
    }

    public function setSignName(string $signName): void
    {
        $this->signName = $signName;
    }

    /**
     * @return array<string, mixed>
     */
    public function getTemplateParam(): array
    {
        return $this->templateParam;
    }

    /**
     * @param array<string, mixed> $templateParam
     */
    public function setTemplateParam(array $templateParam): void
    {
        $this->templateParam = $templateParam;
    }
}
