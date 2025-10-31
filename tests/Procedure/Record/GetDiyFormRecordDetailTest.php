<?php

declare(strict_types=1);

namespace DiyFormBundle\Tests\Procedure\Record;

use DiyFormBundle\Procedure\Record\GetDiyFormRecordDetail;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\JsonRPC\Core\Tests\AbstractProcedureTestCase;

/**
 * @internal
 */
#[CoversClass(GetDiyFormRecordDetail::class)]
#[RunTestsInSeparateProcesses]
final class GetDiyFormRecordDetailTest extends AbstractProcedureTestCase
{
    protected function onSetUp(): void
    {
    }

    public function testProcedureCanBeInstantiated(): void
    {
        $procedure = self::getService(GetDiyFormRecordDetail::class);
        $this->assertInstanceOf(GetDiyFormRecordDetail::class, $procedure);
    }

    public function testExecuteMethodExists(): void
    {
        $procedure = self::getService(GetDiyFormRecordDetail::class);
        $this->assertTrue(method_exists($procedure, 'execute'));
    }
}
