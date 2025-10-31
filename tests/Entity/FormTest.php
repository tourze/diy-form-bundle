<?php

declare(strict_types=1);

namespace DiyFormBundle\Tests\Entity;

use DiyFormBundle\Entity\Analyse;
use DiyFormBundle\Entity\Field;
use DiyFormBundle\Entity\Form;
use DiyFormBundle\Entity\Record;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(Form::class)]
final class FormTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new Form();
    }

    /**
     * @return iterable<array{0: string, 1: mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        yield 'title' => ['title', '测试表单'];
        yield 'description' => ['description', '这是一个测试描述'];
        yield 'remark' => ['remark', '这是一个测试备注'];
        yield 'valid' => ['valid', true];
        yield 'sortNumber' => ['sortNumber', 100];
    }

    public function testFields初始化为空集合(): void
    {
        $form = new Form();
        $fields = $form->getFields();

        $this->assertInstanceOf(ArrayCollection::class, $fields);
        $this->assertTrue($fields->isEmpty());
    }

    public function testAddField添加字段并建立双向关系(): void
    {
        $form = new Form();
        $field = $this->createMock(Field::class);
        $field->expects($this->once())
            ->method('setForm')
            ->with(self::callback(fn ($arg) => $arg === $form))
        ;

        $form->addField($field);
        $this->assertTrue($form->getFields()->contains($field));
    }

    public function testRecords初始化为空集合(): void
    {
        $form = new Form();
        $records = $form->getRecords();

        $this->assertInstanceOf(ArrayCollection::class, $records);
        $this->assertTrue($records->isEmpty());
    }

    public function testAddRecord添加记录并建立双向关系(): void
    {
        $form = new Form();
        $record = $this->createMock(Record::class);
        $record->expects($this->once())
            ->method('setForm')
            ->with(self::callback(fn ($arg) => $arg === $form))
        ;

        $form->addRecord($record);
        $this->assertTrue($form->getRecords()->contains($record));
    }

    public function testAnalyses初始化为空集合(): void
    {
        $form = new Form();
        $analyses = $form->getAnalyses();

        $this->assertInstanceOf(ArrayCollection::class, $analyses);
        $this->assertTrue($analyses->isEmpty());
    }

    public function testAddAnalysis添加分析并建立双向关系(): void
    {
        $form = new Form();
        $analysis = $this->createMock(Analyse::class);
        $analysis->expects($this->once())
            ->method('setForm')
            ->with(self::callback(fn ($arg) => $arg === $form))
        ;

        $form->addAnalysis($analysis);

        $this->assertTrue($form->getAnalyses()->contains($analysis));
    }

    public function testToString无ID时返回空字符串(): void
    {
        $form = new Form();
        $this->assertEquals('', (string) $form);
    }

    public function testToString有ID时返回标题和ID(): void
    {
        $form = new Form();
        $title = '测试表单';
        $form->setTitle($title);

        // 使用反射设置私有属性id
        $reflectionClass = new \ReflectionClass(Form::class);
        $property = $reflectionClass->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($form, 123);

        $this->assertEquals('测试表单(123)', (string) $form);
    }
}
