<?php

declare(strict_types=1);

namespace DiyFormBundle\DataFixtures;

use DiyFormBundle\Entity\Field;
use DiyFormBundle\Entity\Form;
use DiyFormBundle\Enum\FieldType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When(env: 'disabled')]
class FieldFixtures extends Fixture implements DependentFixtureInterface
{
    public const FIELD_TEST = 'field-test';

    public function load(ObjectManager $manager): void
    {
        $form = $this->getReference(FormFixtures::FORM_TEST, Form::class);

        $field = new Field();
        $field->setForm($form);
        $field->setSn('test-field-001');
        $field->setTitle('测试字段');
        $field->setType(FieldType::TEXT);
        $field->setSortNumber(1);
        $field->setValid(true);

        $manager->persist($field);
        $manager->flush();

        $this->addReference(self::FIELD_TEST, $field);
    }

    public function getDependencies(): array
    {
        return [
            FormFixtures::class,
        ];
    }
}
