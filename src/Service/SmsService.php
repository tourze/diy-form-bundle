<?php

declare(strict_types=1);

namespace DiyFormBundle\Service;

use DiyFormBundle\Entity\SendLog;
use DiyFormBundle\Entity\SmsDsn;
use DiyFormBundle\Enum\SmsReceiveEnum;
use DiyFormBundle\Repository\SmsDsnRepository;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\Notifier\Message\SentMessage;
use Symfony\Component\Notifier\Message\SmsMessage;
use Symfony\Component\Notifier\Transport;
use Symfony\Component\Notifier\Transport\TransportInterface;
use Symfony\Component\Uid\Uuid;
use Tourze\DoctrineAsyncInsertBundle\Service\AsyncInsertService as DoctrineService;

/**
 * @see https://symfony.com/doc/current/notifier.html#notifier-sms-channel
 */
#[Autoconfigure(public: true)]
#[WithMonologChannel(channel: 'diy_form')]
readonly class SmsService
{
    public function __construct(
        private SmsDsnRepository $dsnRepository,
        private DoctrineService $doctrineService,
        private LoggerInterface $logger,
    ) {
    }

    public function send(SmsMessage $message): ?SentMessage
    {
        // 保存发送日志
        $log = new SendLog();
        $log->setBatchId(Uuid::v4()->toRfc4122());
        $log->setMobile($message->getPhone());
        $log->setStatus(SmsReceiveEnum::SENT);

        try {
            return $this->getValidTransport()?->send($message);
        } finally {
            $this->doctrineService->asyncInsert($log);
        }
    }

    private function getValidTransport(): ?TransportInterface
    {
        // 查找所有有效的配置
        $dsnList = $this->dsnRepository->findBy(['valid' => true]);
        if ([] === $dsnList || 0 === count($dsnList)) {
            return null;
        }

        $totalWeight = 0;
        foreach ($dsnList as $dsn) {
            assert($dsn instanceof SmsDsn);
            $totalWeight += $dsn->getWeight() ?? 0;
        }

        $randomWeight = random_int(1, $totalWeight);
        $cumulativeWeight = 0;

        foreach ($dsnList as $dsn) {
            assert($dsn instanceof SmsDsn);
            $cumulativeWeight += $dsn->getWeight() ?? 0;

            if ($randomWeight <= $cumulativeWeight) {
                // TODO 因为有一些Transport不一定Symfony官方支持，所以我们还可能需要手工改一次
                $dsnString = $dsn->getDsn();
                if (null === $dsnString) {
                    continue;
                }

                try {
                    return Transport::fromDsn($dsnString);
                } catch (\Throwable $e) {
                    $this->logger->error('Failed to create transport from DSN', [
                        'dsn' => $dsn->getDsn(),
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }

        try {
            return Transport::fromDsn('null://null');
        } catch (\Throwable $e) {
            return null;
        }
    }
}
