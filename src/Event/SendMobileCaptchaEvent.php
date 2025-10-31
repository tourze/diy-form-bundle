<?php

declare(strict_types=1);

namespace DiyFormBundle\Event;

use DiyFormBundle\Entity\Form;
use Tourze\UserEventBundle\Event\UserInteractionEvent;

class SendMobileCaptchaEvent extends UserInteractionEvent
{
    private Form $form;

    private string $phoneNumber;

    private string $code;

    private bool $sent = false;

    public function getForm(): Form
    {
        return $this->form;
    }

    public function setForm(Form $form): void
    {
        $this->form = $form;
    }

    public function getPhoneNumber(): string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(string $phoneNumber): void
    {
        $this->phoneNumber = $phoneNumber;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    public function isSent(): bool
    {
        return $this->sent;
    }

    public function setSent(bool $sent): void
    {
        $this->sent = $sent;
    }
}
