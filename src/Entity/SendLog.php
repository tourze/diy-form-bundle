<?php

declare(strict_types=1);

namespace DiyFormBundle\Entity;

use DiyFormBundle\Enum\SmsReceiveEnum;
use DiyFormBundle\Repository\SendLogRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;

#[ORM\Entity(repositoryClass: SendLogRepository::class)]
#[ORM\Table(name: 'ims_sms_receive_log', options: ['comment' => 'SMS日志'])]
class SendLog implements \Stringable
{
    use TimestampableAware;
    use BlameableAware;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private int $id = 0;

    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    #[ORM\Column(length: 100, options: ['comment' => '发送批次号'])]
    private string $batchId;

    #[Assert\Length(max: 6)]
    #[ORM\Column(length: 6, nullable: true, options: ['comment' => '区号'])]
    private ?string $zone = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 20)]
    #[Assert\Regex(pattern: '/^1[3-9]\d{9}$/', message: '手机号码格式不正确')]
    #[IndexColumn]
    #[ORM\Column(length: 20, options: ['comment' => '手机号码'])]
    private string $mobile;

    #[Assert\Length(max: 100)]
    #[ORM\Column(length: 100, nullable: true, options: ['comment' => '退回原因'])]
    private ?string $memo = null;

    #[Assert\Choice(callback: [SmsReceiveEnum::class, 'cases'])]
    #[ORM\Column(nullable: true, enumType: SmsReceiveEnum::class, options: ['comment' => '接收状态'])]
    private ?SmsReceiveEnum $status = null;

    public function __toString(): string
    {
        if (0 === $this->getId()) {
            return '';
        }

        $statusStr = null !== $this->getStatus() ? $this->getStatus()->value : '未知';

        return "SMS日志#{$this->getId()} - {$this->getMobile()} - {$statusStr}";
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getBatchId(): string
    {
        return $this->batchId;
    }

    public function setBatchId(string $batchId): void
    {
        $this->batchId = $batchId;
    }

    public function getMobile(): string
    {
        return $this->mobile;
    }

    public function setMobile(string $mobile): void
    {
        $this->mobile = $mobile;
    }

    public function getMemo(): ?string
    {
        return $this->memo;
    }

    public function setMemo(?string $memo): void
    {
        $this->memo = $memo;
    }

    public function getStatus(): ?SmsReceiveEnum
    {
        return $this->status;
    }

    public function setStatus(?SmsReceiveEnum $status): void
    {
        $this->status = $status;
    }

    public function getZone(): ?string
    {
        return $this->zone;
    }

    public function setZone(?string $zone): void
    {
        $this->zone = $zone;
    }
}
