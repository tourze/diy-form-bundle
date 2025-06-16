<?php

namespace DiyFormBundle\Tests\Entity;

use DiyFormBundle\Entity\Data;
use DiyFormBundle\Entity\Field;
use DiyFormBundle\Entity\Form;
use DiyFormBundle\Entity\Record;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\User\UserInterface;

class RecordTest extends TestCase
{
    private Record $record;

    protected function setUp(): void
    {
        $this->record = new Record();
    }

    public function testId_初始值为0()
    {
        $this->assertEquals(0, $this->record->getId());
    }

    public function testDatas_初始化为空集合()
    {
        $datas = $this->record->getDatas();
        
        $this->assertInstanceOf(ArrayCollection::class, $datas);
        $this->assertTrue($datas->isEmpty());
    }

    public function testForm_可以设置和获取()
    {
        $form = $this->createMock(Form::class);
        $result = $this->record->setForm($form);
        
        $this->assertSame($this->record, $result);
        $this->assertSame($form, $this->record->getForm());
    }

    public function testAddData_添加数据并建立双向关系()
    {
        $data = $this->createMock(Data::class);
        $data->expects($this->once())
            ->method('setRecord')
            ->with($this->identicalTo($this->record))
            ->willReturn($data);
        
        $result = $this->record->addData($data);
        
        $this->assertSame($this->record, $result);
        $this->assertTrue($this->record->getDatas()->contains($data));
    }

    public function testRemoveData_移除数据并解除双向关系()
    {
        // 创建模拟对象
        $data = $this->createMock(Data::class);
        
        // 首先添加数据
        $this->record->addData($data);
        
        // 设置期望
        $data->expects($this->once())
            ->method('getRecord')
            ->willReturn($this->record);
        
        $data->expects($this->once())
            ->method('setRecord')
            ->with(null)
            ->willReturn($data);
        
        // 移除数据
        $result = $this->record->removeData($data);
        
        $this->assertSame($this->record, $result);
        $this->assertFalse($this->record->getDatas()->contains($data));
    }

    public function testUser_可以设置和获取()
    {
        $user = $this->createMock(UserInterface::class);
        $result = $this->record->setUser($user);
        
        $this->assertSame($this->record, $result);
        $this->assertSame($user, $this->record->getUser());
    }

    public function testFinished_可以设置和获取()
    {
        $this->assertNull($this->record->isFinished());
        
        $result = $this->record->setFinished(true);
        
        $this->assertSame($this->record, $result);
        $this->assertTrue($this->record->isFinished());
    }

    public function testStartTime_可以设置和获取()
    {
        $startTime = new \DateTime();
        $result = $this->record->setStartTime($startTime);
        
        $this->assertSame($this->record, $result);
        $this->assertSame($startTime, $this->record->getStartTime());
    }

    public function testFinishTime_可以设置和获取()
    {
        $finishTime = new \DateTime();
        $result = $this->record->setFinishTime($finishTime);
        
        $this->assertSame($this->record, $result);
        $this->assertSame($finishTime, $this->record->getFinishTime());
    }

    public function testAnswerTags_可以设置和获取()
    {
        $tags = ['tag1' => true, 'tag2' => false];
        $result = $this->record->setAnswerTags($tags);
        
        $this->assertSame($this->record, $result);
        $this->assertEquals($tags, $this->record->getAnswerTags());
    }

    public function testSubmitData_可以设置和获取()
    {
        $data = ['field1' => 'value1', 'field2' => 'value2'];
        $result = $this->record->setSubmitData($data);
        
        $this->assertSame($this->record, $result);
        $this->assertEquals($data, $this->record->getSubmitData());
    }

    public function testInviter_可以设置和获取()
    {
        $inviter = $this->createMock(UserInterface::class);
        $result = $this->record->setInviter($inviter);
        
        $this->assertSame($this->record, $result);
        $this->assertSame($inviter, $this->record->getInviter());
    }

    public function testLockVersion_可以设置和获取()
    {
        $version = 5;
        $result = $this->record->setLockVersion($version);
        
        $this->assertSame($this->record, $result);
        $this->assertEquals($version, $this->record->getLockVersion());
    }

    public function testExtraData_可以设置和获取()
    {
        $data = ['extra1' => 'value1', 'extra2' => 'value2'];
        $result = $this->record->setExtraData($data);
        
        $this->assertSame($this->record, $result);
        $this->assertEquals($data, $this->record->getExtraData());
    }

    public function testCreatedBy_可以设置和获取()
    {
        $createdBy = 'test-user';
        $result = $this->record->setCreatedBy($createdBy);
        
        $this->assertSame($this->record, $result);
        $this->assertEquals($createdBy, $this->record->getCreatedBy());
    }

    public function testUpdatedBy_可以设置和获取()
    {
        $updatedBy = 'test-user';
        $result = $this->record->setUpdatedBy($updatedBy);
        
        $this->assertSame($this->record, $result);
        $this->assertEquals($updatedBy, $this->record->getUpdatedBy());
    }

    public function testCreatedFromUa_可以设置和获取()
    {
        $ua = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36';
        $this->record->setCreatedFromUa($ua);
        $this->assertEquals($ua, $this->record->getCreatedFromUa());
    }

    public function testUpdatedFromUa_可以设置和获取()
    {
        $ua = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36';
        $this->record->setUpdatedFromUa($ua);
        $this->assertEquals($ua, $this->record->getUpdatedFromUa());
    }

