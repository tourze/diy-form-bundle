<?php

declare(strict_types=1);

namespace DiyFormBundle\DataFixtures;

use DiyFormBundle\Entity\Field;
use DiyFormBundle\Entity\Option;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When(env: 'disabled')]
class OptionFixtures extends Fixture implements DependentFixtureInterface
{
    public const OPTION_TEST = 'option-test';

    public function load(ObjectManager $manager): void
    {
        $field = $this->getReference(FieldFixtures::FIELD_TEST, Field::class);

        $option = new Option();
        $option->setField($field);
        $option->setSn('test-option-001');
        $option->setText('测试选项');
        $option->setAllowInput(false);

        $manager->persist($option);
        $manager->flush();

        $this->addReference(self::OPTION_TEST, $option);
    }

    public function getDependencies(): array
    {
        return [
            FieldFixtures::class,
        ];
    }
}
