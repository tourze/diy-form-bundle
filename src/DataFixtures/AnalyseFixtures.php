<?php

declare(strict_types=1);

namespace DiyFormBundle\DataFixtures;

use DiyFormBundle\Entity\Analyse;
use DiyFormBundle\Entity\Form;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When(env: 'test')]
#[When(env: 'dev')]
class AnalyseFixtures extends Fixture implements DependentFixtureInterface
{
    public const ANALYSE_TEST = 'analyse-test';

    public function load(ObjectManager $manager): void
    {
        $form = $this->getReference(FormFixtures::FORM_TEST, Form::class);

        $analyse = new Analyse();
        $analyse->setForm($form);
        $analyse->setTitle('测试分析规则');
        $analyse->setRule('test = true');
        $analyse->setResult('测试结果');
        $analyse->setRemark('用于测试的分析规则');
        $analyse->setSortNumber(1);
        $analyse->setValid(true);

        $manager->persist($analyse);
        $manager->flush();

        $this->addReference(self::ANALYSE_TEST, $analyse);
    }

    public function getDependencies(): array
    {
        return [
            FormFixtures::class,
        ];
    }
}
