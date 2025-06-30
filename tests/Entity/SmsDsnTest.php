<?php

namespace DiyFormBundle\Tests\Entity;

use DiyFormBundle\Entity\SmsDsn;
use PHPUnit\Framework\TestCase;

class SmsDsnTest extends TestCase
{
    private SmsDsn $smsDsn;

    protected function setUp(): void
    {
        $this->smsDsn = new SmsDsn();
    }

    public function testId_初始值为null()
    {
        $this->assertNull($this->smsDsn->getId());
    }

    public function testName_可以设置和获取()
    {
        $this->smsDsn->setName('阿里云短信');
        $this->assertEquals('阿里云短信', $this->smsDsn->getName());
    }

    public function testDsn_可以设置和获取()
    {
        $this->smsDsn->setDsn('aliyun://key:secret@default');
        $this->assertEquals('aliyun://key:secret@default', $this->smsDsn->getDsn());
    }

    public function testValid_默认值为false()
    {
        $this->assertFalse($this->smsDsn->isValid());
    }

    public function testValid_可以设置和获取()
    {
        $this->smsDsn->setValid(true);
        $this->assertTrue($this->smsDsn->isValid());
    }

    public function testWeight_可以设置和获取()
    {
        $this->smsDsn->setWeight(100);
        $this->assertEquals(100, $this->smsDsn->getWeight());
    }

    public function test__toString_返回名称和状态()
    {
        $this->smsDsn->setId('123');
        $this->smsDsn->setName('腾讯云短信');
        $this->smsDsn->setValid(true);
        $result = (string) $this->smsDsn;
        $this->assertStringContainsString('腾讯云短信', $result);
        $this->assertStringContainsString('有效', $result);
    }

    public function test__toString_ID为null时返回空字符串()
    {
        $this->smsDsn->setName('腾讯云短信');
        $this->smsDsn->setValid(true);
        $this->assertEquals('', (string) $this->smsDsn);
    }
}