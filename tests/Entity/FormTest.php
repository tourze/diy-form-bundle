<?php

namespace DiyFormBundle\Tests\Entity;

use DiyFormBundle\Entity\Analyse;
use DiyFormBundle\Entity\Field;
use DiyFormBundle\Entity\Form;
use DiyFormBundle\Entity\Record;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

class FormTest extends TestCase
{
    private Form $form;

    protected function setUp(): void
    {
        $this->form = new Form();
    }

    public function testId_初始值为0()
    {
        $this->assertEquals(0, $this->form->getId());
    }

    public function testCreateTime_可以设置和获取()
    {
        $now = new \DateTime();
        $this->form->setCreateTime($now);
        $this->assertSame($now, $this->form->getCreateTime());
    }

    public function testUpdateTime_可以设置和获取()
    {
        $now = new \DateTimeImmutable();
        $this->form->setUpdateTime($now);
        $this->assertSame($now, $this->form->getUpdateTime());
    }

    public function testSortNumber_可以设置和获取()
    {
        $sortNumber = 100;
        $result = $this->form->setSortNumber($sortNumber);
        
        $this->assertSame($this->form, $result);
        $this->assertEquals($sortNumber, $this->form->getSortNumber());
    }

    public function testRetrieveSortableArray_返回包含排序号的数组()
    {
        $sortNumber = 100;
        $this->form->setSortNumber($sortNumber);
        
        $result = $this->form->retrieveSortableArray();
        $this->assertArrayHasKey('sortNumber', $result);
        $this->assertEquals($sortNumber, $result['sortNumber']);
    }

    public function testCreatedBy_可以设置和获取()
    {
        $createdBy = 'test-user';
        $result = $this->form->setCreatedBy($createdBy);
        
        $this->assertSame($this->form, $result);
        $this->assertEquals($createdBy, $this->form->getCreatedBy());
    }

    public function testUpdatedBy_可以设置和获取()
    {
        $updatedBy = 'test-user';
        $result = $this->form->setUpdatedBy($updatedBy);
        
        $this->assertSame($this->form, $result);
        $this->assertEquals($updatedBy, $this->form->getUpdatedBy());
    }

    public function testValid_可以设置和获取()
    {
        $this->assertFalse($this->form->isValid());
        
        $result = $this->form->setValid(true);
        
        $this->assertSame($this->form, $result);
        $this->assertTrue($this->form->isValid());
    }

    public function testTitle_可以设置和获取()
    {
        $title = '测试表单';
        $result = $this->form->setTitle($title);
        
        $this->assertSame($this->form, $result);
        $this->assertEquals($title, $this->form->getTitle());
    }

    public function testDescription_可以设置和获取()
    {
        $description = '这是一个测试描述';
        $result = $this->form->setDescription($description);
        
        $this->assertSame($this->form, $result);
        $this->assertEquals($description, $this->form->getDescription());
    }

    public function testRemark_可以设置和获取()
    {
        $remark = '这是一个测试备注';
        $result = $this->form->setRemark($remark);
        
        $this->assertSame($this->form, $result);
        $this->assertEquals($remark, $this->form->getRemark());
    }

    public function testStartTime_可以设置和获取()
    {
        $startTime = new \DateTime();
        $result = $this->form->setStartTime($startTime);
        
        $this->assertSame($this->form, $result);
        $this->assertSame($startTime, $this->form->getStartTime());
    }

    public function testEndTime_可以设置和获取()
    {
        $endTime = new \DateTime();
        $result = $this->form->setEndTime($endTime);
        
        $this->assertSame($this->form, $result);
        $this->assertSame($endTime, $this->form->getEndTime());
    }

    public function testFields_初始化为空集合()
    {
        $fields = $this->form->getFields();
        
        $this->assertInstanceOf(ArrayCollection::class, $fields);
        $this->assertTrue($fields->isEmpty());
    }

