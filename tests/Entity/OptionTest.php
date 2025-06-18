<?php

namespace DiyFormBundle\Tests\Entity;

use DiyFormBundle\Entity\Field;
use DiyFormBundle\Entity\Option;
use DiyFormBundle\Enum\FieldType;
use PHPUnit\Framework\TestCase;

class OptionTest extends TestCase
{
    private Option $option;

    protected function setUp(): void
    {
        $this->option = new Option();
    }

    public function testId_初始值为空()
    {
        $this->assertNull($this->option->getId());
    }

    public function testCreateTime_可以设置和获取()
    {
        $now = new \DateTime();
        $this->option->setCreateTime($now);
        $this->assertSame($now, $this->option->getCreateTime());
    }

    public function testUpdateTime_可以设置和获取()
    {
        $now = new \DateTime();
        $this->option->setUpdateTime($now);
        $this->assertSame($now, $this->option->getUpdateTime());
    }

    public function testCreatedBy_可以设置和获取()
    {
        $createdBy = 'test-user';
        $result = $this->option->setCreatedBy($createdBy);
        
        $this->assertSame($this->option, $result);
        $this->assertEquals($createdBy, $this->option->getCreatedBy());
    }

    public function testUpdatedBy_可以设置和获取()
    {
        $updatedBy = 'test-user';
        $result = $this->option->setUpdatedBy($updatedBy);
        
        $this->assertSame($this->option, $result);
        $this->assertEquals($updatedBy, $this->option->getUpdatedBy());
    }

    public function testField_可以设置和获取()
    {
        $field = $this->createMock(Field::class);
        $result = $this->option->setField($field);
        
        $this->assertSame($this->option, $result);
        $this->assertSame($field, $this->option->getField());
    }

    public function testSn_可以设置和获取()
    {
        $sn = 'test-sn-123';
        $result = $this->option->setSn($sn);
        
        $this->assertSame($this->option, $result);
        $this->assertEquals($sn, $this->option->getSn());
    }

    public function testText_可以设置和获取()
    {
        $text = '选项文本';
        $result = $this->option->setText($text);
        
        $this->assertSame($this->option, $result);
        $this->assertEquals($text, $this->option->getText());
    }

    public function testDescription_可以设置和获取()
    {
        $description = '选项说明';
        $result = $this->option->setDescription($description);
        
        $this->assertSame($this->option, $result);
        $this->assertEquals($description, $this->option->getDescription());
    }

    public function testTags_可以设置和获取()
    {
        $tags = 'tag1,tag2,tag3';
        $result = $this->option->setTags($tags);
        
        $this->assertSame($this->option, $result);
        $this->assertEquals($tags, $this->option->getTags());
    }

    public function testTagList_返回标签数组()
    {
        $tags = 'tag1,tag2,tag3';
        $this->option->setTags($tags);
        
        $tagList = $this->option->getTagList();
        $this->assertEquals(['tag1', 'tag2', 'tag3'], $tagList);
    }

    public function testTagList_空标签返回空数组()
    {
        $this->option->setTags(null);
        $this->assertEquals([], $this->option->getTagList());
        
        $this->option->setTags('');
        $this->assertEquals([], $this->option->getTagList());
    }

    public function testShowExpression_可以设置和获取()
    {
        $expression = 'form.field1 == "value"';
        $result = $this->option->setShowExpression($expression);
        
        $this->assertSame($this->option, $result);
        $this->assertEquals($expression, $this->option->getShowExpression());
    }

    public function testMutex_可以设置和获取()
    {
        $mutex = 'group1';
        $result = $this->option->setMutex($mutex);
        
        $this->assertSame($this->option, $result);
        $this->assertEquals($mutex, $this->option->getMutex());
    }

    public function testAllowInput_可以设置和获取()
    {
        $this->assertFalse($this->option->isAllowInput());
        
        $result = $this->option->setAllowInput(true);
        
        $this->assertSame($this->option, $result);
        $this->assertTrue($this->option->isAllowInput());
    }

    public function testAnswer_可以设置和获取()
    {
        $this->assertFalse($this->option->isAnswer());
        
        $result = $this->option->setAnswer(true);
        
        $this->assertSame($this->option, $result);
        $this->assertTrue($this->option->isAnswer());
    }

