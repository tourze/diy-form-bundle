<?php

declare(strict_types=1);

namespace DiyFormBundle\Tests\Entity;

use DiyFormBundle\Entity\Data;
use DiyFormBundle\Entity\Field;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(Data::class)]
final class DataTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new Data();
    }

    /**
     * @return iterable<array{0: string, 1: mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        yield 'input' => ['input', '测试输入'];
        yield 'skip' => ['skip', true];
        yield 'deletable' => ['deletable', false];
        yield 'answerTags' => ['answerTags', ['tag1', 'tag2']];
    }

    public function testToString返回输入内容(): void
    {
        $data = new Data();
        $data->setId('123');
        $field = new Field();
        $field->setTitle('测试字段');
        $data->setField($field);
        $data->setInput('测试数据');
        $this->assertEquals('测试字段: 测试数据', (string) $data);
    }

    public function testToStringID为null时返回空字符串(): void
    {
        $data = new Data();
        $data->setInput('测试数据');
        $this->assertEquals('', (string) $data);
    }
}