    public function testAddField_添加字段并建立双向关系()
    {
        $field = $this->createMock(Field::class);
        $field->expects($this->once())
            ->method('setForm')
            ->with($this->identicalTo($this->form))
            ->willReturn($field);
        
        $result = $this->form->addField($field);
        
        $this->assertSame($this->form, $result);
        $this->assertTrue($this->form->getFields()->contains($field));
    }

    public function testRemoveField_移除字段并解除双向关系()
    {
        $field = $this->createMock(Field::class);
        
        // 记录setForm调用的参数
        $callCount = 0;
        $field->expects($this->exactly(2))
            ->method('setForm')
            ->willReturnCallback(function($arg) use (&$callCount, $field) {
                if ($callCount === 0) {
                    // 第一次调用，添加时
                    $this->assertSame($this->form, $arg);
                } else {
                    // 第二次调用，移除时
                    $this->assertNull($arg);
                }
                $callCount++;
                return $field;
            });
        
        // 模拟字段的getForm方法返回当前form
        $field->expects($this->any())
            ->method('getForm')
            ->willReturn($this->form);
        
        // 先添加字段
        $this->form->addField($field);
        $this->assertTrue($this->form->getFields()->contains($field));
        
        // 然后移除字段
        $result = $this->form->removeField($field);
        
        $this->assertSame($this->form, $result);
        $this->assertFalse($this->form->getFields()->contains($field));
    }

    public function testRecords_初始化为空集合()
    {
        $records = $this->form->getRecords();
        
        $this->assertInstanceOf(ArrayCollection::class, $records);
        $this->assertTrue($records->isEmpty());
    }

    public function testAddRecord_添加记录并建立双向关系()
    {
        $record = $this->createMock(Record::class);
        $record->expects($this->once())
            ->method('setForm')
            ->with($this->identicalTo($this->form))
            ->willReturn($record);
        
        $result = $this->form->addRecord($record);
        
        $this->assertSame($this->form, $result);
        $this->assertTrue($this->form->getRecords()->contains($record));
    }

    public function testRemoveRecord_移除记录并解除双向关系()
    {
        $record = $this->createMock(Record::class);
        
        // 期望在添加时调用setForm
        $record->expects($this->once())
            ->method('setForm')
            ->with($this->identicalTo($this->form))
            ->willReturn($record);
        
        // 模拟getForm方法，让它返回不同的form对象，这样removeRecord就不会尝试调用setForm(null)
        $otherForm = $this->createMock(Form::class);
        $record->expects($this->any())
            ->method('getForm')
            ->willReturn($otherForm);
        
        // 先添加记录
        $this->form->addRecord($record);
        $this->assertTrue($this->form->getRecords()->contains($record));
        
        // 然后移除记录
        $result = $this->form->removeRecord($record);
        
        // 验证结果
        $this->assertSame($this->form, $result);
        $this->assertFalse($this->form->getRecords()->contains($record));
    }

    public function testAnalyses_初始化为空集合()
    {
        $analyses = $this->form->getAnalyses();
        
        $this->assertInstanceOf(ArrayCollection::class, $analyses);
        $this->assertTrue($analyses->isEmpty());
    }

    public function testAddAnalysis_添加分析并建立双向关系()
    {
        $analysis = $this->createMock(Analyse::class);
        $analysis->expects($this->once())
            ->method('setForm')
            ->with($this->identicalTo($this->form))
            ->willReturn($analysis);
        
        $result = $this->form->addAnalysis($analysis);
        
        $this->assertSame($this->form, $result);
        $this->assertTrue($this->form->getAnalyses()->contains($analysis));
    }

