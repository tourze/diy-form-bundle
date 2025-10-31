<?php

declare(strict_types=1);

namespace DiyFormBundle\Tests\Repository;

use DiyFormBundle\Entity\Analyse;
use DiyFormBundle\Entity\Form;
use DiyFormBundle\Repository\AnalyseRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(AnalyseRepository::class)]
#[RunTestsInSeparateProcesses]
final class AnalyseRepositoryTest extends AbstractRepositoryTestCase
{
    private EntityManagerInterface $em;

    private AnalyseRepository $repository;

    private Form $testForm;

    protected function createNewEntity(): object
    {
        $analyse = new Analyse();
        $analyse->setForm($this->testForm);
        $analyse->setTitle('Test Analysis ' . uniqid());
        $analyse->setRule('test = true');
        $analyse->setResult('Test passed');
        $analyse->setCategory('default');
        $analyse->setValid(true);

        return $analyse;
    }

    protected function onSetUp(): void
    {
        $this->em = self::getEntityManager();
        $repository = $this->em->getRepository(Analyse::class);
        self::assertInstanceOf(AnalyseRepository::class, $repository);
        $this->repository = $repository;

        // 创建测试用的Form
        $this->testForm = new Form();
        $this->testForm->setTitle('Test Form for Analyse');
        $this->testForm->setStartTime(new \DateTimeImmutable());
        $this->testForm->setEndTime(new \DateTimeImmutable('+1 month'));
        $this->em->persist($this->testForm);
        $this->em->flush();
    }

    public function testSave(): void
    {
        $analyse = new Analyse();
        $analyse->setForm($this->testForm);
        $analyse->setTitle('Save Test Analysis');
        $analyse->setRule('test rule');
        $analyse->setResult('test result');
        $analyse->setCategory('test category');

        $this->repository->save($analyse, true);

        self::assertGreaterThan(0, $analyse->getId());

        $foundAnalyse = $this->repository->find($analyse->getId());
        self::assertInstanceOf(Analyse::class, $foundAnalyse);
        self::assertSame('Save Test Analysis', $foundAnalyse->getTitle());
    }

    public function testRemove(): void
    {
        $analyse = $this->createTestAnalyse();
        $analyseId = $analyse->getId();

        $this->repository->remove($analyse, true);

        $foundAnalyse = $this->repository->find($analyseId);
        self::assertNull($foundAnalyse);
    }

    public function testFindOneByWithValidFieldShouldReturnEntity(): void
    {
        $analyse = $this->createTestAnalyse();

        $foundAnalyse = $this->repository->findOneBy(['title' => 'Test Analysis']);
        self::assertInstanceOf(Analyse::class, $foundAnalyse);
        self::assertSame($analyse->getId(), $foundAnalyse->getId());
    }

    public function testFindOneByWithNullFieldShouldReturnMatchingEntity(): void
    {
        $analyse = new Analyse();
        $analyse->setForm($this->testForm);
        $analyse->setTitle('Null Category Test');
        $analyse->setRule('test rule');
        $analyse->setResult('test result');
        $analyse->setCategory(null);
        $this->em->persist($analyse);
        $this->em->flush();

        $foundAnalyse = $this->repository->findOneBy(['category' => null]);
        self::assertInstanceOf(Analyse::class, $foundAnalyse);
        self::assertNull($foundAnalyse->getCategory());
    }

    public function testFindOneByWithOrderByShouldReturnFirstMatchingEntity(): void
    {
        $analyse1 = new Analyse();
        $analyse1->setForm($this->testForm);
        $analyse1->setTitle('Same Category Analysis 1');
        $analyse1->setRule('test rule');
        $analyse1->setResult('test result');
        $analyse1->setCategory('same-category');
        $analyse1->setSortNumber(2);
        $analyse2 = new Analyse();
        $analyse2->setForm($this->testForm);
        $analyse2->setTitle('Same Category Analysis 2');
        $analyse2->setRule('test rule');
        $analyse2->setResult('test result');
        $analyse2->setCategory('same-category');
        $analyse2->setSortNumber(1);

        $this->em->persist($analyse1);
        $this->em->persist($analyse2);
        $this->em->flush();

        $foundAnalyse = $this->repository->findOneBy(['category' => 'same-category'], ['sortNumber' => 'ASC']);
        self::assertInstanceOf(Analyse::class, $foundAnalyse);
        self::assertSame($analyse2->getId(), $foundAnalyse->getId());
    }

