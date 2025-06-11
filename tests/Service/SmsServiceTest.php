<?php

namespace DiyFormBundle\Tests\Service;

use DiyFormBundle\Entity\SendLog;
use DiyFormBundle\Entity\SmsDsn;
use DiyFormBundle\Enum\SmsReceiveEnum;
use DiyFormBundle\Repository\SmsDsnRepository;
use DiyFormBundle\Service\SmsService;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Symfony\Component\Notifier\Message\SentMessage;
use Symfony\Component\Notifier\Message\SmsMessage;
use Symfony\Component\Notifier\Transport\TransportInterface;
use Tourze\DoctrineAsyncInsertBundle\Service\AsyncInsertService as DoctrineService;

class SmsServiceTest extends TestCase
{
    private SmsDsnRepository $dsnRepository;
    private DoctrineService $doctrineService;
    private ContainerInterface $container;
    private SmsService $smsService;
    private TransportInterface $transport;

    protected function setUp(): void
    {
        $this->dsnRepository = $this->createMock(SmsDsnRepository::class);
        $this->doctrineService = $this->createMock(DoctrineService::class);
        $this->container = $this->createMock(ContainerInterface::class);
        $this->transport = $this->createMock(TransportInterface::class);
        
        // 更好的方式是直接返回一个已经配置好的容器
        // 第一次调用返回transport_factory，第二次可能调用的是返回的factory的fromString方法
        $this->container->method('get')
            ->willReturnCallback(function ($service) {
                if ($service === 'texter.transport_factory') {
                    $factory = new class($this->transport) {
                        private $transport;
                        
                        public function __construct($transport)
                        {
                            $this->transport = $transport;
                        }
                        
                        public function fromString($dsn)
                        {
                            return $this->transport;
                        }
                    };
                    return $factory;
                }
                return null;
            });
        
        $this->smsService = new SmsService(
            $this->dsnRepository,
            $this->doctrineService,
            $this->container
        );
    }

    public function testSend_成功发送短信()
    {
        // 准备有效的DSN配置
        $validDsn = new SmsDsn();
        $validDsn->setDsn('twilio://sid:token@default?from=+1234567890');
        $validDsn->setWeight(100);
        $validDsn->setValid(true);
        
        // 设置仓库返回有效的DSN列表
        $this->dsnRepository->expects($this->once())
            ->method('findBy')
            ->with(['valid' => true])
            ->willReturn([$validDsn]);
        
        // 创建要发送的短信消息
        $smsMessage = new SmsMessage('+1234567890', '测试短信内容');
        
        // 设置transport可以发送消息
        $sentMessage = $this->createMock(SentMessage::class);
        $this->transport->expects($this->once())
            ->method('send')
            ->willReturn($sentMessage);
        
        // 检查是否异步保存发送日志
        $this->doctrineService->expects($this->once())
            ->method('asyncInsert')
            ->with($this->callback(function ($log) {
                return $log instanceof SendLog
                    && $log->getMobile() === '+1234567890'
                    && $log->getStatus() === SmsReceiveEnum::SENT;
            }));
        
        // 调用并验证结果
        $result = $this->smsService->send($smsMessage);
        $this->assertSame($sentMessage, $result);
    }

    public function testSend_无有效DSN配置()
    {
        // 设置仓库返回空的DSN列表
        $this->dsnRepository->expects($this->once())
            ->method('findBy')
            ->with(['valid' => true])
            ->willReturn([]);
        
        // 创建要发送的短信消息
        $smsMessage = new SmsMessage('+1234567890', '测试短信内容');
        
        // 检查仍然会异步保存发送日志
        $this->doctrineService->expects($this->once())
            ->method('asyncInsert')
            ->with($this->callback(function ($log) {
                return $log instanceof SendLog
                    && $log->getStatus() === SmsReceiveEnum::SENT;
            }));
        
        // 调用并验证结果
        $result = $this->smsService->send($smsMessage);
        $this->assertNull($result);
    }

    public function testSend_使用权重选择DSN配置()
    {
        // 准备两个有效的DSN配置
        $dsn1 = new SmsDsn();
        $dsn1->setDsn('twilio://sid1:token1@default?from=+1111111111');
        $dsn1->setWeight(30);
        $dsn1->setValid(true);
        
        $dsn2 = new SmsDsn();
        $dsn2->setDsn('twilio://sid2:token2@default?from=+2222222222');
        $dsn2->setWeight(70);
        $dsn2->setValid(true);
        
        // 设置仓库返回DSN列表
        $this->dsnRepository->expects($this->once())
            ->method('findBy')
            ->with(['valid' => true])
            ->willReturn([$dsn1, $dsn2]);
        
        // 创建要发送的短信消息
        $smsMessage = new SmsMessage('+1234567890', '测试短信内容');
        
        // 设置传输对象返回已发送消息
        $sentMessage = $this->createMock(SentMessage::class);
        $this->transport->expects($this->once())
            ->method('send')
            ->willReturn($sentMessage);
        
        // 检查是否异步保存发送日志
        $this->doctrineService->expects($this->once())
            ->method('asyncInsert');
        
        // 调用并验证结果
        $result = $this->smsService->send($smsMessage);
        $this->assertSame($sentMessage, $result);
    }

    public function testSend_发送异常仍保存日志()
    {
        // 准备有效的DSN配置
        $validDsn = new SmsDsn();
        $validDsn->setDsn('twilio://sid:token@default?from=+1234567890');
        $validDsn->setWeight(100);
        $validDsn->setValid(true);
        
        // 设置仓库返回有效的DSN列表
        $this->dsnRepository->expects($this->once())
            ->method('findBy')
            ->willReturn([$validDsn]);
        
        // 创建要发送的短信消息
        $smsMessage = new SmsMessage('+1234567890', '测试短信内容');
        
        // 设置传输对象抛出异常
        $this->transport->expects($this->once())
            ->method('send')
            ->willThrowException(new \Exception('发送失败'));
        
        // 检查即使异常也会异步保存发送日志
        $this->doctrineService->expects($this->once())
            ->method('asyncInsert');
        
        // 预期会抛出异常
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('发送失败');
        
        // 调用方法
        $this->smsService->send($smsMessage);
    }
} 