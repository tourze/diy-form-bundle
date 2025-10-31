<?php

declare(strict_types=1);

namespace DiyFormBundle\Tests\Procedure\Record;

use DiyFormBundle\Procedure\Record\GetMyInviteFormRecordList;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\JsonRPC\Core\Tests\AbstractProcedureTestCase;

/**
 * @internal
 */
#[CoversClass(GetMyInviteFormRecordList::class)]
#[RunTestsInSeparateProcesses]
final class GetMyInviteFormRecordListTest extends AbstractProcedureTestCase
{
    protected function onSetUp(): void
    {
    }

    public function testProcedureCanBeInstantiated(): void
    {
        $procedure = self::getService(GetMyInviteFormRecordList::class);
        $this->assertInstanceOf(GetMyInviteFormRecordList::class, $procedure);
    }

    public function testExecuteMethodExists(): void
    {
        $procedure = self::getService(GetMyInviteFormRecordList::class);
        $this->assertTrue(method_exists($procedure, 'execute'));
    }
}
