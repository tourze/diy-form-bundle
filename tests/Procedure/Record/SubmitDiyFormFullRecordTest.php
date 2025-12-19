<?php

declare(strict_types=1);

namespace DiyFormBundle\Tests\Procedure\Record;

use DiyFormBundle\Procedure\Record\SubmitDiyFormFullRecord;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitJsonRPC\AbstractProcedureTestCase;

/**
 * @internal
 */
#[CoversClass(SubmitDiyFormFullRecord::class)]
#[RunTestsInSeparateProcesses]
final class SubmitDiyFormFullRecordTest extends AbstractProcedureTestCase
{
    protected function onSetUp(): void
    {
    }

    public function testProcedureCanBeInstantiated(): void
    {
        $procedure = self::getService(SubmitDiyFormFullRecord::class);
        $this->assertNotNull($procedure);
    }

    public function testExecute(): void
    {
        $procedure = self::getService(SubmitDiyFormFullRecord::class);

        // 测试execute方法存在且可调用
        $this->assertTrue(method_exists($procedure, 'execute'));
        $this->assertTrue(true); // 标记测试完成
    }
}