    public function testFindByWithNullCategoryShouldReturnMatchingEntities(): void
    {
        // 清除可能存在的旧数据
        $existingAnalyses = $this->repository->findAll();
        foreach ($existingAnalyses as $analyse) {
            $this->em->remove($analyse);
        }
        $this->em->flush();

        $analyse1 = new Analyse();
        $analyse1->setForm($this->testForm);
        $analyse1->setTitle('Null Category Analysis 1');
        $analyse1->setRule('test rule');
        $analyse1->setResult('test result');
        $analyse1->setCategory(null);
        $analyse2 = new Analyse();
        $analyse2->setForm($this->testForm);
        $analyse2->setTitle('Null Category Analysis 2');
        $analyse2->setRule('test rule');
        $analyse2->setResult('test result');
        $analyse2->setCategory(null);
        $analyse3 = new Analyse();
        $analyse3->setForm($this->testForm);
        $analyse3->setTitle('With Category Analysis');
        $analyse3->setRule('test rule');
        $analyse3->setResult('test result');
        $analyse3->setCategory('has-category');

        $this->em->persist($analyse1);
        $this->em->persist($analyse2);
        $this->em->persist($analyse3);
        $this->em->flush();

        $analyses = $this->repository->findBy(['category' => null]);
        self::assertCount(2, $analyses);
        foreach ($analyses as $analyse) {
            self::assertNull($analyse->getCategory());
        }
    }

    public function testFindByWithNullThumbShouldReturnMatchingEntities(): void
    {
        // 清除可能存在的旧数据
        $existingAnalyses = $this->repository->findAll();
        foreach ($existingAnalyses as $analyse) {
            $this->em->remove($analyse);
        }
        $this->em->flush();

        $analyse1 = new Analyse();
        $analyse1->setForm($this->testForm);
        $analyse1->setTitle('Null Thumb Analysis 1');
        $analyse1->setRule('test rule');
        $analyse1->setResult('test result');
        $analyse1->setThumb(null);
        $analyse2 = new Analyse();
        $analyse2->setForm($this->testForm);
        $analyse2->setTitle('Null Thumb Analysis 2');
        $analyse2->setRule('test rule');
        $analyse2->setResult('test result');
        $analyse2->setThumb(null);
        $analyse3 = new Analyse();
        $analyse3->setForm($this->testForm);
        $analyse3->setTitle('With Thumb Analysis');
        $analyse3->setRule('test rule');
        $analyse3->setResult('test result');
        $analyse3->setThumb('thumb.jpg');

        $this->em->persist($analyse1);
        $this->em->persist($analyse2);
        $this->em->persist($analyse3);
        $this->em->flush();

        $analyses = $this->repository->findBy(['thumb' => null]);
        self::assertCount(2, $analyses);
        foreach ($analyses as $analyse) {
            self::assertNull($analyse->getThumb());
        }
    }

    public function testCountWithNullCategoryShouldReturnCorrectNumber(): void
    {
        // 清除可能存在的旧数据
        $existingAnalyses = $this->repository->findAll();
        foreach ($existingAnalyses as $analyse) {
            $this->em->remove($analyse);
        }
        $this->em->flush();

        $analyse1 = new Analyse();
        $analyse1->setForm($this->testForm);
        $analyse1->setTitle('Count Null Category 1');
        $analyse1->setRule('test rule');
        $analyse1->setResult('test result');
        $analyse1->setCategory(null);
        $analyse2 = new Analyse();
        $analyse2->setForm($this->testForm);
        $analyse2->setTitle('Count Null Category 2');
        $analyse2->setRule('test rule');
        $analyse2->setResult('test result');
        $analyse2->setCategory(null);
        $analyse3 = new Analyse();
        $analyse3->setForm($this->testForm);
        $analyse3->setTitle('Count With Category');
        $analyse3->setRule('test rule');
        $analyse3->setResult('test result');
        $analyse3->setCategory('has-category');

        $this->em->persist($analyse1);
        $this->em->persist($analyse2);
        $this->em->persist($analyse3);
        $this->em->flush();

        $count = $this->repository->count(['category' => null]);
        self::assertSame(2, $count);
    }

