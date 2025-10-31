<?php

declare(strict_types=1);

namespace DiyFormBundle\DependencyInjection;

use Tourze\SymfonyDependencyServiceLoader\AutoExtension;

class DiyFormExtension extends AutoExtension
{
    protected function getConfigDir(): string
    {
        return __DIR__ . '/../Resources/config';
    }
}
