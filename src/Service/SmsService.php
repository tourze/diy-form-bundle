<?php

namespace DiyFormBundle\Service;

use DiyFormBundle\Entity\SendLog;
use DiyFormBundle\Enum\SmsReceiveEnum;
use DiyFormBundle\Repository\SmsDsnRepository;
use Psr\Container\ContainerInterface;
use Symfony\Component\Notifier\Message\SentMessage;
use Symfony\Component\Notifier\Message\SmsMessage;
use Symfony\Component\Notifier\Transport\TransportInterface;
use Symfony\Component\Uid\Uuid;
use Tourze\DoctrineAsyncInsertBundle\Service\AsyncInsertService as DoctrineService;

/**
 * @see https://symfony.com/doc/current/notifier.html#notifier-sms-channel
 */
class SmsService
{
    public function __construct(
        private readonly SmsDsnRepository $dsnRepository,
        private readonly DoctrineService $doctrineService,
        private readonly ContainerInterface $container,
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
        if (empty($dsnList)) {
            return null;
        }

        $totalWeight = 0;
        foreach ($dsnList as $dsn) {
            $totalWeight += $dsn->getWeight();
        }

        $randomWeight = random_int(1, $totalWeight);
        $cumulativeWeight = 0;

        foreach ($dsnList as $dsn) {
            $cumulativeWeight += $dsn->getWeight();

            if ($randomWeight <= $cumulativeWeight) {
                // TODO 因为有一些Transport不一定Symfony官方支持，所以我们还可能需要手工改一次
                return $this->container->get('texter.transport_factory')->fromString($dsn->getDsn());
            }
        }

        return $this->container->get('texter.transport_factory')->fromString('null://null');
    }
}
