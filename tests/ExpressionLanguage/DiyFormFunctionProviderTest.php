<?php

declare(strict_types=1);

namespace DiyFormBundle\Tests\ExpressionLanguage;

use DiyFormBundle\Entity\Form;
use DiyFormBundle\Entity\Record;
use DiyFormBundle\ExpressionLanguage\DiyFormFunctionProvider;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * @internal
 */
#[CoversClass(DiyFormFunctionProvider::class)]
#[RunTestsInSeparateProcesses]
final class DiyFormFunctionProviderTest extends AbstractIntegrationTestCase
{
    private DiyFormFunctionProvider $provider;

    protected function onSetUp(): void
    {
    }

    private function getProvider(): DiyFormFunctionProvider
    {
        return $this->provider ??= self::getService(DiyFormFunctionProvider::class);
    }

    private function createFormAndRecord(): Record
    {
        $form = new Form();
        $form->setTitle('测试表单-' . uniqid());
        $form->setValid(true);
        $form->setStartTime(new \DateTimeImmutable());
        $form->setEndTime(new \DateTimeImmutable('+1 hour'));

        $record = new Record();
        $record->setForm($form);
        $record->setStartTime(new \DateTimeImmutable());
        $record->setFinished(false);

        $em = self::getEntityManager();
        $em->persist($form);
        $em->persist($record);
        $em->flush();

        return $record;
    }

    public function testGetFunctions未设置上下文时返回空数组(): void
    {
        $functions = $this->getProvider()->getFunctions();
        $this->assertIsArray($functions);
        $this->assertEmpty($functions);
    }

    public function testSetContextAndGetFunctions设置上下文后返回正确函数数量(): void
    {
        // 创建真实的 Record 实体用于测试
        $record = $this->createFormAndRecord();
        $answerTags = ['tag1' => 'value1'];

        $provider = $this->getProvider();
        $provider->setContext($record, $answerTags);
        $functions = $provider->getFunctions();

        $this->assertIsArray($functions);
        $this->assertCount(7, $functions);
    }

    public function testSetContext正确设置记录和标签(): void
    {
        // 创建真实的 Record 实体用于测试
        $record = $this->createFormAndRecord();
        $answerTags = ['tag1' => 'value1', 'tag2' => 'value2'];

        $provider = $this->getProvider();
        $provider->setContext($record, $answerTags);
        $functions = $provider->getFunctions();

        // 验证函数被正确创建
        $this->assertNotEmpty($functions);
        foreach ($functions as $function) {
            $this->assertInstanceOf(ExpressionFunction::class, $function);
        }
    }

    public function testGetFunctions创建正确的表达式函数类型(): void
    {
        // 创建真实的 Record 实体用于测试
        $record = $this->createFormAndRecord();
        $answerTags = [];

        $provider = $this->getProvider();
        $provider->setContext($record, $answerTags);
        $functions = $provider->getFunctions();

        $functionNames = array_map(fn ($func) => $func->getName(), $functions);

        // 验证包含期望的函数名
        $this->assertContains('answerItem', $functionNames);
        $this->assertContains('获取指定题号的回答提交', $functionNames);
        $this->assertContains('answerDiffInMonth', $functionNames);
        $this->assertContains('计算指定题号回答跟当前时间的月份差', $functionNames);
        $this->assertContains('answerItemCount', $functionNames);
        $this->assertContains('获取指定题号的回答数量', $functionNames);
        $this->assertContains('answerTagInclude', $functionNames);
    }
}
