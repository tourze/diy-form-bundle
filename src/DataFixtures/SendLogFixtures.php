<?php

declare(strict_types=1);

namespace DiyFormBundle\DataFixtures;

use DiyFormBundle\Entity\SendLog;
use DiyFormBundle\Enum\SmsReceiveEnum;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When(env: 'test')]
#[When(env: 'dev')]
class SendLogFixtures extends Fixture
{
    public const SEND_LOG_TEST = 'send-log-test';

    public function load(ObjectManager $manager): void
    {
        $sendLog = new SendLog();
        $sendLog->setBatchId('test-batch-123');
        $sendLog->setMobile('13800000000');
        $sendLog->setStatus(SmsReceiveEnum::SENT);

        $manager->persist($sendLog);
        $manager->flush();

        $this->addReference(self::SEND_LOG_TEST, $sendLog);
    }
}
