<?php

namespace DiyFormBundle\Tests\Entity;

use DiyFormBundle\Entity\Analyse;
use DiyFormBundle\Entity\Form;
use PHPUnit\Framework\TestCase;

class AnalyseTest extends TestCase
{
    private Analyse $analyse;

    protected function setUp(): void
    {
        $this->analyse = new Analyse();
    }

    public function testId_初始值为null()
    {
        $this->assertNull($this->analyse->getId());
    }

    public function testRemark_可以设置和获取()
    {
        $this->analyse->setRemark('测试备注');
        $this->assertEquals('测试备注', $this->analyse->getRemark());
    }

    public function testSortNumber_初始值为0()
    {
        $this->assertEquals(0, $this->analyse->getSortNumber());
    }

    public function testSortNumber_可以设置和获取()
    {
        $this->analyse->setSortNumber(10);
        $this->assertEquals(10, $this->analyse->getSortNumber());
    }

    public function testTitle_可以设置和获取()
    {
        $this->analyse->setTitle('测试分析规则');
        $this->assertEquals('测试分析规则', $this->analyse->getTitle());
    }

    public function testValid_默认值为false()
    {
        $this->assertFalse($this->analyse->isValid());
    }

    public function testValid_可以设置和获取()
    {
        $this->analyse->setValid(false);
        $this->assertFalse($this->analyse->isValid());
    }

    public function testForm_可以设置和获取()
    {
        $form = new Form();
        $this->analyse->setForm($form);
        $this->assertSame($form, $this->analyse->getForm());
    }

    public function test__toString_返回标题()
    {
        $this->analyse->setId('123');
        $this->analyse->setTitle('测试分析');
        $this->analyse->setCategory('测试分类');
        $this->assertEquals('#123[测试分类] 测试分析', (string) $this->analyse);
    }

    public function test__toString_ID为null时返回空字符串()
    {
        $this->analyse->setTitle('测试分析');
        $this->assertEquals('', (string) $this->analyse);
    }

}