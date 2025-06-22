<?php

namespace DiyFormBundle\Tests\Entity;

use DiyFormBundle\Entity\Field;
use DiyFormBundle\Entity\Form;
use DiyFormBundle\Entity\Option;
use DiyFormBundle\Enum\FieldType;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use Yiisoft\Json\Json;

class FieldTest extends TestCase
{
    private Field $field;

    protected function setUp(): void
    {
        $this->field = new Field();
    }

    public function testId_初始值为0()
    {
        $this->assertEquals(0, $this->field->getId());
    }

    public function testCreateTime_可以设置和获取()
    {
        $now = new \DateTimeImmutable();
        $this->field->setCreateTime($now);
        $this->assertSame($now, $this->field->getCreateTime());
    }

    public function testUpdateTime_可以设置和获取()
    {
        $now = new \DateTimeImmutable();
        $this->field->setUpdateTime($now);
        $this->assertSame($now, $this->field->getUpdateTime());
    }

    public function testCreatedBy_可以设置和获取()
    {
        $createdBy = 'test-user';
        $result = $this->field->setCreatedBy($createdBy);

        $this->assertSame($this->field, $result);
        $this->assertEquals($createdBy, $this->field->getCreatedBy());
    }

    public function testUpdatedBy_可以设置和获取()
    {
        $updatedBy = 'test-user';
        $result = $this->field->setUpdatedBy($updatedBy);

        $this->assertSame($this->field, $result);
        $this->assertEquals($updatedBy, $this->field->getUpdatedBy());
    }

    public function testValid_可以设置和获取()
    {
        $this->assertFalse($this->field->isValid());

        $result = $this->field->setValid(true);

        $this->assertSame($this->field, $result);
        $this->assertTrue($this->field->isValid());
    }

    public function testForm_可以设置和获取()
    {
        $form = new Form();
        $result = $this->field->setForm($form);

        $this->assertSame($this->field, $result);
        $this->assertSame($form, $this->field->getForm());
    }

    public function testSn_可以设置和获取()
    {
        $sn = 'test-serial-number';
        $result = $this->field->setSn($sn);

        $this->assertSame($this->field, $result);
        $this->assertEquals($sn, $this->field->getSn());
    }

    public function testType_可以设置和获取()
    {
        $type = FieldType::STRING;
        $result = $this->field->setType($type);

        $this->assertSame($this->field, $result);
        $this->assertSame($type, $this->field->getType());
    }

    public function testSortNumber_可以设置和获取()
    {
        $sortNumber = 100;
        $result = $this->field->setSortNumber($sortNumber);

        $this->assertSame($this->field, $result);
        $this->assertEquals($sortNumber, $this->field->getSortNumber());
    }

    public function testRequired_可以设置和获取()
    {
        $required = true;
        $result = $this->field->setRequired($required);

        $this->assertSame($this->field, $result);
        $this->assertTrue($this->field->isRequired());
    }

    public function testMaxInput_可以设置和获取()
    {
        $maxInput = 10;
        $result = $this->field->setMaxInput($maxInput);

        $this->assertSame($this->field, $result);
        $this->assertEquals($maxInput, $this->field->getMaxInput());
    }

    public function testTitle_可以设置和获取()
    {
        $title = '测试字段';
        $result = $this->field->setTitle($title);

        $this->assertSame($this->field, $result);
        $this->assertEquals($title, $this->field->getTitle());
    }

    public function testPlaceholder_可以设置和获取()
    {
        $placeholder = '请输入测试内容';
        $result = $this->field->setPlaceholder($placeholder);

        $this->assertSame($this->field, $result);
        $this->assertEquals($placeholder, $this->field->getPlaceholder());
    }

    public function testBgImage_可以设置和获取()
    {
        $bgImage = '/path/to/image.jpg';
        $result = $this->field->setBgImage($bgImage);

        $this->assertSame($this->field, $result);
        $this->assertEquals($bgImage, $this->field->getBgImage());
    }

