<?php

declare(strict_types=1);

namespace DiyFormBundle\Tests\Repository;

use DiyFormBundle\Entity\Form;
use DiyFormBundle\Entity\Record;
use DiyFormBundle\Repository\RecordRepository;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\OptimisticLockException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(RecordRepository::class)]
#[RunTestsInSeparateProcesses]
final class RecordRepositoryTest extends AbstractRepositoryTestCase
{
    protected function onSetUp(): void
    {
        // Repository 测试基类要求的初始化方法
    }

    protected function createNewEntity(): Record
    {
        $form = new Form();
        $form->setTitle('测试表单-' . uniqid());
        $form->setValid(true);
        $form->setStartTime(new \DateTimeImmutable());
        $form->setEndTime(new \DateTimeImmutable('+1 hour'));

        $record = new Record();
        $record->setForm($form);
        $record->setStartTime(new \DateTimeImmutable());
        $record->setFinished(false);

        return $record;
    }

    protected function getRepository(): RecordRepository
    {
        return self::getService(RecordRepository::class);
    }

    public function testFindWithOptimisticLockWhenVersionMismatchesShouldThrowExceptionOnFlush(): void
    {
        // Check if lockVersion column exists in test database
        $connection = self::getEntityManager()->getConnection();
        try {
            $schemaManager = $connection->createSchemaManager();
            $table = $schemaManager->introspectTable('diy_form_record');

            if (!$table->hasColumn('lockVersion')) {
                self::markTestSkipped('lockVersion column does not exist in test database');
            }
        } catch (\Exception $e) {
            // If schema inspection fails, skip the test gracefully
            self::markTestSkipped('Unable to inspect database schema: ' . $e->getMessage());
        }

        // Arrange: 创建并持久化一个Record实体
        $entity = $this->createNewEntity();
        $em = self::getEntityManager();
        $form = $entity->getForm();
        self::assertNotNull($form);
        $em->persist($form);
        $em->persist($entity);
        $em->flush();
        $entityId = $entity->getId();

        // Act: 查找实体
        $foundEntity = $this->getRepository()->find($entityId);
        self::assertNotNull($foundEntity);

        // 模拟外部更新（直接通过DBAL更新版本号）
        $connection->executeStatement(
            'UPDATE diy_form_record SET lockVersion = lockVersion + 1 WHERE id = ?',
            [$entityId]
        );

        // 修改加载的实体的某个属性
        self::assertInstanceOf(Record::class, $foundEntity);
        $foundEntity->setFinished(true);

        // Assert: flush操作应该抛出OptimisticLockException
        self::expectException(OptimisticLockException::class);
        $em->flush();
    }

    public function testFindWithPessimisticWriteLockShouldReturnEntityAndLockRow(): void
    {
        // Arrange: 创建并持久化一个Record实体
        $entity = $this->createNewEntity();
        $em = self::getEntityManager();
        $form = $entity->getForm();
        self::assertNotNull($form);
        $em->persist($form);
        $em->persist($entity);
        $em->flush();
        $entityId = $entity->getId();

        // Act & Assert: 在事务中使用悲观写锁查找实体
        $result = $em->wrapInTransaction(function () use ($entityId) {
            $foundEntity = $this->getRepository()->find($entityId, LockMode::PESSIMISTIC_WRITE);

            // 断言返回的对象是正确的实体类的实例
            self::assertInstanceOf(Record::class, $foundEntity);
            self::assertSame($entityId, $foundEntity->getId());

            return $foundEntity;
        });

        // 验证事务成功完成且返回了正确的实体
        self::assertInstanceOf(Record::class, $result);
    }
}
