<?php

declare(strict_types=1);

namespace DiyFormBundle\Service;

use DiyFormBundle\Entity\Form;

class PhoneNumberService
{
    public function buildCaptchaCacheKey(Form $form, string $phoneNumber): string
    {
        // 特殊符号特殊处理
        $phoneNumber = str_replace(['"', "'", ' ', '/', '+'], '_', $phoneNumber);

        return "DiyFormBundle_captcha_{$phoneNumber}_{$form->getId()}";
    }
}
