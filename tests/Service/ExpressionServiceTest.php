<?php

namespace DiyFormBundle\Tests\Service;

use DiyFormBundle\Entity\Record;
use DiyFormBundle\Service\ExpressionService;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class ExpressionServiceTest extends TestCase
{
    private LoggerInterface $logger;
    private ExpressionService $expressionService;

    protected function setUp(): void
    {
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->expressionService = new ExpressionService($this->logger);
    }

    public function testBindRecordFunction_添加所有表达式函数()
    {
        // 创建模拟对象
        $expressionLanguage = $this->createMock(ExpressionLanguage::class);
        $record = $this->createMock(Record::class);
        $answerTags = ['tag1' => 'value1', 'tag2' => 'value2'];

        // 在 PHPUnit 10 中，withConsecutive 被移除，所以我们需要一个新的方法来测试
        // 我们期望 addFunction 方法被调用 7 次
        $expressionLanguage->expects($this->exactly(7))
            ->method('addFunction');

        // 调用被测试的方法
        $this->expressionService->bindRecordFunction($expressionLanguage, $record, $answerTags);
    }

    public function testConstruct_设置Logger()
    {
        // 使用反射API检查私有属性
        $reflectionClass = new \ReflectionClass(ExpressionService::class);
        $loggerProperty = $reflectionClass->getProperty('logger');
        $loggerProperty->setAccessible(true);

        // 验证构造函数是否正确设置了logger属性
        $logger = $loggerProperty->getValue($this->expressionService);
        $this->assertSame($this->logger, $logger);
    }
} 