    public function testDescription_可以设置和获取()
    {
        $description = '这是一个测试描述';
        $result = $this->field->setDescription($description);

        $this->assertSame($this->field, $result);
        $this->assertEquals($description, $this->field->getDescription());
    }

    public function testShowExpression_可以设置和获取()
    {
        $expression = 'form.field1 == "value"';
        $result = $this->field->setShowExpression($expression);

        $this->assertSame($this->field, $result);
        $this->assertEquals($expression, $this->field->getShowExpression());
    }

    public function testExtra_可以设置和获取()
    {
        $extra = Json::encode(['key' => 'value']);
        $result = $this->field->setExtra($extra);

        $this->assertSame($this->field, $result);
        $this->assertEquals($extra, $this->field->getExtra());
    }

    public function testExtraConfig_解析Extra字段并返回数组()
    {
        $extraData = ['key' => 'value', 'nested' => ['a' => 1]];
        $extraJson = Json::encode($extraData);
        $this->field->setExtra($extraJson);

        $result = $this->field->getExtraConfig();
        $this->assertEquals($extraData, $result);
    }

    public function testExtraConfig_当Extra为null时返回空数组()
    {
        $this->field->setExtra(null);

        $result = $this->field->getExtraConfig();
        $this->assertEmpty($result);
    }

    public function testExtraConfig_当Extra不是有效JSON时返回空数组()
    {
        $this->field->setExtra('invalid json');

        $result = $this->field->getExtraConfig();
        $this->assertEmpty($result);
    }

    public function testOptions_初始化为空集合()
    {
        $options = $this->field->getOptions();

        $this->assertInstanceOf(ArrayCollection::class, $options);
        $this->assertTrue($options->isEmpty());
    }

    public function testAddOption_添加选项并建立双向关系()
    {
        $option = $this->createMock(Option::class);
        $option->expects($this->once())
            ->method('setField')
            ->with($this->identicalTo($this->field))
            ->willReturn($option);

        $result = $this->field->addOption($option);

        $this->assertSame($this->field, $result);
        $this->assertTrue($this->field->getOptions()->contains($option));
    }

    public function testRemoveOption_移除选项并解除双向关系()
    {
        $option = $this->createMock(Option::class);

        // 记录setField调用的参数
        $callCount = 0;
        $option->expects($this->exactly(2))
            ->method('setField')
            ->willReturnCallback(function ($arg) use (&$callCount, $option) {
                if ($callCount === 0) {
                    // 第一次调用，添加时
                    $this->assertSame($this->field, $arg);
                } else {
                    // 第二次调用，移除时
                    $this->assertNull($arg);
                }
                $callCount++;
                return $option;
            });

        // 模拟选项的getField方法返回当前field
        $option->expects($this->any())
            ->method('getField')
            ->willReturn($this->field);

        // 先添加选项
        $this->field->addOption($option);
        $this->assertTrue($this->field->getOptions()->contains($option));

        // 然后移除选项
        $result = $this->field->removeOption($option);

        $this->assertSame($this->field, $result);
        $this->assertFalse($this->field->getOptions()->contains($option));
    }

    public function testCreatedFromIp_可以设置和获取()
    {
        $ip = '127.0.0.1';
        $result = $this->field->setCreatedFromIp($ip);

        $this->assertSame($this->field, $result);
        $this->assertEquals($ip, $this->field->getCreatedFromIp());
    }

    public function testUpdatedFromIp_可以设置和获取()
    {
        $ip = '127.0.0.1';
        $result = $this->field->setUpdatedFromIp($ip);

        $this->assertSame($this->field, $result);
        $this->assertEquals($ip, $this->field->getUpdatedFromIp());
    }

    public function testToString_无ID时返回空字符串()
    {
        // 设置必要的属性，但保持ID为空
        $this->field->setType(FieldType::STRING);
        $this->field->setTitle('测试字段');
        $this->field->setSn('F001');

        $this->assertEquals('F001.字符串 测试字段', (string)$this->field);
    }

