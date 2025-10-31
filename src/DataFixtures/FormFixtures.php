<?php

declare(strict_types=1);

namespace DiyFormBundle\DataFixtures;

use DiyFormBundle\Entity\Form;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When(env: 'test')]
#[When(env: 'dev')]
class FormFixtures extends Fixture
{
    public const FORM_TEST = 'form-test';

    public function load(ObjectManager $manager): void
    {
        $form = new Form();
        $form->setTitle('测试表单');
        $form->setDescription('用于测试的表单');
        $form->setStartTime(new \DateTimeImmutable());
        $form->setEndTime(new \DateTimeImmutable('+1 month'));
        $form->setValid(true);

        $manager->persist($form);
        $manager->flush();

        $this->addReference(self::FORM_TEST, $form);
    }
}
