<?php

declare(strict_types=1);

namespace DiyFormBundle\Entity;

use DiyFormBundle\Repository\DataRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\Ignore;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineIpBundle\Traits\IpTraceableAware;
use Tourze\DoctrineSnowflakeBundle\Traits\SnowflakeKeyAware;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;
use Yiisoft\Json\Json;

/**
 * 提交数据.
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
    use SnowflakeKeyAware;
    use IpTraceableAware;

    #[Ignore]
    #[ORM\ManyToOne(targetEntity: Record::class, inversedBy: 'datas')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Record $record = null;

    #[Groups(groups: ['restful_read'])]
    #[ORM\ManyToOne(targetEntity: Field::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Field $field = null;

    #[Assert\Length(max: 65535)]
    #[Groups(groups: ['restful_read'])]
    #[ORM\Column(type: Types::TEXT, options: ['comment' => '输入数据'])]
    private ?string $input = null;

    #[Assert\Type(type: 'bool')]
    #[Groups(groups: ['restful_read'])]
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '是否跳过'])]
    private ?bool $skip = null;

    #[Assert\Type(type: 'bool')]
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '是否可删除', 'default' => 1])]
    private bool $deletable = true;

    /**
     * @var list<string>|null
     */
    #[Assert\Type(type: 'array')]
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '回答标签'])]
    private ?array $answerTags = null;

    public function __toString(): string
    {
        if (null === $this->getId()) {
            return '';
        }

        return "{$this->getField()?->getTitle()}: {$this->getInput()}";
    }

    public function getRecord(): ?Record
    {
        return $this->record;
    }

    public function setRecord(?Record $record): void
    {
        $this->record = $record;
    }

    public function getField(): ?Field
    {
        return $this->field;
    }

    public function setField(?Field $field): void
    {
        $this->field = $field;
    }

    public function getInput(): ?string
    {
        return $this->input;
    }

    /**
     * 前端输入的 Input，可能是数组喔，所以需要兼容一次
     *
     * @return list<string>
     */
    #[Groups(groups: ['restful_read'])]
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
            $input = [self::convertToString($input)];
        }

        return array_values(array_map(static fn ($item): string => self::convertToString($item), $input));
    }

    /**
     * 安全地将mixed类型转换为字符串
     */
    private static function convertToString(mixed $value): string
    {
        if (is_string($value)) {
            return $value;
        }

        if (is_null($value)) {
            return '';
        }

        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        if (is_scalar($value)) {
            return (string) $value;
        }

        if (is_object($value) && method_exists($value, '__toString')) {
            return (string) $value;
        }

        // 对于无法转换的复杂类型，返回其类型描述
        return sprintf('[%s]', get_debug_type($value));
    }

    public function setInput(?string $input): void
    {
        $this->input = $input;
    }

    public function isSkip(): ?bool
    {
        return $this->skip;
    }

    public function setSkip(?bool $skip): void
    {
        $this->skip = $skip;
    }

    public function isDeletable(): ?bool
    {
        return $this->deletable;
    }

    public function setDeletable(bool $deletable): void
    {
        $this->deletable = $deletable;
    }

    /**
     * @return list<string>|null
     */
    public function getAnswerTags(): ?array
    {
        if (null === $this->answerTags) {
            return null;
        }

        return $this->answerTags;
    }

    /**
     * @param list<string>|null $answerTags
     */
    public function setAnswerTags(?array $answerTags): void
    {
        $this->answerTags = $answerTags;
    }
}