    public function testIcon_可以设置和获取()
    {
        $icon = 'path/to/icon.png';
        $result = $this->option->setIcon($icon);
        
        $this->assertSame($this->option, $result);
        $this->assertEquals($icon, $this->option->getIcon());
    }

    public function testSelectedIcon_可以设置和获取()
    {
        $selectedIcon = 'path/to/selected-icon.png';
        $result = $this->option->setSelectedIcon($selectedIcon);
        
        $this->assertSame($this->option, $result);
        $this->assertEquals($selectedIcon, $this->option->getSelectedIcon());
    }

    public function testCreatedFromIp_可以设置和获取()
    {
        $ip = '127.0.0.1';
        $result = $this->option->setCreatedFromIp($ip);
        
        $this->assertSame($this->option, $result);
        $this->assertEquals($ip, $this->option->getCreatedFromIp());
    }

    public function testUpdatedFromIp_可以设置和获取()
    {
        $ip = '127.0.0.1';
        $result = $this->option->setUpdatedFromIp($ip);
        
        $this->assertSame($this->option, $result);
        $this->assertEquals($ip, $this->option->getUpdatedFromIp());
    }

    public function testToString_无ID时返回空字符串()
    {
        $this->assertEquals('', (string)$this->option);
    }

    public function testToString_单选类型选项格式正确()
    {
        // 使用反射设置私有属性id
        $reflectionClass = new \ReflectionClass(Option::class);
        $property = $reflectionClass->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($this->option, '123456789');

        $this->option->setText('单选项');
        
        $field = $this->createMock(Field::class);
        $field->method('getType')->willReturn(FieldType::SINGLE_SELECT);
        $this->option->setField($field);
        
        $this->assertEquals('○单选项', (string)$this->option);
    }

    public function testToString_多选类型选项格式正确()
    {
        // 使用反射设置私有属性id
        $reflectionClass = new \ReflectionClass(Option::class);
        $property = $reflectionClass->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($this->option, '123456789');

        $this->option->setText('多选项');
        
        $field = $this->createMock(Field::class);
        $field->method('getType')->willReturn(FieldType::MULTIPLE_SELECT);
        $this->option->setField($field);
        
        $this->assertEquals('□多选项', (string)$this->option);
    }

    public function testToString_带标签选项格式正确()
    {
        // 使用反射设置私有属性id
        $reflectionClass = new \ReflectionClass(Option::class);
        $property = $reflectionClass->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($this->option, '123456789');

        $this->option->setText('测试选项');
        $this->option->setTags('重要,特殊');
        
        $this->assertEquals('[重要,特殊]测试选项', (string)$this->option);
    }

    public function testToString_带显示规则选项格式正确()
    {
        // 使用反射设置私有属性id
        $reflectionClass = new \ReflectionClass(Option::class);
        $property = $reflectionClass->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($this->option, '123456789');

        $this->option->setText('条件选项');
        $this->option->setShowExpression('form.age > 18');
        
        $this->assertEquals('条件选项。显示规则：form.age > 18', (string)$this->option);
    }

    public function testRetrievePlainArray_返回正确的数组结构()
    {
        $this->option->setText('测试选项');
        $this->option->setDescription('测试描述');
        $this->option->setTags('tag1,tag2');
        $this->option->setSn('test-123');
        
        $array = $this->option->retrievePlainArray();
        $this->assertArrayHasKey('text', $array);
        $this->assertArrayHasKey('description', $array);
        $this->assertArrayHasKey('tags', $array);
        $this->assertArrayHasKey('sn', $array);
        $this->assertEquals('测试选项', $array['text']);
        $this->assertEquals('测试描述', $array['description']);
        $this->assertEquals('tag1,tag2', $array['tags']);
        $this->assertEquals('test-123', $array['sn']);
    }

    public function testRetrieveApiArray_返回正确的API数组结构()
    {
        $this->option->setText('API选项');
        $this->option->setDescription('API描述');
        $this->option->setSn('api-123');
        $this->option->setAllowInput(true);
        $this->option->setAnswer(true);
        
        $array = $this->option->retrieveApiArray();
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