<?php

declare(strict_types=1);

namespace DiyFormBundle;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use EasyCorp\Bundle\EasyAdminBundle\EasyAdminBundle;
use Knp\Bundle\PaginatorBundle\KnpPaginatorBundle;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tourze\BundleDependency\BundleDependencyInterface;
use Tourze\DoctrineAsyncInsertBundle\DoctrineAsyncInsertBundle;
use Tourze\DoctrineIndexedBundle\DoctrineIndexedBundle;
use Tourze\DoctrineIpBundle\DoctrineIpBundle;
use Tourze\DoctrineSnowflakeBundle\DoctrineSnowflakeBundle;
use Tourze\DoctrineTimestampBundle\DoctrineTimestampBundle;
use Tourze\DoctrineTrackBundle\DoctrineTrackBundle;
use Tourze\DoctrineUserAgentBundle\DoctrineUserAgentBundle;
use Tourze\DoctrineUserBundle\DoctrineUserBundle;
use Tourze\EasyAdminExtraBundle\EasyAdminExtraBundle;
use Tourze\EasyAdminMenuBundle\EasyAdminMenuBundle;
use Tourze\EcolBundle\EcolBundle;
use Tourze\JsonRPCEndpointBundle\JsonRPCEndpointBundle;
use Tourze\JsonRPCLockBundle\JsonRPCLockBundle;
use Tourze\JsonRPCLogBundle\JsonRPCLogBundle;
use Tourze\JsonRPCPaginatorBundle\JsonRPCPaginatorBundle;
use Tourze\JsonRPCSecurityBundle\JsonRPCSecurityBundle;
use Tourze\LockServiceBundle\LockServiceBundle;
use Tourze\RoutingAutoLoaderBundle\RoutingAutoLoaderBundle;
use Tourze\TextManageBundle\TextManageBundle;
use Tourze\UserAvatarBundle\UserAvatarBundle;
use Tourze\UserEventBundle\UserEventBundle;
use Tourze\UserIDBundle\UserIDBundle;

class DiyFormBundle extends Bundle implements BundleDependencyInterface
{
    public static function getBundleDependencies(): array
    {
        return [
            DoctrineBundle::class => ['all' => true],
            EasyAdminBundle::class => ['all' => true],
            TwigBundle::class => ['all' => true],
            KnpPaginatorBundle::class => ['all' => true],
            SecurityBundle::class => ['all' => true],
            DoctrineAsyncInsertBundle::class => ['all' => true],
            DoctrineIndexedBundle::class => ['all' => true],
            DoctrineIpBundle::class => ['all' => true],
            DoctrineSnowflakeBundle::class => ['all' => true],
            DoctrineTimestampBundle::class => ['all' => true],
            DoctrineTrackBundle::class => ['all' => true],
            DoctrineUserAgentBundle::class => ['all' => true],
            DoctrineUserBundle::class => ['all' => true],
            EasyAdminExtraBundle::class => ['all' => true],
            EasyAdminMenuBundle::class => ['all' => true],
            JsonRPCEndpointBundle::class => ['all' => true],
            JsonRPCLockBundle::class => ['all' => true],
            JsonRPCLogBundle::class => ['all' => true],
            JsonRPCPaginatorBundle::class => ['all' => true],
            JsonRPCSecurityBundle::class => ['all' => true],
            LockServiceBundle::class => ['all' => true],
            EcolBundle::class => ['all' => true],
            TextManageBundle::class => ['all' => true],
            UserAvatarBundle::class => ['all' => true],
            UserEventBundle::class => ['all' => true],
            UserIDBundle::class => ['all' => true],
            RoutingAutoLoaderBundle::class => ['all' => true],
        ];
    }
}
