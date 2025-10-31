<?php

declare(strict_types=1);

namespace DiyFormBundle\Tests\DependencyInjection;

use DiyFormBundle\DependencyInjection\DiyFormExtension;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitSymfonyUnitTest\AbstractDependencyInjectionExtensionTestCase;

/**
 * @internal
 */
#[CoversClass(DiyFormExtension::class)]
final class DiyFormExtensionTest extends AbstractDependencyInjectionExtensionTestCase
{
}
