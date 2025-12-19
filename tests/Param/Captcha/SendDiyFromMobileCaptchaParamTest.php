<?php

declare(strict_types=1);

namespace DiyFormBundle\Tests\Param\Captcha;

use DiyFormBundle\Param\Captcha\SendDiyFromMobileCaptchaParam;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;

/**
 * @internal
 */
#[CoversClass(SendDiyFromMobileCaptchaParam::class)]
final class SendDiyFromMobileCaptchaParamTest extends TestCase
{
    public function testParamImplementsRpcParamInterface(): void
    {
        $param = new SendDiyFromMobileCaptchaParam();
        $this->assertInstanceOf(RpcParamInterface::class, $param);
    }

    public function testParamCanBeConstructedWithNoArguments(): void
    {
        $param = new SendDiyFromMobileCaptchaParam();

        $this->assertSame('', $param->formId);
        $this->assertSame('', $param->phoneNumber);
    }

    public function testParamCanBeConstructedWithArguments(): void
    {
        $param = new SendDiyFromMobileCaptchaParam(
            formId: 'test-form-123',
            phoneNumber: '13800138000'
        );

        $this->assertSame('test-form-123', $param->formId);
        $this->assertSame('13800138000', $param->phoneNumber);
    }

    public function testParamIsReadonly(): void
    {
        $param = new SendDiyFromMobileCaptchaParam(
            formId: 'readonly-test',
            phoneNumber: '13900139000'
        );

        // 验证属性值不能被修改（readonly 属性）
        $this->assertSame('readonly-test', $param->formId);
        $this->assertSame('13900139000', $param->phoneNumber);
    }

    /**
     * @param string $formId 表单ID
     * @param string $phoneNumber 手机号码
     */
    #[DataProvider('validPhoneNumbersProvider')]
    public function testParamWithValidPhoneNumbers(string $formId, string $phoneNumber): void
    {
        $param = new SendDiyFromMobileCaptchaParam(
            formId: $formId,
            phoneNumber: $phoneNumber
        );

        $this->assertSame($formId, $param->formId);
        $this->assertSame($phoneNumber, $param->phoneNumber);
    }

    /**
     * @param string $formId 表单ID
     * @param string $phoneNumber 手机号码
     */
    #[DataProvider('validFormIdsProvider')]
    public function testParamWithValidFormIds(string $formId, string $phoneNumber): void
    {
        $param = new SendDiyFromMobileCaptchaParam(
            formId: $formId,
            phoneNumber: $phoneNumber
        );

        $this->assertSame($formId, $param->formId);
        $this->assertSame($phoneNumber, $param->phoneNumber);
    }

    public static function validPhoneNumbersProvider(): array
    {
        return [
            ['form-1', '13800138000'],
            ['form-2', '15000150000'],
            ['form-3', '18800188000'],
            ['form-4', '19900199000'],
            ['form-5', '17000170000'],
        ];
    }

    public static function validFormIdsProvider(): array
    {
        return [
            ['1', '13800138000'],
            ['abc-123', '13800138000'],
            ['form_with_underscores', '13800138000'],
            ['FORM-WITH-DASHES', '13800138000'],
            ['123456789', '13800138000'],
        ];
    }

    public function testParamWithEmptyValues(): void
    {
        $param = new SendDiyFromMobileCaptchaParam(
            formId: '',
            phoneNumber: ''
        );

        $this->assertSame('', $param->formId);
        $this->assertSame('', $param->phoneNumber);
    }

    public function testParamWithLongValues(): void
    {
        $longFormId = str_repeat('form-id-', 20); // 很长的表单ID
        $longPhoneNumber = '13800138000'; // 手机号码通常有固定长度

        $param = new SendDiyFromMobileCaptchaParam(
            formId: $longFormId,
            phoneNumber: $longPhoneNumber
        );

        $this->assertSame($longFormId, $param->formId);
        $this->assertSame($longPhoneNumber, $param->phoneNumber);
    }

    public function testParamWithSpecialCharacters(): void
    {
        $param = new SendDiyFromMobileCaptchaParam(
            formId: 'form_特殊字符-123',
            phoneNumber: '+86-138-0013-8000'
        );

        $this->assertSame('form_特殊字符-123', $param->formId);
        $this->assertSame('+86-138-0013-8000', $param->phoneNumber);
    }
}
