<?php

namespace DiyFormBundle\Tests\Service;

use DiyFormBundle\Entity\Form;
use DiyFormBundle\Service\PhoneNumberService;
use PHPUnit\Framework\TestCase;

class PhoneNumberServiceTest extends TestCase
{
    private PhoneNumberService $phoneNumberService;

    protected function setUp(): void
    {
        $this->phoneNumberService = new PhoneNumberService();
    }

    public function testBuildCaptchaCacheKey_基本电话号码()
    {
        // 创建表单模拟对象
        $form = $this->createMock(Form::class);
        $form->method('getId')->willReturn(123);
        
        $phoneNumber = '13800138000';
        
        $expected = 'DiyFormBundle_captcha_13800138000_123';
        $actual = $this->phoneNumberService->buildCaptchaCacheKey($form, $phoneNumber);
        
        $this->assertEquals($expected, $actual);
    }

    public function testBuildCaptchaCacheKey_包含特殊字符的号码()
    {
        // 创建表单模拟对象
        $form = $this->createMock(Form::class);
        $form->method('getId')->willReturn(456);
        
        $phoneNumber = '+86 139-1234"5678';
        
        $expected = 'DiyFormBundle_captcha__86_139-1234_5678_456';
        $actual = $this->phoneNumberService->buildCaptchaCacheKey($form, $phoneNumber);
        
        $this->assertEquals($expected, $actual);
    }

    public function testBuildCaptchaCacheKey_包含所有特殊字符处理()
    {
        // 创建表单模拟对象
        $form = $this->createMock(Form::class);
        $form->method('getId')->willReturn(789);
        
        $phoneNumber = "+'/ 138-0013-8000";
        
        $expected = 'DiyFormBundle_captcha_____138-0013-8000_789';
        $actual = $this->phoneNumberService->buildCaptchaCacheKey($form, $phoneNumber);
        
        $this->assertEquals($expected, $actual);
    }

    public function testBuildCaptchaCacheKey_空电话号码()
    {
        // 创建表单模拟对象
        $form = $this->createMock(Form::class);
        $form->method('getId')->willReturn(999);
        
        $phoneNumber = '';
        
        // 修正预期结果，空字符串不会生成额外的下划线
        $expected = 'DiyFormBundle_captcha__999';
        $actual = $this->phoneNumberService->buildCaptchaCacheKey($form, $phoneNumber);
        
        $this->assertEquals($expected, $actual);
    }
} 