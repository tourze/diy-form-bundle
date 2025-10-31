<?php

declare(strict_types=1);

namespace DiyFormBundle\Tests\Repository;

use DiyFormBundle\Entity\SmsDsn;
use DiyFormBundle\Repository\SmsDsnRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(SmsDsnRepository::class)]
#[RunTestsInSeparateProcesses]
final class SmsDsnRepositoryTest extends AbstractRepositoryTestCase
{
    private EntityManagerInterface $em;

    private SmsDsnRepository $repository;

    protected function onSetUp(): void
    {
        $this->em = self::getEntityManager();
        $repository = $this->em->getRepository(SmsDsn::class);
        self::assertInstanceOf(SmsDsnRepository::class, $repository);
        $this->repository = $repository;
    }

    public function testSave(): void
    {
        $smsDsn = new SmsDsn();
        $smsDsn->setName('Save Test SMS Provider');
        $smsDsn->setDsn('twilio://key:secret@default?from=+1234567890');
        $smsDsn->setWeight(50);
        $smsDsn->setValid(true);

        $this->repository->save($smsDsn, true);

        self::assertGreaterThan(0, $smsDsn->getId());

        $foundSmsDsn = $this->repository->find($smsDsn->getId());
        self::assertInstanceOf(SmsDsn::class, $foundSmsDsn);
        self::assertSame('Save Test SMS Provider', $foundSmsDsn->getName());
        self::assertSame(50, $foundSmsDsn->getWeight());
    }

    public function testRemove(): void
    {
        $smsDsn = $this->createTestSmsDsn();
        $smsDsnId = $smsDsn->getId();

        $this->repository->remove($smsDsn, true);

        $foundSmsDsn = $this->repository->find($smsDsnId);
        self::assertNull($foundSmsDsn);
    }

    public function testFindOneByWithValidFieldShouldReturnEntity(): void
    {
        $smsDsn = $this->createTestSmsDsn();

        $foundSmsDsn = $this->repository->findOneBy(['name' => 'Test SMS Provider']);
        self::assertInstanceOf(SmsDsn::class, $foundSmsDsn);
        self::assertSame($smsDsn->getId(), $foundSmsDsn->getId());
    }

    public function testFindByValidTrueShouldReturnValidProviders(): void
    {
        $validProvider = $this->createTestSmsDsn();
        $invalidProvider = new SmsDsn();
        $invalidProvider->setName('Invalid SMS Provider');
        $invalidProvider->setDsn('invalid://provider@test');
        $invalidProvider->setWeight(10);
        $invalidProvider->setValid(false);
        $this->em->persist($invalidProvider);
        $this->em->flush();

        $validProviders = $this->repository->findBy(['valid' => true]);
        self::assertNotEmpty($validProviders);

        foreach ($validProviders as $provider) {
            self::assertTrue($provider->isValid());
        }
    }

    public function testFindByWeightShouldReturnMatchingProviders(): void
    {
        $highPriorityProvider = new SmsDsn();
        $highPriorityProvider->setName('High Priority Provider');
        $highPriorityProvider->setDsn('aws://key:secret@us-east-1');
        $highPriorityProvider->setWeight(100);
        $highPriorityProvider->setValid(true);

        $lowPriorityProvider = new SmsDsn();
        $lowPriorityProvider->setName('Low Priority Provider');
        $lowPriorityProvider->setDsn('clicksend://key:secret@default');
        $lowPriorityProvider->setWeight(10);
        $lowPriorityProvider->setValid(true);

        $this->em->persist($highPriorityProvider);
        $this->em->persist($lowPriorityProvider);
        $this->em->flush();

        $highWeightProviders = $this->repository->findBy(['weight' => 100]);
        self::assertNotEmpty($highWeightProviders);

        foreach ($highWeightProviders as $provider) {
            self::assertSame(100, $provider->getWeight());
        }

        $lowWeightProviders = $this->repository->findBy(['weight' => 10]);
        self::assertNotEmpty($lowWeightProviders);

        foreach ($lowWeightProviders as $provider) {
            self::assertSame(10, $provider->getWeight());
        }
    }

    public function testFindValidProvidersOrderedByWeightShouldReturnCorrectOrder(): void
    {
        // 首先清理所有已存在的 SmsDsn 记录
        $existingProviders = $this->repository->findAll();
        foreach ($existingProviders as $provider) {
            $this->em->remove($provider);
        }
        $this->em->flush();

        $provider1 = new SmsDsn();
        $provider1->setName('Medium Priority');
        $provider1->setDsn('medium://config');
        $provider1->setWeight(50);
        $provider1->setValid(true);

        $provider2 = new SmsDsn();
        $provider2->setName('High Priority');
        $provider2->setDsn('high://config');
        $provider2->setWeight(80);
        $provider2->setValid(true);

        $provider3 = new SmsDsn();
        $provider3->setName('Disabled Provider');
        $provider3->setDsn('disabled://config');
        $provider3->setWeight(90);
        $provider3->setValid(false);

        $this->em->persist($provider1);
        $this->em->persist($provider2);
        $this->em->persist($provider3);
        $this->em->flush();

        $validProviders = $this->repository->findBy(['valid' => true], ['weight' => 'DESC']);
        self::assertCount(2, $validProviders);

        self::assertSame(80, $validProviders[0]->getWeight());
        self::assertSame(50, $validProviders[1]->getWeight());

        // 验证被禁用的提供商不在结果中
        foreach ($validProviders as $provider) {
            self::assertTrue($provider->isValid());
        }
    }

    public function testCountShouldReturnCorrectNumber(): void
    {
        $initialCount = $this->repository->count([]);

        $this->createTestSmsDsn();
        $this->createTestSmsDsn('Second SMS Provider', 'second://provider@test', 25);

        $newCount = $this->repository->count([]);
        self::assertSame($initialCount + 2, $newCount);
    }

    public function testCountValidProvidersShouldReturnCorrectNumber(): void
    {
        $validProvider = $this->createTestSmsDsn();
        $invalidProvider = new SmsDsn();
        $invalidProvider->setName('Invalid Provider');
        $invalidProvider->setDsn('invalid://config');
        $invalidProvider->setWeight(10);
        $invalidProvider->setValid(false);
        $this->em->persist($invalidProvider);
        $this->em->flush();

        $validCount = $this->repository->count(['valid' => true]);
        $totalCount = $this->repository->count([]);

        self::assertGreaterThanOrEqual(1, $validCount);
        self::assertGreaterThan($validCount, $totalCount);
    }

    protected function createNewEntity(): object
    {
        $smsDsn = new SmsDsn();
        $smsDsn->setName('Create New Entity SMS Provider');
        $smsDsn->setDsn('test://provider' . uniqid() . '@default');
        $smsDsn->setWeight(50);
        $smsDsn->setValid(true);

        return $smsDsn;
    }

    private function createTestSmsDsn(string $name = 'Test SMS Provider', string $dsn = 'twilio://test:secret@default?from=+1234567890', int $weight = 75): SmsDsn
    {
        $smsDsn = new SmsDsn();
        $smsDsn->setName($name);
        $smsDsn->setDsn($dsn);
        $smsDsn->setWeight($weight);
        $smsDsn->setValid(true);

        $this->em->persist($smsDsn);
        $this->em->flush();

        return $smsDsn;
    }

    protected function getRepository(): SmsDsnRepository
    {
        return $this->repository;
    }
}
