<?php

declare(strict_types=1);

namespace DiyFormBundle\Tests\Procedure\Step;

use DiyFormBundle\Procedure\Step\CreateDiyFormRecord;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\JsonRPC\Core\Tests\AbstractProcedureTestCase;

/**
 * @internal
 */
#[CoversClass(CreateDiyFormRecord::class)]
#[RunTestsInSeparateProcesses]
final class CreateDiyFormRecordTest extends AbstractProcedureTestCase
{
    protected function onSetUp(): void
    {
    }

    public function testProcedureCanBeInstantiated(): void
    {
        $procedure = self::getService(CreateDiyFormRecord::class);
        $this->assertInstanceOf(CreateDiyFormRecord::class, $procedure);
    }

    public function testExecuteMethodExists(): void
    {
        $procedure = self::getService(CreateDiyFormRecord::class);
        $this->assertTrue(method_exists($procedure, 'execute'));
    }
}
