<?php

declare(strict_types=1);

namespace DiyFormBundle\Tests\Service;

use DiyFormBundle\Entity\Form;
use DiyFormBundle\Service\PhoneNumberService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * @internal
 */
#[CoversClass(PhoneNumberService::class)]
#[RunTestsInSeparateProcesses]
final class PhoneNumberServiceTest extends AbstractIntegrationTestCase
{
    protected function onSetUp(): void
    {
        // 集成测试基类要求的初始化方法
    }

    private function getPhoneNumberService(): PhoneNumberService
    {
        return self::getService(PhoneNumberService::class);
    }

    private function createForm(string $title): Form
    {
        $form = new Form();
        $form->setTitle($title);
        $form->setValid(true);
        $form->setStartTime(new \DateTimeImmutable());
        $form->setEndTime(new \DateTimeImmutable('+1 hour'));

        $em = self::getEntityManager();
        $em->persist($form);
        $em->flush();

        return $form;
    }

    public function testBuildCaptchaCacheKey基本电话号码(): void
    {
        // 创建真实的Form实体并持久化到数据库
        $form = $this->createForm('测试表单基本-' . uniqid());
        $formId = $form->getId();

        $phoneNumber = '13800138000';

        $expected = "DiyFormBundle_captcha_{$phoneNumber}_{$formId}";
        $actual = $this->getPhoneNumberService()->buildCaptchaCacheKey($form, $phoneNumber);

        $this->assertEquals($expected, $actual);
    }

    public function testBuildCaptchaCacheKey包含特殊字符的号码(): void
    {
        // 创建真实的Form实体并持久化到数据库
        $form = $this->createForm('测试表单特殊字符-' . uniqid());
        $formId = $form->getId();

        $phoneNumber = '+86 139-1234"5678';

        $expected = "DiyFormBundle_captcha__86_139-1234_5678_{$formId}";
        $actual = $this->getPhoneNumberService()->buildCaptchaCacheKey($form, $phoneNumber);

        $this->assertEquals($expected, $actual);
    }

    public function testBuildCaptchaCacheKey包含所有特殊字符处理(): void
    {
        // 创建真实的Form实体并持久化到数据库
        $form = $this->createForm('测试表单全特殊-' . uniqid());
        $formId = $form->getId();

        $phoneNumber = "+'/ 138-0013-8000";

        $expected = "DiyFormBundle_captcha_____138-0013-8000_{$formId}";
        $actual = $this->getPhoneNumberService()->buildCaptchaCacheKey($form, $phoneNumber);

        $this->assertEquals($expected, $actual);
    }

    public function testBuildCaptchaCacheKey空电话号码(): void
    {
        // 创建真实的Form实体并持久化到数据库
        $form = $this->createForm('测试表单空号码-' . uniqid());
        $formId = $form->getId();

        $phoneNumber = '';

        // 空字符串不会生成额外的下划线
        $expected = "DiyFormBundle_captcha__{$formId}";
        $actual = $this->getPhoneNumberService()->buildCaptchaCacheKey($form, $phoneNumber);

        $this->assertEquals($expected, $actual);
    }
}
