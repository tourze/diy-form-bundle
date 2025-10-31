<?php

declare(strict_types=1);

namespace DiyFormBundle\Tests\Entity;

use DiyFormBundle\Entity\Field;
use DiyFormBundle\Entity\Option;
use DiyFormBundle\Enum\FieldType;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(Option::class)]
final class OptionTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new Option();
    }

    /**
     * @return iterable<array{0: string, 1: mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        yield 'sn' => ['sn', 'test-sn-123'];
        yield 'text' => ['text', '选项文本'];
        yield 'description' => ['description', '选项说明'];
        yield 'tags' => ['tags', 'tag1,tag2,tag3'];
        yield 'showExpression' => ['showExpression', 'form.field1 == "value"'];
        yield 'mutex' => ['mutex', 'group1'];
        yield 'allowInput' => ['allowInput', true];
        yield 'answer' => ['answer', true];
        yield 'icon' => ['icon', 'path/to/icon.png'];
        yield 'selectedIcon' => ['selectedIcon', 'path/to/selected-icon.png'];
    }

    public function testTagList返回标签数组(): void
    {
        $option = new Option();
        $option->setTags('tag1,tag2,tag3');

        $tagList = $option->getTagList();
        $this->assertEquals(['tag1', 'tag2', 'tag3'], $tagList);
    }

    public function testTagList空标签返回空数组(): void
    {
        $option = new Option();
        $option->setTags(null);
        $this->assertEquals([], $option->getTagList());

        $option->setTags('');
        $this->assertEquals([], $option->getTagList());
    }

    public function testToString无ID时返回空字符串(): void
    {
        $option = new Option();
        $this->assertEquals('', (string) $option);
    }

    public function testToString单选类型选项格式正确(): void
    {
        $option = new Option();
        // 使用反射设置私有属性id
        $reflectionClass = new \ReflectionClass(Option::class);
        $property = $reflectionClass->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($option, '123456789');

        $option->setText('单选项');

        $field = $this->createMock(Field::class);
        $field->method('getType')->willReturn(FieldType::SINGLE_SELECT);
        $option->setField($field);

        $this->assertEquals('○单选项', (string) $option);
    }

    public function testToString多选类型选项格式正确(): void
    {
        $option = new Option();
        // 使用反射设置私有属性id
        $reflectionClass = new \ReflectionClass(Option::class);
        $property = $reflectionClass->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($option, '123456789');

        $option->setText('多选项');

        $field = $this->createMock(Field::class);
        $field->method('getType')->willReturn(FieldType::MULTIPLE_SELECT);
        $option->setField($field);

        $this->assertEquals('□多选项', (string) $option);
    }

    public function testToString带标签选项格式正确(): void
    {
        $option = new Option();
        // 使用反射设置私有属性id
        $reflectionClass = new \ReflectionClass(Option::class);
        $property = $reflectionClass->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($option, '123456789');

        $option->setText('测试选项');
        $option->setTags('重要,特殊');

        $this->assertEquals('[重要,特殊]测试选项', (string) $option);
    }

    public function testToString带显示规则选项格式正确(): void
    {
        $option = new Option();
        // 使用反射设置私有属性id
        $reflectionClass = new \ReflectionClass(Option::class);
        $property = $reflectionClass->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($option, '123456789');

        $option->setText('条件选项');
        $option->setShowExpression('form.age > 18');

        $this->assertEquals('条件选项。显示规则：form.age > 18', (string) $option);
    }

    public function testRetrievePlainArray返回正确的数组结构(): void
    {
        $option = new Option();
        $option->setText('测试选项');
        $option->setDescription('测试描述');
        $option->setTags('tag1,tag2');
        $option->setSn('test-123');

        $array = $option->retrievePlainArray();
        $this->assertArrayHasKey('text', $array);
        $this->assertArrayHasKey('description', $array);
        $this->assertArrayHasKey('tags', $array);
        $this->assertArrayHasKey('sn', $array);
        $this->assertEquals('测试选项', $array['text']);
        $this->assertEquals('测试描述', $array['description']);
        $this->assertEquals('tag1,tag2', $array['tags']);
        $this->assertEquals('test-123', $array['sn']);
    }

    public function testRetrieveApiArray返回正确的API数组结构(): void
    {
        $option = new Option();
        $option->setText('API选项');
        $option->setDescription('API描述');
        $option->setSn('api-123');
        $option->setAllowInput(true);
        $option->setAnswer(true);

        $array = $option->retrieveApiArray();
        $this->assertArrayHasKey('text', $array);
        $this->assertArrayHasKey('description', $array);
        $this->assertArrayHasKey('sn', $array);
        $this->assertArrayHasKey('allowInput', $array);
        $this->assertArrayHasKey('answer', $array);
        $this->assertEquals('API选项', $array['text']);
        $this->assertEquals('API描述', $array['description']);
        $this->assertEquals('api-123', $array['sn']);
        $this->assertTrue($array['allowInput']);
        $this->assertTrue($array['answer']);
    }
}
