<?php

declare(strict_types=1);

namespace DiyFormBundle\Tests\Entity;

use DiyFormBundle\Entity\SmsDsn;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(SmsDsn::class)]
final class SmsDsnTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new SmsDsn();
    }

    /**
     * @return iterable<array{0: string, 1: mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        yield 'valid' => ['valid', true];
        yield 'name' => ['name', '阿里云短信'];
        yield 'dsn' => ['dsn', 'aliyun://key:secret@default'];
        yield 'weight' => ['weight', 100];
    }

    public function testToString返回名称和状态(): void
    {
        $smsDsn = new SmsDsn();
        $smsDsn->setId('123');
        $smsDsn->setName('腾讯云短信');
        $smsDsn->setValid(true);
        $result = (string) $smsDsn;
        $this->assertStringContainsString('腾讯云短信', $result);
        $this->assertStringContainsString('有效', $result);
    }

    public function testToStringID为null时返回空字符串(): void
    {
        $smsDsn = new SmsDsn();
        $smsDsn->setName('腾讯云短信');
        $smsDsn->setValid(true);
        $this->assertEquals('', (string) $smsDsn);
    }
}
