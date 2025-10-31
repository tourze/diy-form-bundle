<?php

declare(strict_types=1);

namespace DiyFormBundle\Tests\Entity;

use DiyFormBundle\Entity\Analyse;
use DiyFormBundle\Entity\Form;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(Analyse::class)]
final class AnalyseTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new Analyse();
    }

    /**
     * @return iterable<array{0: string, 1: mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        yield 'remark' => ['remark', '测试备注'];
        yield 'sortNumber' => ['sortNumber', 10];
        yield 'title' => ['title', '测试分析规则'];
        yield 'valid' => ['valid', true];
        yield 'category' => ['category', '测试分类'];
    }

    public function testForm关联(): void
    {
        $analyse = new Analyse();
        $form = new Form();
        $analyse->setForm($form);

        $this->assertSame($form, $analyse->getForm());
    }

    public function testToString返回标题(): void
    {
        $analyse = new Analyse();
        $analyse->setTitle('测试分析');
        $analyse->setCategory('测试分类');

        // 使用反射设置私有属性id
        $reflectionClass = new \ReflectionClass(Analyse::class);
        $property = $reflectionClass->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($analyse, 123);

        $this->assertEquals('#123[测试分类] 测试分析', (string) $analyse);
    }

    public function testToString无标题时返回空字符串(): void
    {
        $analyse = new Analyse();
        $this->assertEquals('', (string) $analyse);
    }
}
