<?php

declare(strict_types=1);

namespace DiyFormBundle\Tests\Entity;

use DiyFormBundle\Entity\Data;
use DiyFormBundle\Entity\Field;
use DiyFormBundle\Entity\Record;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(Record::class)]
final class RecordTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new Record();
    }

    /**
     * @return iterable<array{0: string, 1: mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        yield 'finished' => ['finished', true];
        yield 'startTime' => ['startTime', new \DateTimeImmutable()];
        yield 'finishTime' => ['finishTime', new \DateTimeImmutable()];
        yield 'answerTags' => ['answerTags', ['tag1' => true, 'tag2' => false]];
        yield 'submitData' => ['submitData', ['field1' => 'value1', 'field2' => 'value2']];
        yield 'lockVersion' => ['lockVersion', 5];
        yield 'extraData' => ['extraData', ['extra1' => 'value1', 'extra2' => 'value2']];
        yield 'createdFromUa' => ['createdFromUa', 'Mozilla/5.0'];
        yield 'updatedFromUa' => ['updatedFromUa', 'Mozilla/5.0'];
    }

    public function testDatas初始化为空集合(): void
    {
        $record = new Record();
        $datas = $record->getDatas();

        $this->assertInstanceOf(ArrayCollection::class, $datas);
        $this->assertTrue($datas->isEmpty());
    }

    public function testAddData添加数据并建立双向关系(): void
    {
        $record = new Record();
        $data = $this->createMock(Data::class);
        $data->expects($this->once())
            ->method('setRecord')
            ->with(self::callback(fn ($arg) => $arg === $record))
        ;

        $record->addData($data);
        $this->assertTrue($record->getDatas()->contains($data));
    }

    public function testRemoveData移除数据并解除双向关系(): void
    {
        $record = new Record();
        $data = $this->createMock(Data::class);

        // 首先添加数据
        $record->addData($data);

        // 设置期望
        $data->expects($this->once())
            ->method('getRecord')
            ->willReturn($record)
        ;

        $data->expects($this->once())
            ->method('setRecord')
            ->with(null)
        ;

        // 移除数据
        $record->removeData($data);
        $this->assertFalse($record->getDatas()->contains($data));
    }

    public function testToString无ID时返回空字符串(): void
    {
        $record = new Record();
        $this->assertEquals('', (string) $record);
    }

    public function testToString有ID时返回完整描述(): void
    {
        $record = new Record();
        // 使用反射设置私有属性id
        $reflectionClass = new \ReflectionClass(Record::class);
        $property = $reflectionClass->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($record, 123);

        $expected = '记录#123 - 未知表单 - 匿名用户 - 未完成';
        $this->assertEquals($expected, (string) $record);
    }

    public function testGetDataList返回有效的数据列表(): void
    {
        $record = new Record();

        // 准备模拟数据
        $field1 = $this->createMock(Field::class);
        $field1->expects($this->any())
            ->method('isValid')
            ->willReturn(true)
        ;
        $field1->expects($this->any())
            ->method('getSn')
            ->willReturn('field-sn-1')
        ;

        $field2 = $this->createMock(Field::class);
        $field2->expects($this->any())
            ->method('isValid')
            ->willReturn(false) // 无效字段，不应包含在结果中
        ;

        $field3 = $this->createMock(Field::class);
        $field3->expects($this->any())
            ->method('isValid')
            ->willReturn(true)
        ;
        $field3->expects($this->any())
            ->method('getSn')
            ->willReturn('field-sn-3')
        ;

        $data1 = $this->createMock(Data::class);
        $data1->expects($this->any())
            ->method('getField')
            ->willReturn($field1)
        ;

        $data2 = $this->createMock(Data::class);
        $data2->expects($this->any())
            ->method('getField')
            ->willReturn($field2)
        ;

        $data3 = $this->createMock(Data::class);
        $data3->expects($this->any())
            ->method('getField')
            ->willReturn($field3)
        ;

        $data4 = $this->createMock(Data::class);
        $data4->expects($this->any())
            ->method('getField')
            ->willReturn(null) // 没有字段的数据，不应包含在结果中
        ;

        // 添加数据到record
        $record->addData($data1);
        $record->addData($data2);
        $record->addData($data3);
        $record->addData($data4);

        // 测试方法
        $result = $record->getDataList();

        // 验证结果
        $this->assertCount(2, $result); // 只有2个有效的数据
        $this->assertArrayHasKey('field-sn-1', $result);
        $this->assertArrayHasKey('field-sn-3', $result);
        $this->assertSame($data1, $result['field-sn-1']);
        $this->assertSame($data3, $result['field-sn-3']);
    }

    public function testCheckHasAnswered返回是否已回答(): void
    {
        $record = new Record();

        // 准备模拟数据
        $field1 = $this->createMock(Field::class);
        $field1->expects($this->any())
            ->method('getId')
            ->willReturn(1)
        ;

        $field2 = $this->createMock(Field::class);
        $field2->expects($this->any())
            ->method('getId')
            ->willReturn(2)
        ;

        $field3 = $this->createMock(Field::class);
        $field3->expects($this->any())
            ->method('getId')
            ->willReturn(3)
        ;

        $data1 = $this->createMock(Data::class);
        $data1->expects($this->any())
            ->method('getField')
            ->willReturn($field1)
        ;

        $data2 = $this->createMock(Data::class);
        $data2->expects($this->any())
            ->method('getField')
            ->willReturn($field2)
        ;

        // 添加数据到record
        $record->addData($data1);
        $record->addData($data2);

        // 测试已回答的字段
        $this->assertTrue($record->checkHasAnswered($field1));
        $this->assertTrue($record->checkHasAnswered($field2));

        // 测试未回答的字段
        $this->assertFalse($record->checkHasAnswered($field3));
    }

    public function testObtainDataBySN根据SN获取Data(): void
    {
        $record = new Record();

        // 准备模拟数据
        $field1 = $this->createMock(Field::class);
        $field1->expects($this->any())
            ->method('getSn')
            ->willReturn('field-sn-1')
        ;

        $field2 = $this->createMock(Field::class);
        $field2->expects($this->any())
            ->method('getSn')
            ->willReturn('field-sn-2')
        ;

        $data1 = $this->createMock(Data::class);
        $data1->expects($this->any())
            ->method('getField')
            ->willReturn($field1)
        ;

        $data2 = $this->createMock(Data::class);
        $data2->expects($this->any())
            ->method('getField')
            ->willReturn($field2)
        ;

        $data3 = $this->createMock(Data::class);
        $data3->expects($this->any())
            ->method('getField')
            ->willReturn(null) // 没有字段的数据
        ;

        // 添加数据到record
        $record->addData($data1);
        $record->addData($data2);
        $record->addData($data3);

        // 测试找到的情况
        $result1 = $record->obtainDataBySN('field-sn-1');
        $this->assertSame($data1, $result1);

        $result2 = $record->obtainDataBySN('field-sn-2');
        $this->assertSame($data2, $result2);

        // 测试找不到的情况
        $result3 = $record->obtainDataBySN('field-sn-not-exist');
        $this->assertNull($result3);
    }
}
