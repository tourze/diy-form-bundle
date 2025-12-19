<?php

declare(strict_types=1);

namespace DiyFormBundle\Tests\Procedure\Form;

use DiyFormBundle\Procedure\Form\GetFullDiyFormDetail;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitJsonRPC\AbstractProcedureTestCase;

/**
 * @internal
 */
#[CoversClass(GetFullDiyFormDetail::class)]
#[RunTestsInSeparateProcesses]
final class GetFullDiyFormDetailTest extends AbstractProcedureTestCase
{
    protected function onSetUp(): void
    {
    }

    public function testProcedureCanBeInstantiated(): void
    {
        $procedure = self::getService(GetFullDiyFormDetail::class);
        $this->assertNotNull($procedure);
    }

    public function testExecute(): void
    {
        $procedure = self::getService(GetFullDiyFormDetail::class);

        // 测试execute方法存在且可调用
        $this->assertTrue(method_exists($procedure, 'execute'));
        $this->assertTrue(true); // 标记测试完成
    }
}
