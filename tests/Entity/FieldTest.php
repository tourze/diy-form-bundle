<?php

declare(strict_types=1);

namespace DiyFormBundle\Tests\Entity;

use DiyFormBundle\Entity\Field;
use DiyFormBundle\Entity\Option;
use DiyFormBundle\Enum\FieldType;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;
use Yiisoft\Json\Json;

/**
 * @internal
 */
#[CoversClass(Field::class)]
final class FieldTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new Field();
    }

    /**
     * @return iterable<array{0: string, 1: mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        yield 'valid' => ['valid', true];
        yield 'sn' => ['sn', 'test-serial-number'];
        yield 'type' => ['type', FieldType::STRING];
        yield 'sortNumber' => ['sortNumber', 100];
        yield 'required' => ['required', true];
        yield 'maxInput' => ['maxInput', 10];
        yield 'title' => ['title', '测试字段'];
        yield 'placeholder' => ['placeholder', '请输入测试内容'];
        yield 'bgImage' => ['bgImage', '/path/to/image.jpg'];
        yield 'description' => ['description', '这是一个测试描述'];
        yield 'showExpression' => ['showExpression', 'form.field1 == "value"'];
        yield 'extra' => ['extra', Json::encode(['key' => 'value'])];
    }

    public function testExtraConfig解析Extra字段并返回数组(): void
    {
        $field = new Field();
        $extraData = ['key' => 'value', 'nested' => ['a' => 1]];
        $extraJson = Json::encode($extraData);
        $field->setExtra($extraJson);

        $result = $field->getExtraConfig();
        $this->assertEquals($extraData, $result);
    }

    public function testExtraConfig当Extra为null时返回空数组(): void
    {
        $field = new Field();
        $field->setExtra(null);

        $result = $field->getExtraConfig();
        $this->assertEmpty($result);
    }

    public function testExtraConfig当Extra不是有效JSON时返回空数组(): void
    {
        $field = new Field();
        $field->setExtra('invalid json');

        $result = $field->getExtraConfig();
        $this->assertEmpty($result);
    }

    public function testOptions初始化为空集合(): void
    {
        $field = new Field();
        $options = $field->getOptions();

        $this->assertInstanceOf(ArrayCollection::class, $options);
        $this->assertTrue($options->isEmpty());
    }

    public function testAddOption添加选项并建立双向关系(): void
    {
        $field = new Field();
        $option = $this->createMock(Option::class);
        $option->expects($this->once())
            ->method('setField')
            ->with(self::callback(fn ($arg) => $arg === $field))
        ;

        $field->addOption($option);
        $this->assertTrue($field->getOptions()->contains($option));
    }

    public function testRemoveOption移除选项并解除双向关系(): void
    {
        $field = new Field();
        $option = $this->createMock(Option::class);

        // 模拟setField方法调用 - 第一次调用是addOption时，第二次调用是removeOption时
        $option->expects($this->exactly(2))
            ->method('setField')
            ->willReturnCallback(function ($arg) use ($option) {
                return $option;
            })
        ;

        // 模拟选项的getField方法返回当前field（这是removeOption中的条件检查）
        $option->expects($this->any())
            ->method('getField')
            ->willReturn($field)
        ;

        // 先添加选项
        $field->addOption($option);
        $this->assertTrue($field->getOptions()->contains($option));

        // 然后移除选项
        $field->removeOption($option);

        $this->assertFalse($field->getOptions()->contains($option));
    }

    public function testToString无ID时返回空字符串(): void
    {
        $field = new Field();
        // 设置必要的属性，但保持ID为空
        $field->setType(FieldType::STRING);
        $field->setTitle('测试字段');
        $field->setSn('F001');

        $this->assertEquals('', (string) $field);
    }

    public function testToString有ID时返回正确的字符串表示(): void
    {
        $field = new Field();
        // 设置必要的属性
        $field->setType(FieldType::STRING);
        $field->setTitle('测试字段');
        $field->setSn('F001');

        // 使用反射设置私有属性id
        $reflectionClass = new \ReflectionClass(Field::class);
        $property = $reflectionClass->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($field, 123);

        $expected = 'F001.字符串 测试字段';
        $this->assertEquals($expected, (string) $field);
    }

    public function testToString包含显示表达式(): void
    {
        $field = new Field();
        // 设置必要的属性
        $field->setType(FieldType::STRING);
        $field->setTitle('测试字段');
        $field->setSn('F001');
        $field->setShowExpression('form.field1 == "value"');

        // 使用反射设置私有属性id
        $reflectionClass = new \ReflectionClass(Field::class);
        $property = $reflectionClass->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($field, 123);

        $expected = 'F001.【如果 form.field1 == "value"】字符串 测试字段';
        $this->assertEquals($expected, (string) $field);
    }

    public function testRetrievePlainArray返回正确的数组结构(): void
    {
        $field = new Field();
        // 设置字段属性
        $field->setSn('F001');
        $field->setType(FieldType::STRING);
        $field->setRequired(true);
        $field->setMaxInput(100);
        $field->setTitle('测试字段');
        $field->setPlaceholder('请输入内容');
        $field->setBgImage('/images/bg.jpg');
        $field->setDescription('这是一个测试字段');
        $field->setValid(true);
        $field->setExtra('{"custom":"value"}');

        // 设置ID（使用反射）
        $reflectionClass = new \ReflectionClass(Field::class);
        $property = $reflectionClass->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($field, 123);

        // 创建一个模拟的选项
        $option = $this->createMock(Option::class);
        $option->expects($this->once())
            ->method('retrievePlainArray')
            ->willReturn([
                'id' => 1,
                'title' => '选项1',
                'value' => 'opt1',
            ])
        ;
        $option->expects($this->once())
            ->method('setField')
        ;

        $field->addOption($option);

        // 获取结果
        $result = $field->retrievePlainArray();

        // 验证数组结构
        $this->assertArrayHasKey('sn', $result);
        $this->assertArrayHasKey('type', $result);
        $this->assertArrayHasKey('required', $result);
        $this->assertArrayHasKey('maxInput', $result);
        $this->assertArrayHasKey('title', $result);
        $this->assertArrayHasKey('placeholder', $result);
        $this->assertArrayHasKey('bgImage', $result);
        $this->assertArrayHasKey('options', $result);
        $this->assertArrayHasKey('description', $result);
        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('createTime', $result);
        $this->assertArrayHasKey('updateTime', $result);
        $this->assertArrayHasKey('valid', $result);
        $this->assertArrayHasKey('extraConfig', $result);
        $this->assertArrayHasKey('extra', $result);

        // 验证值
        $this->assertEquals('F001', $result['sn']);
        $this->assertEquals(FieldType::STRING, $result['type']);
        $this->assertTrue($result['required']);
        $this->assertEquals(100, $result['maxInput']);
        $this->assertEquals('测试字段', $result['title']);
        $this->assertEquals('请输入内容', $result['placeholder']);
        $this->assertEquals('/images/bg.jpg', $result['bgImage']);
        $this->assertEquals('这是一个测试字段', $result['description']);
        $this->assertEquals(123, $result['id']);
        $this->assertTrue($result['valid']);
        $this->assertEquals('{"custom":"value"}', $result['extra']);
        $this->assertEquals(['custom' => 'value'], $result['extraConfig']);

        // 验证选项数组
        $this->assertIsArray($result['options']);
        $this->assertCount(1, $result['options']);
        $this->assertIsArray($result['options'][0]);
        $this->assertEquals('选项1', $result['options'][0]['title']);
    }

    public function testRetrieveApiArray返回正确的API数组结构(): void
    {
        $field = new Field();
        // 设置字段属性
        $field->setSn('F001');
        $field->setType(FieldType::SINGLE_SELECT);
        $field->setRequired(false);
        $field->setMaxInput(1);
        $field->setTitle('选择字段');
        $field->setPlaceholder('请选择');
        $field->setBgImage('/images/select-bg.jpg');
        $field->setDescription('这是一个选择字段');
        $field->setValid(true);
        $field->setExtra('{"multiple":false}');

        // 设置ID（使用反射）
        $reflectionClass = new \ReflectionClass(Field::class);
        $property = $reflectionClass->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($field, 456);

        // 创建两个模拟的选项
        $option1 = $this->createMock(Option::class);
        $option1->expects($this->once())
            ->method('retrieveApiArray')
            ->willReturn([
                'id' => 1,
                'title' => 'API选项1',
                'value' => 'api_opt1',
            ])
        ;
        $option1->expects($this->once())
            ->method('setField')
        ;

        $option2 = $this->createMock(Option::class);
        $option2->expects($this->once())
            ->method('retrieveApiArray')
            ->willReturn([
                'id' => 2,
                'title' => 'API选项2',
                'value' => 'api_opt2',
            ])
        ;
        $option2->expects($this->once())
            ->method('setField')
        ;

        $field->addOption($option1);
        $field->addOption($option2);

        // 获取结果
        $result = $field->retrieveApiArray();

        // 验证数组结构（API数组应该与Plain数组有相同的结构）
        $this->assertArrayHasKey('sn', $result);
        $this->assertArrayHasKey('type', $result);
        $this->assertArrayHasKey('required', $result);
        $this->assertArrayHasKey('maxInput', $result);
        $this->assertArrayHasKey('title', $result);
        $this->assertArrayHasKey('placeholder', $result);
        $this->assertArrayHasKey('bgImage', $result);
        $this->assertArrayHasKey('options', $result);
        $this->assertArrayHasKey('description', $result);
        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('createTime', $result);
        $this->assertArrayHasKey('updateTime', $result);
        $this->assertArrayHasKey('valid', $result);
        $this->assertArrayHasKey('extraConfig', $result);
        $this->assertArrayHasKey('extra', $result);

        // 验证值
        $this->assertEquals('F001', $result['sn']);
        $this->assertEquals(FieldType::SINGLE_SELECT, $result['type']);
        $this->assertFalse($result['required']);
        $this->assertEquals(1, $result['maxInput']);
        $this->assertEquals('选择字段', $result['title']);
        $this->assertEquals('请选择', $result['placeholder']);
        $this->assertEquals('/images/select-bg.jpg', $result['bgImage']);
        $this->assertEquals('这是一个选择字段', $result['description']);
        $this->assertEquals(456, $result['id']);
        $this->assertTrue($result['valid']);
        $this->assertEquals('{"multiple":false}', $result['extra']);
        $this->assertEquals(['multiple' => false], $result['extraConfig']);

        // 验证选项数组
        $this->assertIsArray($result['options']);
        $this->assertCount(2, $result['options']);
        $this->assertIsArray($result['options'][0]);
        $this->assertIsArray($result['options'][1]);
        $this->assertEquals('API选项1', $result['options'][0]['title']);
        $this->assertEquals('api_opt1', $result['options'][0]['value']);
        $this->assertEquals('API选项2', $result['options'][1]['title']);
        $this->assertEquals('api_opt2', $result['options'][1]['value']);
    }
}
