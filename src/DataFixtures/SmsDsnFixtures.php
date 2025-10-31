<?php

declare(strict_types=1);

namespace DiyFormBundle\DataFixtures;

use DiyFormBundle\Entity\SmsDsn;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When(env: 'test')]
#[When(env: 'dev')]
class SmsDsnFixtures extends Fixture
{
    public const SMS_DSN_TEST = 'sms-dsn-test';

    public function load(ObjectManager $manager): void
    {
        $smsDsn = new SmsDsn();
        $smsDsn->setName('测试短信配置');
        $smsDsn->setDsn('null://null');
        $smsDsn->setWeight(100);
        $smsDsn->setValid(true);

        $manager->persist($smsDsn);
        $manager->flush();

        $this->addReference(self::SMS_DSN_TEST, $smsDsn);
    }
}