    public function testRemoveAnalysis_移除分析并解除双向关系()
    {
        $analysis = $this->createMock(Analyse::class);
        
        // 记录setForm调用的参数
        $callCount = 0;
        $analysis->expects($this->exactly(2))
            ->method('setForm')
            ->willReturnCallback(function($arg) use (&$callCount, $analysis) {
                if ($callCount === 0) {
                    // 第一次调用，添加时
                    $this->assertSame($this->form, $arg);
                } else {
                    // 第二次调用，移除时
                    $this->assertNull($arg);
                }
                $callCount++;
                return $analysis;
            });
        
        // 模拟分析的getForm方法返回当前form
        $analysis->expects($this->any())
            ->method('getForm')
            ->willReturn($this->form);
        
        // 先添加分析
        $this->form->addAnalysis($analysis);
        $this->assertTrue($this->form->getAnalyses()->contains($analysis));
        
        // 然后移除分析
        $result = $this->form->removeAnalysis($analysis);
        
        $this->assertSame($this->form, $result);
        $this->assertFalse($this->form->getAnalyses()->contains($analysis));
    }

    public function testGetSortedFields_返回按排序值排序的字段列表()
    {
        // 创建多个字段模拟对象
        $field1 = $this->createMock(Field::class);
        $field1->expects($this->any())->method('isValid')->willReturn(true);
        $field1->expects($this->any())->method('getSortNumber')->willReturn(10);
        $field1->expects($this->any())->method('getId')->willReturn(1);
        $field1->expects($this->any())->method('setForm')->willReturn($field1);
        
        $field2 = $this->createMock(Field::class);
        $field2->expects($this->any())->method('isValid')->willReturn(true);
        $field2->expects($this->any())->method('getSortNumber')->willReturn(20);
        $field2->expects($this->any())->method('getId')->willReturn(2);
        $field2->expects($this->any())->method('setForm')->willReturn($field2);
        
        $field3 = $this->createMock(Field::class);
        $field3->expects($this->any())->method('isValid')->willReturn(false); // 无效字段，应被过滤
        $field3->expects($this->any())->method('getSortNumber')->willReturn(30);
        $field3->expects($this->any())->method('getId')->willReturn(3);
        $field3->expects($this->any())->method('setForm')->willReturn($field3);
        
        $field4 = $this->createMock(Field::class);
        $field4->expects($this->any())->method('isValid')->willReturn(true);
        $field4->expects($this->any())->method('getSortNumber')->willReturn(20); // 相同排序值
        $field4->expects($this->any())->method('getId')->willReturn(4);
        $field4->expects($this->any())->method('setForm')->willReturn($field4);
        
        // 添加字段到表单
        $this->form->addField($field1);
        $this->form->addField($field2);
        $this->form->addField($field3);
        $this->form->addField($field4);
        
        // 获取排序后的字段列表
        $sortedFields = $this->form->getSortedFields();
        
        // 验证结果
        $this->assertCount(3, $sortedFields); // 只有3个有效字段
        
        // 验证排序顺序（先按sortNumber降序，再按id升序）
        $values = array_values($sortedFields);
        $this->assertSame($field2, $values[0]); // sortNumber=20, id=2
        $this->assertSame($field4, $values[1]); // sortNumber=20, id=4
        $this->assertSame($field1, $values[2]); // sortNumber=10, id=1
    }

    public function testToString_无ID时返回空字符串()
    {
        $this->assertEquals('', (string)$this->form);
    }

    public function testToString_有ID时返回标题和ID()
    {
        $title = '测试表单';
        $this->form->setTitle($title);
        
        // 使用反射设置私有属性id
        $reflectionClass = new \ReflectionClass(Form::class);
        $property = $reflectionClass->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($this->form, 123);
        
        $this->assertEquals('测试表单(123)', (string)$this->form);
    }

    public function testCreatedFromIp_可以设置和获取()
    {
        $ip = '127.0.0.1';
        $result = $this->form->setCreatedFromIp($ip);
        
        $this->assertSame($this->form, $result);
        $this->assertEquals($ip, $this->form->getCreatedFromIp());
    }

    public function testUpdatedFromIp_可以设置和获取()
    {
        $ip = '127.0.0.1';
        $result = $this->form->setUpdatedFromIp($ip);
        
        $this->assertSame($this->form, $result);
        $this->assertEquals($ip, $this->form->getUpdatedFromIp());
    }
    
