<?php

declare(strict_types=1);

namespace DiyFormBundle\Tests\Service;

use DiyFormBundle\Entity\Form;
use DiyFormBundle\Service\PhoneNumberService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(PhoneNumberService::class)]
final class PhoneNumberServiceTest extends TestCase
{
    private PhoneNumberService $phoneNumberService;

    private function getPhoneNumberService(): PhoneNumberService
    {
        return $this->phoneNumberService ??= new PhoneNumberService();
    }

    public function testBuildCaptchaCacheKey基本电话号码(): void
    {
        /*
         * 模拟Form实体进行测试：
         * 1. Form是Doctrine实体，代表表单定义
         * 2. PhoneNumberService需要表单ID来构建验证码缓存key
         * 3. 测试时使用Mock可控制ID返回值，保证测试的可预测性
         */
        // 创建表单模拟对象
        $form = $this->createMock(Form::class);
        $form->method('getId')->willReturn(123);

        $phoneNumber = '13800138000';

        $expected = 'DiyFormBundle_captcha_13800138000_123';
        $actual = $this->getPhoneNumberService()->buildCaptchaCacheKey($form, $phoneNumber);

        $this->assertEquals($expected, $actual);
    }

    public function testBuildCaptchaCacheKey包含特殊字符的号码(): void
    {
        /*
         * 模拟Form实体进行测试：
         * 1. Form是Doctrine实体，代表表单定义
         * 2. PhoneNumberService需要表单ID来构建验证码缓存key
         * 3. 测试时使用Mock可控制ID返回值，保证测试的可预测性
         */
        // 创建表单模拟对象
        $form = $this->createMock(Form::class);
        $form->method('getId')->willReturn(456);

        $phoneNumber = '+86 139-1234"5678';

        $expected = 'DiyFormBundle_captcha__86_139-1234_5678_456';
        $actual = $this->getPhoneNumberService()->buildCaptchaCacheKey($form, $phoneNumber);

        $this->assertEquals($expected, $actual);
    }

    public function testBuildCaptchaCacheKey包含所有特殊字符处理(): void
    {
        /*
         * 模拟Form实体进行测试：
         * 1. Form是Doctrine实体，代表表单定义
         * 2. PhoneNumberService需要表单ID来构建验证码缓存key
         * 3. 测试时使用Mock可控制ID返回值，保证测试的可预测性
         */
        // 创建表单模拟对象
        $form = $this->createMock(Form::class);
        $form->method('getId')->willReturn(789);

        $phoneNumber = "+'/ 138-0013-8000";

        $expected = 'DiyFormBundle_captcha_____138-0013-8000_789';
        $actual = $this->getPhoneNumberService()->buildCaptchaCacheKey($form, $phoneNumber);

        $this->assertEquals($expected, $actual);
    }

    public function testBuildCaptchaCacheKey空电话号码(): void
    {
        /*
         * 模拟Form实体进行测试：
         * 1. Form是Doctrine实体，代表表单定义
         * 2. PhoneNumberService需要表单ID来构建验证码缓存key
         * 3. 测试时使用Mock可控制ID返回值，保证测试的可预测性
         */
        // 创建表单模拟对象
        $form = $this->createMock(Form::class);
        $form->method('getId')->willReturn(999);

        $phoneNumber = '';

        // 修正预期结果，空字符串不会生成额外的下划线
        $expected = 'DiyFormBundle_captcha__999';
        $actual = $this->getPhoneNumberService()->buildCaptchaCacheKey($form, $phoneNumber);

        $this->assertEquals($expected, $actual);
    }
}