    public function testCreateTime_可以设置和获取()
    {
        $now = new \DateTime();
        $this->record->setCreateTime($now);
        $this->assertSame($now, $this->record->getCreateTime());
    }

    public function testUpdateTime_可以设置和获取()
    {
        $now = new \DateTime();
        $this->record->setUpdateTime($now);
        $this->assertSame($now, $this->record->getUpdateTime());
    }

    public function testCreatedFromIp_可以设置和获取()
    {
        $ip = '127.0.0.1';
        $result = $this->record->setCreatedFromIp($ip);
        
        $this->assertSame($this->record, $result);
        $this->assertEquals($ip, $this->record->getCreatedFromIp());
    }

    public function testUpdatedFromIp_可以设置和获取()
    {
        $ip = '127.0.0.1';
        $result = $this->record->setUpdatedFromIp($ip);
        
        $this->assertSame($this->record, $result);
        $this->assertEquals($ip, $this->record->getUpdatedFromIp());
    }

    public function testGetDataList_返回有效的数据列表()
    {
        // 准备模拟数据
        $field1 = $this->createMock(Field::class);
        $field1->expects($this->any())
              ->method('isValid')
              ->willReturn(true);
        $field1->expects($this->any())
              ->method('getSn')
              ->willReturn('field-sn-1');
        
        $field2 = $this->createMock(Field::class);
        $field2->expects($this->any())
              ->method('isValid')
              ->willReturn(false); // 无效字段，不应包含在结果中
        
        $field3 = $this->createMock(Field::class);
        $field3->expects($this->any())
              ->method('isValid')
              ->willReturn(true);
        $field3->expects($this->any())
              ->method('getSn')
              ->willReturn('field-sn-3');
        
        $data1 = $this->createMock(Data::class);
        $data1->expects($this->any())
              ->method('getField')
              ->willReturn($field1);
        
        $data2 = $this->createMock(Data::class);
        $data2->expects($this->any())
              ->method('getField')
              ->willReturn($field2);
        
        $data3 = $this->createMock(Data::class);
        $data3->expects($this->any())
              ->method('getField')
              ->willReturn($field3);
        
        $data4 = $this->createMock(Data::class);
        $data4->expects($this->any())
              ->method('getField')
              ->willReturn(null); // 没有字段的数据，不应包含在结果中
        
        // 添加数据到record
        $this->record->addData($data1);
        $this->record->addData($data2);
        $this->record->addData($data3);
        $this->record->addData($data4);
        
        // 测试方法
        $result = $this->record->getDataList();
        
        // 验证结果
        $this->assertCount(2, $result); // 只有2个有效的数据
        $this->assertArrayHasKey('field-sn-1', $result);
        $this->assertArrayHasKey('field-sn-3', $result);
        $this->assertSame($data1, $result['field-sn-1']);
        $this->assertSame($data3, $result['field-sn-3']);
    }

    public function testCheckHasAnswered_返回是否已回答()
    {
        // 准备模拟数据
        $field1 = $this->createMock(Field::class);
        $field1->expects($this->any())
              ->method('getId')
              ->willReturn(1);
        
        $field2 = $this->createMock(Field::class);
        $field2->expects($this->any())
              ->method('getId')
              ->willReturn(2);
        
        $field3 = $this->createMock(Field::class);
        $field3->expects($this->any())
              ->method('getId')
              ->willReturn(3);
        
        $data1 = $this->createMock(Data::class);
        $data1->expects($this->any())
              ->method('getField')
              ->willReturn($field1);
        
        $data2 = $this->createMock(Data::class);
        $data2->expects($this->any())
              ->method('getField')
              ->willReturn($field2);
        
        // 添加数据到record
        $this->record->addData($data1);
        $this->record->addData($data2);
        
        // 测试已回答的字段
        $this->assertTrue($this->record->checkHasAnswered($field1));
        $this->assertTrue($this->record->checkHasAnswered($field2));
        
        // 测试未回答的字段
        $this->assertFalse($this->record->checkHasAnswered($field3));
    }

    public function testObtainDataBySN_根据SN获取Data()
    {
        // 准备模拟数据
        $field1 = $this->createMock(Field::class);
        $field1->expects($this->any())
              ->method('getSn')
              ->willReturn('field-sn-1');
        
        $field2 = $this->createMock(Field::class);
        $field2->expects($this->any())
              ->method('getSn')
              ->willReturn('field-sn-2');
        
        $data1 = $this->createMock(Data::class);
        $data1->expects($this->any())
              ->method('getField')
              ->willReturn($field1);
        
        $data2 = $this->createMock(Data::class);
        $data2->expects($this->any())
              ->method('getField')
              ->willReturn($field2);
        
        $data3 = $this->createMock(Data::class);
        $data3->expects($this->any())
              ->method('getField')
              ->willReturn(null); // 没有字段的数据
        
        // 添加数据到record
        $this->record->addData($data1);
        $this->record->addData($data2);
        $this->record->addData($data3);
        
        // 测试找到的情况
        $result1 = $this->record->obtainDataBySN('field-sn-1');
        $this->assertSame($data1, $result1);
        
        $result2 = $this->record->obtainDataBySN('field-sn-2');
        $this->assertSame($data2, $result2);
        
        // 测试找不到的情况
        $result3 = $this->record->obtainDataBySN('field-sn-not-exist');
        $this->assertNull($result3);
    }
} 