    public function testCountWithNullThumbShouldReturnCorrectNumber(): void
    {
        // 清除可能存在的旧数据
        $existingAnalyses = $this->repository->findAll();
        foreach ($existingAnalyses as $analyse) {
            $this->em->remove($analyse);
        }
        $this->em->flush();

        $analyse1 = new Analyse();
        $analyse1->setForm($this->testForm);
        $analyse1->setTitle('Count Null Thumb 1');
        $analyse1->setRule('test rule');
        $analyse1->setResult('test result');
        $analyse1->setThumb(null);
        $analyse2 = new Analyse();
        $analyse2->setForm($this->testForm);
        $analyse2->setTitle('Count Null Thumb 2');
        $analyse2->setRule('test rule');
        $analyse2->setResult('test result');
        $analyse2->setThumb(null);
        $analyse3 = new Analyse();
        $analyse3->setForm($this->testForm);
        $analyse3->setTitle('Count With Thumb');
        $analyse3->setRule('test rule');
        $analyse3->setResult('test result');
        $analyse3->setThumb('thumb.jpg');

        $this->em->persist($analyse1);
        $this->em->persist($analyse2);
        $this->em->persist($analyse3);
        $this->em->flush();

        $count = $this->repository->count(['thumb' => null]);
        self::assertSame(2, $count);
    }

    public function testFindByFormAssociationShouldReturnMatchingEntities(): void
    {
        $otherForm = new Form();
        $otherForm->setTitle('Other Form');
        $otherForm->setStartTime(new \DateTimeImmutable());
        $otherForm->setEndTime(new \DateTimeImmutable('+1 month'));
        $this->em->persist($otherForm);
        $this->em->flush();

        $analyse1 = new Analyse();
        $analyse1->setForm($this->testForm);
        $analyse1->setTitle('Analysis for Test Form');
        $analyse1->setRule('test rule');
        $analyse1->setResult('test result');
        $analyse2 = new Analyse();
        $analyse2->setForm($otherForm);
        $analyse2->setTitle('Analysis for Other Form');
        $analyse2->setRule('test rule');
        $analyse2->setResult('test result');

        $this->em->persist($analyse1);
        $this->em->persist($analyse2);
        $this->em->flush();

        $analyses = $this->repository->findBy(['form' => $this->testForm]);
        self::assertGreaterThanOrEqual(1, count($analyses));
        foreach ($analyses as $analyse) {
            $form = $analyse->getForm();
            self::assertNotNull($form);
            self::assertSame($this->testForm->getId(), $form->getId());
        }
    }

    public function testCountByFormAssociationShouldReturnCorrectNumber(): void
    {
        $otherForm = new Form();
        $otherForm->setTitle('Other Form for Count');
        $otherForm->setStartTime(new \DateTimeImmutable());
        $otherForm->setEndTime(new \DateTimeImmutable('+1 month'));
        $this->em->persist($otherForm);
        $this->em->flush();

        $initialCount = $this->repository->count(['form' => $this->testForm]);

        $analyse1 = new Analyse();
        $analyse1->setForm($this->testForm);
        $analyse1->setTitle('Count Analysis for Test Form 1');
        $analyse1->setRule('test rule');
        $analyse1->setResult('test result');
        $analyse2 = new Analyse();
        $analyse2->setForm($this->testForm);
        $analyse2->setTitle('Count Analysis for Test Form 2');
        $analyse2->setRule('test rule');
        $analyse2->setResult('test result');
        $analyse3 = new Analyse();
        $analyse3->setForm($otherForm);
        $analyse3->setTitle('Count Analysis for Other Form');
        $analyse3->setRule('test rule');
        $analyse3->setResult('test result');

        $this->em->persist($analyse1);
        $this->em->persist($analyse2);
        $this->em->persist($analyse3);
        $this->em->flush();

        $finalCount = $this->repository->count(['form' => $this->testForm]);
        self::assertSame($initialCount + 2, $finalCount);
    }

    private function createTestAnalyse(): Analyse
    {
        $analyse = new Analyse();
        $analyse->setForm($this->testForm);
        $analyse->setTitle('Test Analysis');
        $analyse->setRule('test = true');
        $analyse->setResult('Test passed');
        $analyse->setCategory('default');
        $analyse->setValid(true);

        $this->em->persist($analyse);
        $this->em->flush();

        return $analyse;
    }

    protected function getRepository(): AnalyseRepository
    {
        return $this->repository;
    }
}
