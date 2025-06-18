<?php

namespace DiyFormBundle\Entity;

use DiyFormBundle\Enum\SmsReceiveEnum;
use DiyFormBundle\Repository\SendLogRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineTimestampBundle\Attribute\CreateTimeColumn;
use Tourze\DoctrineTimestampBundle\Attribute\UpdateTimeColumn;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineUserBundle\Attribute\CreatedByColumn;
use Tourze\EasyAdmin\Attribute\Action\BatchDeletable;
use Tourze\EasyAdmin\Attribute\Action\Deletable;
use Tourze\EasyAdmin\Attribute\Permission\AsPermission;

#[AsPermission(title: 'SMS日志')]
#[Deletable]
#[BatchDeletable]
#[ORM\Entity(repositoryClass: SendLogRepository::class)]
#[ORM\Table(name: 'ims_sms_receive_log', options: ['comment' => 'SMS日志'])]
class SendLog
{
    use TimestampableAware;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private ?int $id = 0;

    #[ORM\Column(length: 100, options: ['comment' => '发送批次号'])]
    private string $batchId;

    #[ORM\Column(length: 6, nullable: true, options: ['comment' => '区号'])]
    private ?string $zone = null;

    #[IndexColumn]
    #[ORM\Column(length: 20, options: ['comment' => '手机号码'])]
    private string $mobile;

    #[ORM\Column(length: 100, nullable: true, options: ['comment' => '退回原因'])]
    private ?string $memo = null;

    #[ORM\Column(nullable: true, enumType: SmsReceiveEnum::class, options: ['comment' => '接收状态'])]
    private ?SmsReceiveEnum $status = null;

    #[CreatedByColumn]
    #[ORM\Column(nullable: true, options: ['comment' => '创建人'])]
    private ?string $createdBy = null;

    #[IndexColumn]
    #[CreateTimeColumn]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '创建时间'])]#[UpdateTimeColumn]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '更新时间'])]public function getId(): ?int
    {
        return $this->id;
    }

    public function getBatchId(): string
    {
        return $this->batchId;
    }

    public function setBatchId(string $batchId): static
    {
        $this->batchId = $batchId;

        return $this;
    }

    public function getMobile(): string
    {
        return $this->mobile;
    }

    public function setMobile(string $mobile): static
    {
        $this->mobile = $mobile;

        return $this;
    }

    public function getMemo(): ?string
    {
        return $this->memo;
    }

    public function setMemo(?string $memo): static
    {
        $this->memo = $memo;

        return $this;
    }

    public function getStatus(): ?SmsReceiveEnum
    {
        return $this->status;
    }

    public function setStatus(?SmsReceiveEnum $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getZone(): ?string
    {
        return $this->zone;
    }

    public function setZone(?string $zone): static
    {
        $this->zone = $zone;

        return $this;
    }

    public function setCreatedBy(?string $createdBy): void
    {
        $this->createdBy = $createdBy;
    }

    public function getCreatedBy(): ?string
    {
        return $this->createdBy;
    }}
