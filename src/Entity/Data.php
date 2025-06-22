<?php

namespace DiyFormBundle\Entity;

use DiyFormBundle\Repository\DataRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\Ignore;
use Tourze\DoctrineIpBundle\Attribute\CreateIpColumn;
use Tourze\DoctrineIpBundle\Attribute\UpdateIpColumn;
use Tourze\DoctrineSnowflakeBundle\Service\SnowflakeIdGenerator;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;
use Yiisoft\Json\Json;

/**
 * 提交数据
 *
 * 一个题目在这里对应一个记录
 * 要注意的是，即使是跳题这里也会有一个记录
 */
#[ORM\Entity(repositoryClass: DataRepository::class)]
#[ORM\Table(name: 'diy_form_data', options: ['comment' => '提交数据'])]
#[ORM\UniqueConstraint(name: 'diy_form_data_idx_uniq', columns: ['record_id', 'field_id'])]
class Data implements \Stringable
{
    use TimestampableAware;
    use BlameableAware;

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(SnowflakeIdGenerator::class)]
    #[ORM\Column(type: Types::BIGINT, nullable: false, options: ['comment' => 'ID'])]
    private ?string $id = null;


    #[Ignore]
    #[ORM\ManyToOne(targetEntity: Record::class, inversedBy: 'datas')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Record $record = null;

    #[Groups(['restful_read'])]
    #[ORM\ManyToOne(targetEntity: Field::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Field $field = null;

    #[Groups(['restful_read'])]
    #[ORM\Column(type: Types::TEXT, options: ['comment' => '输入数据'])]
    private ?string $input = null;

    #[Groups(['restful_read'])]
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '是否跳过'])]
    private ?bool $skip = null;

    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '是否可删除', 'default' => 1])]
    private bool $deletable = true;

    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '回答标签'])]
    private ?array $answerTags = [];

    #[CreateIpColumn]
    #[ORM\Column(type: Types::STRING, length: 128, nullable: true, options: ['comment' => '创建者IP'])]
    private ?string $createdFromIp = null;

    #[UpdateIpColumn]
    #[ORM\Column(type: Types::STRING, length: 128, nullable: true, options: ['comment' => '更新者IP'])]
    private ?string $updatedFromIp = null;

    public function __toString(): string
    {
        if (null === $this->getId()) {
            return '';
        }

        return "{$this->getField()?->getTitle()}: {$this->getInput()}";
    }

    public function getId(): ?string
    {
        return $this->id;
    }


    public function getRecord(): ?Record
    {
        return $this->record;
    }

    public function setRecord(?Record $record): self
    {
        $this->record = $record;

        return $this;
    }

    public function getField(): ?Field
    {
        return $this->field;
    }

    public function setField(?Field $field): self
    {
        $this->field = $field;

        return $this;
    }

    public function getInput(): ?string
    {
        return $this->input;
    }

    /**
     * 前端输入的 Input，可能是数组喔，所以需要兼容一次
     */
    #[Groups(['restful_read'])]
    public function getInputArray(): array
    {
        if (null === $this->getInput()) {
            return [];
        }

        try {
            $input = Json::decode($this->getInput());
        } catch (\JsonException) {
            $input = $this->getInput();
        }

        if (!is_array($input)) {
            $input = [strval($input)];
        }

        return $input;
    }

    public function setInput(string $input): self
    {
        $this->input = $input;

        return $this;
    }

    public function isSkip(): ?bool
    {
        return $this->skip;
    }

    public function setSkip(?bool $skip): self
    {
        $this->skip = $skip;

        return $this;
    }

    public function isDeletable(): ?bool
    {
        return $this->deletable;
    }

    public function setDeletable(bool $deletable): self
    {
        $this->deletable = $deletable;

        return $this;
    }

    public function getAnswerTags(): ?array
    {
        return $this->answerTags;
    }

    public function setAnswerTags(?array $answerTags): self
    {
        $this->answerTags = $answerTags;

        return $this;
    }

    public function getCreatedFromIp(): ?string
    {
        return $this->createdFromIp;
    }

    public function setCreatedFromIp(?string $createdFromIp): self
    {
        $this->createdFromIp = $createdFromIp;

        return $this;
    }

    public function getUpdatedFromIp(): ?string
    {
        return $this->updatedFromIp;
    }

    public function setUpdatedFromIp(?string $updatedFromIp): self
    {
        $this->updatedFromIp = $updatedFromIp;

        return $this;
    }
}
