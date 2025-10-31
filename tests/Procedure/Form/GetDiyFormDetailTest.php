<?php

declare(strict_types=1);

namespace DiyFormBundle\Tests\Procedure\Form;

use DiyFormBundle\Procedure\Form\GetDiyFormDetail;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\JsonRPC\Core\Tests\AbstractProcedureTestCase;

/**
 * @internal
 */
#[CoversClass(GetDiyFormDetail::class)]
#[RunTestsInSeparateProcesses]
final class GetDiyFormDetailTest extends AbstractProcedureTestCase
{
    protected function onSetUp(): void
    {
    }

    public function testProcedureCanBeInstantiated(): void
    {
        $procedure = self::getService(GetDiyFormDetail::class);
        $this->assertInstanceOf(GetDiyFormDetail::class, $procedure);
    }

    public function testExecuteMethodExists(): void
    {
        $procedure = self::getService(GetDiyFormDetail::class);
        $this->assertTrue(method_exists($procedure, 'execute'));
    }
}
