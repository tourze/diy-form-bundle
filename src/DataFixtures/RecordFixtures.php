<?php

declare(strict_types=1);

namespace DiyFormBundle\DataFixtures;

use DiyFormBundle\Entity\Form;
use DiyFormBundle\Entity\Record;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When(env: 'disabled')]
class RecordFixtures extends Fixture implements DependentFixtureInterface
{
    public const RECORD_TEST = 'record-test';

    public function load(ObjectManager $manager): void
    {
        $form = $this->getReference(FormFixtures::FORM_TEST, Form::class);

        $record = new Record();
        $record->setFinished(false);
        $record->setForm($form);
        $record->setStartTime(new \DateTimeImmutable());

        $manager->persist($record);
        $manager->flush();

        $this->addReference(self::RECORD_TEST, $record);
    }

    public function getDependencies(): array
    {
        return [
            FormFixtures::class,
        ];
    }
}
