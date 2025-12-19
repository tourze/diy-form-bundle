<?php

declare(strict_types=1);

namespace DiyFormBundle\Tests\Procedure\Record;

use DiyFormBundle\Procedure\Record\GetUserFormRecordList;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitJsonRPC\AbstractProcedureTestCase;

/**
 * @internal
 */
#[CoversClass(GetUserFormRecordList::class)]
#[RunTestsInSeparateProcesses]
final class GetUserFormRecordListTest extends AbstractProcedureTestCase
{
    protected function onSetUp(): void
    {
    }

    public function testProcedureCanBeInstantiated(): void
    {
        $procedure = self::getService(GetUserFormRecordList::class);
        $this->assertInstanceOf(GetUserFormRecordList::class, $procedure);
    }

    public function testExecuteMethodExists(): void
    {
        $procedure = self::getService(GetUserFormRecordList::class);
        $this->assertTrue(method_exists($procedure, 'execute'));
    }
}