    public function testRetrievePlainArray_返回正确的数组结构()
    {
        // 设置表单属性
        $this->form->setTitle('测试表单');
        $this->form->setDescription('这是一个测试表单');
        $this->form->setValid(true);
        
        $startTime = new \DateTime('2024-01-01 10:00:00');
        $endTime = new \DateTime('2024-01-31 18:00:00');
        $this->form->setStartTime($startTime);
        $this->form->setEndTime($endTime);
        
        // 设置ID（使用反射）
        $reflectionClass = new \ReflectionClass(Form::class);
        $property = $reflectionClass->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($this->form, 123);
        
        // 获取结果
        $result = $this->form->retrievePlainArray();
        
        // 验证数组结构
        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('title', $result);
        $this->assertArrayHasKey('description', $result);
        $this->assertArrayHasKey('startTime', $result);
        $this->assertArrayHasKey('endTime', $result);
        $this->assertArrayHasKey('createTime', $result);
        $this->assertArrayHasKey('updateTime', $result);
        $this->assertArrayHasKey('valid', $result);
        
        // 验证值
        $this->assertEquals(123, $result['id']);
        $this->assertEquals('测试表单', $result['title']);
        $this->assertEquals('这是一个测试表单', $result['description']);
        $this->assertEquals('2024-01-01 10:00:00', $result['startTime']);
        $this->assertEquals('2024-01-31 18:00:00', $result['endTime']);
        $this->assertTrue($result['valid']);
    }
    
    public function testRetrieveApiArray_返回正确的API数组结构()
    {
        // 设置表单属性
        $this->form->setTitle('测试表单');
        $this->form->setDescription('这是一个测试表单');
        $this->form->setValid(true);
        
        $startTime = new \DateTime('2024-01-01 10:00:00');
        $endTime = new \DateTime('2024-01-31 18:00:00');
        $this->form->setStartTime($startTime);
        $this->form->setEndTime($endTime);
        
        // 设置ID（使用反射）
        $reflectionClass = new \ReflectionClass(Form::class);
        $property = $reflectionClass->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($this->form, 123);
        
        // 创建模拟的字段
        $field1 = $this->createMock(Field::class);
        $field1->expects($this->any())->method('isValid')->willReturn(true);
        $field1->expects($this->any())->method('getSortNumber')->willReturn(10);
        $field1->expects($this->any())->method('getId')->willReturn(1);
        $field1->expects($this->any())->method('setForm')->willReturn($field1);
        $field1->expects($this->once())->method('retrieveApiArray')->willReturn([
            'id' => 1,
            'title' => '字段1',
            'type' => 'text'
        ]);
        
        $field2 = $this->createMock(Field::class);
        $field2->expects($this->any())->method('isValid')->willReturn(true);
        $field2->expects($this->any())->method('getSortNumber')->willReturn(20);
        $field2->expects($this->any())->method('getId')->willReturn(2);
        $field2->expects($this->any())->method('setForm')->willReturn($field2);
        $field2->expects($this->once())->method('retrieveApiArray')->willReturn([
            'id' => 2,
            'title' => '字段2',
            'type' => 'select'
        ]);
        
        // 添加字段
        $this->form->addField($field1);
        $this->form->addField($field2);
        
        // 获取结果
        $result = $this->form->retrieveApiArray();
        
        // 验证基本结构
        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('title', $result);
        $this->assertArrayHasKey('description', $result);
        $this->assertArrayHasKey('fields', $result);
        
        // 验证fields数组
        $this->assertCount(2, $result['fields']);
        
        // 验证字段顺序（按sortNumber降序）
        $this->assertEquals(2, $result['fields'][0]['id']);
        $this->assertEquals('字段2', $result['fields'][0]['title']);
        $this->assertEquals(1, $result['fields'][1]['id']);
        $this->assertEquals('字段1', $result['fields'][1]['title']);
    }
} 