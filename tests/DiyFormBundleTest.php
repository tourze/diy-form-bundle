<?php

declare(strict_types=1);

namespace DiyFormBundle\Tests;

use DiyFormBundle\DiyFormBundle;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractBundleTestCase;

/**
 * @internal
 */
#[CoversClass(DiyFormBundle::class)]
#[RunTestsInSeparateProcesses]
final class DiyFormBundleTest extends AbstractBundleTestCase
{
}