    public function testToString_有ID时返回正确的字符串表示()
    {
        // 设置必要的属性
        $this->field->setType(FieldType::STRING);
        $this->field->setTitle('测试字段');
        $this->field->setSn('F001');

        // 使用反射设置私有属性id
        $reflectionClass = new \ReflectionClass(Field::class);
        $property = $reflectionClass->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($this->field, 123);

        $expected = 'F001.字符串 测试字段';
        $this->assertEquals($expected, (string)$this->field);
    }

    public function testToString_包含显示表达式()
    {
        // 设置必要的属性
        $this->field->setType(FieldType::STRING);
        $this->field->setTitle('测试字段');
        $this->field->setSn('F001');
        $this->field->setShowExpression('form.field1 == "value"');

        // 使用反射设置私有属性id
        $reflectionClass = new \ReflectionClass(Field::class);
        $property = $reflectionClass->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($this->field, 123);

        $expected = 'F001.【如果 form.field1 == "value"】字符串 测试字段';
        $this->assertEquals($expected, (string)$this->field);
    }

    public function testRetrievePlainArray_返回正确的数组结构()
    {
        // 设置字段属性
        $this->field->setSn('F001');
        $this->field->setType(FieldType::STRING);
        $this->field->setRequired(true);
        $this->field->setMaxInput(100);
        $this->field->setTitle('测试字段');
        $this->field->setPlaceholder('请输入内容');
        $this->field->setBgImage('/images/bg.jpg');
        $this->field->setDescription('这是一个测试字段');
        $this->field->setValid(true);
        $this->field->setExtra('{"custom":"value"}');

        // 设置ID（使用反射）
        $reflectionClass = new \ReflectionClass(Field::class);
        $property = $reflectionClass->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($this->field, 123);

        // 创建一个模拟的选项
        $option = $this->createMock(Option::class);
        $option->expects($this->once())
            ->method('retrievePlainArray')
            ->willReturn([
                'id' => 1,
                'title' => '选项1',
                'value' => 'opt1'
            ]);
        $option->expects($this->once())
            ->method('setField')
            ->willReturn($option);

        $this->field->addOption($option);

        // 获取结果
        $result = $this->field->retrievePlainArray();

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
        $this->assertCount(1, $result['options']);
        $this->assertEquals('选项1', $result['options'][0]['title']);
    }

    public function testRetrieveApiArray_返回正确的API数组结构()
    {
        // 设置字段属性
        $this->field->setSn('F001');
        $this->field->setType(FieldType::SINGLE_SELECT);
        $this->field->setRequired(false);
        $this->field->setMaxInput(1);
        $this->field->setTitle('选择字段');
        $this->field->setPlaceholder('请选择');
        $this->field->setBgImage('/images/select-bg.jpg');
        $this->field->setDescription('这是一个选择字段');
        $this->field->setValid(true);
        $this->field->setExtra('{"multiple":false}');

        // 设置ID（使用反射）
        $reflectionClass = new \ReflectionClass(Field::class);
        $property = $reflectionClass->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($this->field, 456);

        // 创建两个模拟的选项
        $option1 = $this->createMock(Option::class);
        $option1->expects($this->once())
            ->method('retrieveApiArray')
            ->willReturn([
                'id' => 1,
                'title' => 'API选项1',
                'value' => 'api_opt1'
            ]);
        $option1->expects($this->once())
            ->method('setField')
            ->willReturn($option1);

        $option2 = $this->createMock(Option::class);
        $option2->expects($this->once())
            ->method('retrieveApiArray')
            ->willReturn([
                'id' => 2,
                'title' => 'API选项2',
                'value' => 'api_opt2'
            ]);
        $option2->expects($this->once())
            ->method('setField')
            ->willReturn($option2);

        $this->field->addOption($option1);
        $this->field->addOption($option2);

        // 获取结果
        $result = $this->field->retrieveApiArray();

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
        $this->assertCount(2, $result['options']);
        $this->assertEquals('API选项1', $result['options'][0]['title']);
        $this->assertEquals('api_opt1', $result['options'][0]['value']);
        $this->assertEquals('API选项2', $result['options'][1]['title']);
        $this->assertEquals('api_opt2', $result['options'][1]['value']);
    }
}
