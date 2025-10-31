<?php

declare(strict_types=1);

namespace DiyFormBundle\Tests\Procedure\Step;

use DiyFormBundle\Procedure\Step\GetNextDiyFormField;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\JsonRPC\Core\Tests\AbstractProcedureTestCase;

/**
 * @internal
 */
#[CoversClass(GetNextDiyFormField::class)]
#[RunTestsInSeparateProcesses]
final class GetNextDiyFormFieldTest extends AbstractProcedureTestCase
{
    protected function onSetUp(): void
    {
    }

    public function testProcedureCanBeInstantiated(): void
    {
        $procedure = self::getService(GetNextDiyFormField::class);
        $this->assertInstanceOf(GetNextDiyFormField::class, $procedure);
    }

    public function testExecuteMethodExists(): void
    {
        $procedure = self::getService(GetNextDiyFormField::class);
        $this->assertTrue(method_exists($procedure, 'execute'));
    }
}
