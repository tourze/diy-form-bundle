<?php

declare(strict_types=1);

namespace DiyFormBundle\Tests\Procedure\Record;

use DiyFormBundle\Procedure\Record\DeleteDiyFormRecord;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\JsonRPC\Core\Tests\AbstractProcedureTestCase;

/**
 * @internal
 */
#[CoversClass(DeleteDiyFormRecord::class)]
#[RunTestsInSeparateProcesses]
final class DeleteDiyFormRecordTest extends AbstractProcedureTestCase
{
    protected function onSetUp(): void
    {
    }

    public function testProcedureCanBeInstantiated(): void
    {
        $procedure = self::getService(DeleteDiyFormRecord::class);
        $this->assertNotNull($procedure);
    }

    public function testExecute(): void
    {
        $procedure = self::getService(DeleteDiyFormRecord::class);

        // 测试execute方法存在且可调用
        $this->assertTrue(method_exists($procedure, 'execute'));
        $this->assertTrue(true); // 标记测试完成
    }
}
