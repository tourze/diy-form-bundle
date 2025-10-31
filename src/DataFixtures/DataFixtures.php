<?php

declare(strict_types=1);

namespace DiyFormBundle\DataFixtures;

use DiyFormBundle\Entity\Data;
use DiyFormBundle\Entity\Record;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When(env: 'disabled')]
class DataFixtures extends Fixture implements DependentFixtureInterface
{
    public const DATA_TEST = 'data-test';

    public function load(ObjectManager $manager): void
    {
        $record = $this->getReference(RecordFixtures::RECORD_TEST, Record::class);

        $data = new Data();
        $data->setInput('测试答案');
        $data->setRecord($record);

        $manager->persist($data);
        $manager->flush();

        $this->addReference(self::DATA_TEST, $data);
    }

    public function getDependencies(): array
    {
        return [
            RecordFixtures::class,
        ];
    }
}
