<?php

declare(strict_types=1);

namespace DiyFormBundle\Tests\Repository;

use DiyFormBundle\Entity\SendLog;
use DiyFormBundle\Enum\SmsReceiveEnum;
use DiyFormBundle\Repository\SendLogRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(SendLogRepository::class)]
#[RunTestsInSeparateProcesses]
final class SendLogRepositoryTest extends AbstractRepositoryTestCase
{
    private EntityManagerInterface $em;

    private SendLogRepository $repository;

    protected function onSetUp(): void
    {
        $this->em = self::getEntityManager();
        $repository = $this->em->getRepository(SendLog::class);
        self::assertInstanceOf(SendLogRepository::class, $repository);
        $this->repository = $repository;
    }

    public function testSave(): void
    {
        $sendLog = new SendLog();
        $sendLog->setBatchId('SAVE_TEST_BATCH_001');
        $sendLog->setMobile('13900139000');
        $sendLog->setZone('86');
        $sendLog->setMemo('Save test memo');
        $sendLog->setStatus(SmsReceiveEnum::SENT);

        $this->repository->save($sendLog, true);

        self::assertGreaterThan(0, $sendLog->getId());

        $foundSendLog = $this->repository->find($sendLog->getId());
        self::assertInstanceOf(SendLog::class, $foundSendLog);
        self::assertSame('13900139000', $foundSendLog->getMobile());
        self::assertSame('SAVE_TEST_BATCH_001', $foundSendLog->getBatchId());
    }

    public function testRemove(): void
    {
        $sendLog = $this->createTestSendLog();
        $sendLogId = $sendLog->getId();

        $this->repository->remove($sendLog, true);

        $foundSendLog = $this->repository->find($sendLogId);
        self::assertNull($foundSendLog);
    }

    public function testFindOneByWithValidFieldShouldReturnEntity(): void
    {
        $sendLog = $this->createTestSendLog();

        $foundSendLog = $this->repository->findOneBy(['mobile' => '13800138000']);
        self::assertInstanceOf(SendLog::class, $foundSendLog);
        self::assertSame($sendLog->getId(), $foundSendLog->getId());
    }

    public function testFindOneByWithNullFieldShouldReturnMatchingEntity(): void
    {
        $sendLog = new SendLog();
        $sendLog->setBatchId('NULL_MEMO_BATCH_001');
        $sendLog->setMobile('13700137000');
        $sendLog->setZone('86');
        $sendLog->setMemo(null);
        $sendLog->setStatus(SmsReceiveEnum::SENT);
        $this->em->persist($sendLog);
        $this->em->flush();

        $foundSendLog = $this->repository->findOneBy(['memo' => null]);
        self::assertInstanceOf(SendLog::class, $foundSendLog);
        self::assertNull($foundSendLog->getMemo());
    }

    public function testFindByBatchIdShouldReturnAllLogsForBatch(): void
    {
        $batchId = 'BATCH_TEST_001';
        $sendLog1 = new SendLog();
        $sendLog1->setBatchId($batchId);
        $sendLog1->setMobile('13800138001');
        $sendLog1->setZone('86');
        $sendLog1->setStatus(SmsReceiveEnum::SENT);

        $sendLog2 = new SendLog();
        $sendLog2->setBatchId($batchId);
        $sendLog2->setMobile('13800138002');
        $sendLog2->setZone('86');
        $sendLog2->setStatus(SmsReceiveEnum::REJECT);

        $this->em->persist($sendLog1);
        $this->em->persist($sendLog2);
        $this->em->flush();

        $sendLogs = $this->repository->findBy(['batchId' => $batchId]);
        self::assertGreaterThanOrEqual(2, count($sendLogs));

        foreach ($sendLogs as $sendLog) {
            self::assertInstanceOf(SendLog::class, $sendLog);
            self::assertSame($batchId, $sendLog->getBatchId());
        }
    }

    public function testFindByStatusShouldReturnMatchingLogs(): void
    {
        $sentLog = new SendLog();
        $sentLog->setBatchId('STATUS_TEST_SENT');
        $sentLog->setMobile('13800138003');
        $sentLog->setZone('86');
        $sentLog->setStatus(SmsReceiveEnum::SENT);

        $rejectedLog = new SendLog();
        $rejectedLog->setBatchId('STATUS_TEST_REJECT');
        $rejectedLog->setMobile('13800138004');
        $rejectedLog->setZone('86');
        $rejectedLog->setStatus(SmsReceiveEnum::REJECT);

        $this->em->persist($sentLog);
        $this->em->persist($rejectedLog);
        $this->em->flush();

        $sentLogs = $this->repository->findBy(['status' => SmsReceiveEnum::SENT]);
        self::assertNotEmpty($sentLogs);

        foreach ($sentLogs as $sendLog) {
            self::assertSame(SmsReceiveEnum::SENT, $sendLog->getStatus());
        }

        $rejectedLogs = $this->repository->findBy(['status' => SmsReceiveEnum::REJECT]);
        self::assertNotEmpty($rejectedLogs);

        foreach ($rejectedLogs as $sendLog) {
            self::assertSame(SmsReceiveEnum::REJECT, $sendLog->getStatus());
        }
    }

    public function testFindByZoneShouldReturnMatchingLogs(): void
    {
        $chinaLog = new SendLog();
        $chinaLog->setBatchId('ZONE_TEST_CN');
        $chinaLog->setMobile('13800138005');
        $chinaLog->setZone('86');
        $chinaLog->setStatus(SmsReceiveEnum::SENT);

        $usLog = new SendLog();
        $usLog->setBatchId('ZONE_TEST_US');
        $usLog->setMobile('15551234567');
        $usLog->setZone('1');
        $usLog->setStatus(SmsReceiveEnum::SENT);

        $this->em->persist($chinaLog);
        $this->em->persist($usLog);
        $this->em->flush();

        $chinaLogs = $this->repository->findBy(['zone' => '86']);
        self::assertNotEmpty($chinaLogs);

        foreach ($chinaLogs as $sendLog) {
            self::assertSame('86', $sendLog->getZone());
        }
    }

    public function testCountShouldReturnCorrectNumber(): void
    {
        $initialCount = $this->repository->count([]);

        $this->createTestSendLog();
        $this->createTestSendLog('SECOND_BATCH_001', '13900139001');

        $newCount = $this->repository->count([]);
        self::assertSame($initialCount + 2, $newCount);
    }

    protected function createNewEntity(): object
    {
        $sendLog = new SendLog();
        $sendLog->setBatchId('CREATE_NEW_ENTITY_' . uniqid());
        $sendLog->setMobile('1' . str_pad((string) rand(3000000000, 9999999999), 10, '0', STR_PAD_LEFT));
        $sendLog->setZone('86');
        $sendLog->setStatus(SmsReceiveEnum::SENT);

        return $sendLog;
    }

    private function createTestSendLog(string $batchId = 'TEST_BATCH_001', string $mobile = '13800138000'): SendLog
    {
        $sendLog = new SendLog();
        $sendLog->setBatchId($batchId);
        $sendLog->setMobile($mobile);
        $sendLog->setZone('86');
        $sendLog->setMemo('Test SMS log entry');
        $sendLog->setStatus(SmsReceiveEnum::SENT);

        $this->em->persist($sendLog);
        $this->em->flush();

        return $sendLog;
    }

    protected function getRepository(): SendLogRepository
    {
        return $this->repository;
    }
}
