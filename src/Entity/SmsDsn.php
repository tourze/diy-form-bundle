<?php

namespace DiyFormBundle\Entity;

use AntdCpBundle\Builder\Action\ModalFormAction;
use AntdCpBundle\Builder\Field\InputTextField;
use AntdCpBundle\Builder\Field\LongTextField;
use AppBundle\Notifier\Message\SmsTemplateMessage;
use DiyFormBundle\Repository\SmsDsnRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Psr\Container\ContainerInterface;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineSnowflakeBundle\Service\SnowflakeIdGenerator;
use Tourze\DoctrineTimestampBundle\Attribute\CreateTimeColumn;
use Tourze\DoctrineTimestampBundle\Attribute\UpdateTimeColumn;
use Tourze\DoctrineTrackBundle\Attribute\TrackColumn;
use Tourze\DoctrineUserBundle\Attribute\CreatedByColumn;
use Tourze\DoctrineUserBundle\Attribute\UpdatedByColumn;
use Tourze\EasyAdmin\Attribute\Action\BatchDeletable;
use Tourze\EasyAdmin\Attribute\Action\Creatable;
use Tourze\EasyAdmin\Attribute\Action\Deletable;
use Tourze\EasyAdmin\Attribute\Action\Editable;
use Tourze\EasyAdmin\Attribute\Action\ListAction;
use Tourze\EasyAdmin\Attribute\Column\BoolColumn;
use Tourze\EasyAdmin\Attribute\Column\ExportColumn;
use Tourze\EasyAdmin\Attribute\Column\ListColumn;
use Tourze\EasyAdmin\Attribute\Field\FormField;
use Tourze\EasyAdmin\Attribute\Filter\Filterable;
use Tourze\EasyAdmin\Attribute\Filter\Keyword;
use Tourze\EasyAdmin\Attribute\Permission\AsPermission;

#[AsPermission(title: 'SMS配置')]
#[Deletable]
#[Editable]
#[Creatable]
#[BatchDeletable]
#[ORM\Entity(repositoryClass: SmsDsnRepository::class)]
#[ORM\Table(name: 'sms_dsn', options: ['comment' => 'SMS配置'])]
class SmsDsn
{
    #[Filterable]
    #[IndexColumn]
    #[ListColumn(order: 98, sorter: true)]
    #[ExportColumn]
    #[CreateTimeColumn]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '创建时间'])]
    private ?\DateTimeInterface $createTime = null;

    #[UpdateTimeColumn]
    #[ListColumn(order: 99, sorter: true)]
    #[Filterable]
    #[ExportColumn]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '更新时间'])]
    private ?\DateTimeInterface $updateTime = null;

    public function setCreateTime(?\DateTimeInterface $createdAt): void
    {
        $this->createTime = $createdAt;
    }

    public function getCreateTime(): ?\DateTimeInterface
    {
        return $this->createTime;
    }

    public function setUpdateTime(?\DateTimeInterface $updateTime): void
    {
        $this->updateTime = $updateTime;
    }

    public function getUpdateTime(): ?\DateTimeInterface
    {
        return $this->updateTime;
    }

    #[ExportColumn]
    #[ListColumn(order: -1, sorter: true)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(SnowflakeIdGenerator::class)]
    #[ORM\Column(type: Types::BIGINT, nullable: false, options: ['comment' => 'ID'])]
    private ?string $id = null;

    #[CreatedByColumn]
    #[ORM\Column(nullable: true, options: ['comment' => '创建人'])]
    private ?string $createdBy = null;

    #[UpdatedByColumn]
    #[ORM\Column(nullable: true, options: ['comment' => '更新人'])]
    private ?string $updatedBy = null;

    #[BoolColumn]
    #[IndexColumn]
    #[TrackColumn]
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '有效', 'default' => 0])]
    #[ListColumn(order: 97)]
    #[FormField(order: 97)]
    private ?bool $valid = false;

    #[IndexColumn]
    #[FormField]
    #[Keyword]
    #[ListColumn]
    #[ORM\Column(type: Types::STRING, length: 100, options: ['comment' => '名称'])]
    private ?string $name = null;

    #[FormField]
    #[Keyword]
    #[ListColumn]
    #[ORM\Column(type: Types::TEXT, options: ['comment' => 'DSN'])]
    private ?string $dsn = null;

    #[FormField]
    #[ListColumn]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '权重'])]
    private ?int $weight = null;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setCreatedBy(?string $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getCreatedBy(): ?string
    {
        return $this->createdBy;
    }

    public function setUpdatedBy(?string $updatedBy): self
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    public function getUpdatedBy(): ?string
    {
        return $this->updatedBy;
    }

    public function isValid(): ?bool
    {
        return $this->valid;
    }

    public function setValid(?bool $valid): self
    {
        $this->valid = $valid;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDsn(): ?string
    {
        return $this->dsn;
    }

    public function setDsn(string $dsn): self
    {
        $this->dsn = $dsn;

        return $this;
    }

    public function getWeight(): ?int
    {
        return $this->weight;
    }

    public function setWeight(int $weight): self
    {
        $this->weight = $weight;

        return $this;
    }

    #[ListAction(title: '测试模板消息')]
    public function genSendTemplateBtn(): ModalFormAction
    {
        return ModalFormAction::gen()
            ->setFormTitle('测试模板消息')
            ->setLabel('测试模板消息')
            ->setFormFields([
                InputTextField::gen()->setId('phoneNumber')->setLabel('手机号码'),
                InputTextField::gen()->setId('signName')->setLabel('签名'),
                InputTextField::gen()->setId('templateCode')->setLabel('模板'),
                LongTextField::gen()
                    ->setId('templateParam')
                    ->setLabel('模板参数')
                    ->setInputProps([
                        'placeholder' => '拼接成URL参数形式，如code=1&xx=2',
                    ]),
            ])
            ->setCallback(function (
                array $form,
                array $record,
                ContainerInterface $container,
            ) {
                $sms = new SmsTemplateMessage(
                    $form['phoneNumber'],
                    $form['phoneNumber'],
                );

                $sms->setTemplateCode($form['templateCode']);
                $sms->setSignName($form['signName']);
                parse_str((string) $form['templateParam'], $templateParams);
                $sms->setTemplateParam($templateParams);

                $container->get('texter.transport_factory')->fromString($this->getDsn())->send($sms);

                return [
                    '__message' => '发送成功',
                ];
            });
    }
}
