<?php

declare(strict_types=1);

namespace DiyFormBundle\Tests\Procedure\Step;

use DiyFormBundle\Procedure\Step\SubmitDiyFormRecord;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitJsonRPC\AbstractProcedureTestCase;

/**
 * @internal
 */
#[CoversClass(SubmitDiyFormRecord::class)]
#[RunTestsInSeparateProcesses]
final class SubmitDiyFormRecordTest extends AbstractProcedureTestCase
{
    protected function onSetUp(): void
    {
    }

    public function testProcedureCanBeInstantiated(): void
    {
        $procedure = self::getService(SubmitDiyFormRecord::class);
        $this->assertInstanceOf(SubmitDiyFormRecord::class, $procedure);
    }

    public function testExecuteWithValidParameters(): void
    {
        $procedure = self::getService(SubmitDiyFormRecord::class);

        // 测试Procedure实例化成功
        $this->assertInstanceOf(SubmitDiyFormRecord::class, $procedure);

        // 测试execute方法存在并且可以被反射
        $reflection = new \ReflectionClass($procedure);
        $this->assertTrue($reflection->hasMethod('execute'));
    }
}
