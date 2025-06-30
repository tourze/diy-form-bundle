<?php

namespace DiyFormBundle\Tests\Entity;

use DiyFormBundle\Entity\Data;
use DiyFormBundle\Entity\Field;
use DiyFormBundle\Entity\Record;
use PHPUnit\Framework\TestCase;

class DataTest extends TestCase
{
    private Data $data;

    protected function setUp(): void
    {
        $this->data = new Data();
    }

    public function testId_初始值为null()
    {
        $this->assertNull($this->data->getId());
    }

    public function testInput_可以设置和获取()
    {
        $this->data->setInput('测试输入');
        $this->assertEquals('测试输入', $this->data->getInput());
    }

    public function testField_可以设置和获取()
    {
        $field = new Field();
        $this->data->setField($field);
        $this->assertSame($field, $this->data->getField());
    }

    public function testRecord_可以设置和获取()
    {
        $record = new Record();
        $this->data->setRecord($record);
        $this->assertSame($record, $this->data->getRecord());
    }

    public function test__toString_返回输入内容()
    {
        $this->data->setId('123');
        $field = new Field();
        $field->setTitle('测试字段');
        $this->data->setField($field);
        $this->data->setInput('测试数据');
        $this->assertEquals('测试字段: 测试数据', (string) $this->data);
    }

    public function test__toString_ID为null时返回空字符串()
    {
        $this->data->setInput('测试数据');
        $this->assertEquals('', (string) $this->data);
    }

}