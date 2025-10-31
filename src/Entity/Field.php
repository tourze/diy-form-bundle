<?php

declare(strict_types=1);

namespace DiyFormBundle\Entity;

use DiyFormBundle\Enum\FieldType;
use DiyFormBundle\Repository\FieldRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\Ignore;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\Arrayable\ApiArrayInterface;
use Tourze\Arrayable\PlainArrayInterface;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineIpBundle\Traits\IpTraceableAware;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineTrackBundle\Attribute\TrackColumn;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;
use Yiisoft\Json\Json;

/**
 * 如果使用场景是调查问卷的话，那么这里的意思实际就是题目.
 *
 * @see https://symfony.com/doc/current/components/expression_language.html
 * @implements PlainArrayInterface<string, mixed>
 * @implements ApiArrayInterface<string, mixed>
 */
#[ORM\Entity(repositoryClass: FieldRepository::class)]
#[ORM\Table(name: 'diy_form_field', options: ['comment' => '字段配置'])]
#[ORM\UniqueConstraint(name: 'diy_form_field_idx_uniq', columns: ['form_id', 'sn'])]
class Field implements \Stringable, PlainArrayInterface, ApiArrayInterface
{
    use TimestampableAware;
    use BlameableAware;
    use IpTraceableAware;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private int $id = 0;

    public function getId(): int
    {
        return $this->id;
    }

    #[Assert\Type(type: 'bool')]
    #[IndexColumn]
    #[TrackColumn]
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '有效', 'default' => 0])]
    private ?bool $valid = false;

    #[Ignore]
    #[ORM\ManyToOne(targetEntity: Form::class, inversedBy: 'fields')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Form $form = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 120)]
    #[Groups(groups: ['restful_read', 'admin_curd'])]
    #[ORM\Column(type: Types::STRING, length: 120, options: ['comment' => '序列号'])]
    private string $sn = '';

    #[Assert\NotNull]
    #[Assert\Choice(callback: [FieldType::class, 'cases'])]
    #[Groups(groups: ['restful_read', 'admin_curd'])]
    #[ORM\Column(type: Types::STRING, length: 100, enumType: FieldType::class, options: ['comment' => '类型'])]
    private ?FieldType $type = null;

    #[Assert\PositiveOrZero]
    #[Groups(groups: ['admin_curd'])]
    #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['comment' => '排序', 'default' => 0])]
    private ?int $sortNumber = null;

    #[Assert\Type(type: 'bool')]
    #[Groups(groups: ['restful_read', 'admin_curd'])]
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '必填'])]
    private ?bool $required = null;

    #[Assert\PositiveOrZero]
    #[Groups(groups: ['restful_read', 'admin_curd'])]
    #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['comment' => '最大输入/选择'])]
    private ?int $maxInput = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    #[Groups(groups: ['restful_read', 'admin_curd'])]
    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => '标题'])]
    private string $title = '';

    #[Assert\Length(max: 255)]
    #[Groups(groups: ['restful_read', 'admin_curd'])]
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '提示文本'])]
    private string $placeholder = '';

    #[Assert\Length(max: 255)]
    #[Groups(groups: ['restful_read', 'admin_curd'])]
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '背景图'])]
    private ?string $bgImage = null;

    /**
     * @var Collection<int, Option>
     */
    #[Groups(groups: ['restful_read', 'admin_curd'])]
    #[ORM\OneToMany(mappedBy: 'field', targetEntity: Option::class, cascade: ['persist'], fetch: 'EXTRA_LAZY', orphanRemoval: true)]
    private Collection $options;

    #[Assert\Length(max: 65535)]
    #[Groups(groups: ['restful_read', 'admin_curd'])]
    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '描述'])]
    private ?string $description = null;

    #[Assert\Length(max: 65535)]
    #[Groups(groups: ['admin_curd'])]
    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '显示规则'])]
    private ?string $showExpression = null;

    #[Assert\Length(max: 65535)]
    #[Groups(groups: ['restful_read', 'admin_curd'])]
    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '额外信息'])]
    private ?string $extra = null;

    public function __construct()
    {
        $this->options = new ArrayCollection();
    }

    public function __toString(): string
    {
        if (0 === $this->getId()) {
            return '';
        }

        $str = "{$this->getType()?->getLabel()} {$this->getTitle()}";
        if (null !== $this->getShowExpression()) {
            $str = "【如果 {$this->getShowExpression()}】{$str}";
        }

        return "{$this->getSn()}.{$str}";
    }

    public function isValid(): ?bool
    {
        return $this->valid;
    }

    public function setValid(?bool $valid): void
    {
        $this->valid = $valid;
    }

    public function getForm(): ?Form
    {
        return $this->form;
    }

    public function setForm(?Form $form): void
    {
        $this->form = $form;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getType(): ?FieldType
    {
        return $this->type;
    }

    public function setType(?FieldType $type): void
    {
        $this->type = $type;
    }

    /**
     * @return Collection<int, Option>
     */
    public function getOptions(): Collection
    {
        return $this->options;
    }

    public function addOption(Option $option): void
    {
        if (!$this->options->contains($option)) {
            $this->options->add($option);
            $option->setField($this);
        }
    }

    public function removeOption(Option $option): void
    {
        if ($this->options->removeElement($option)) {
            // set the owning side to null (unless already changed)
            if ($option->getField() === $this) {
                $option->setField(null);
            }
        }
    }

    public function getSn(): string
    {
        return $this->sn;
    }

    public function setSn(string $sn): void
    {
        $this->sn = $sn;
    }

    public function isRequired(): bool
    {
        return (bool) $this->required;
    }

    public function setRequired(?bool $required): void
    {
        $this->required = $required;
    }

    public function getSortNumber(): ?int
    {
        return $this->sortNumber;
    }

    public function setSortNumber(?int $sortNumber): void
    {
        $this->sortNumber = $sortNumber;
    }

    public function getShowExpression(): ?string
    {
        return $this->showExpression;
    }

    public function setShowExpression(?string $showExpression): void
    {
        $this->showExpression = $showExpression;
    }

    public function getPlaceholder(): string
    {
        return $this->placeholder;
    }

    public function setPlaceholder(string $placeholder): void
    {
        $this->placeholder = $placeholder;
    }

    public function getBgImage(): ?string
    {
        return $this->bgImage;
    }

    public function setBgImage(?string $bgImage): void
    {
        $this->bgImage = $bgImage;
    }

    public function getExtra(): ?string
    {
        return $this->extra;
    }

    /**
     * @return array<string, mixed>
     */
    #[Groups(groups: ['restful_read'])]
    public function getExtraConfig(): array
    {
        if ('' === $this->getExtra() || null === $this->getExtra()) {
            return [];
        }

        try {
            $decoded = Json::decode($this->getExtra());

            if (!is_array($decoded)) {
                return [];
            }

            // 确保是关联数组
            /** @var array<string, mixed> $decoded */
            return $decoded;
        } catch (\Throwable) {
            return [];
        }
    }

    public function setExtra(?string $extra): void
    {
        $this->extra = $extra;
    }

    public function getMaxInput(): ?int
    {
        return $this->maxInput;
    }

    public function setMaxInput(?int $maxInput): void
    {
        $this->maxInput = $maxInput;
    }

    /**
     * @return array<string, mixed>
     */
    public function retrievePlainArray(): array
    {
        $options = [];
        foreach ($this->getOptions() as $option) {
            $options[] = $option->retrievePlainArray();
        }

        return [
            'sn' => $this->getSn(),
            'type' => $this->getType(),
            'required' => $this->isRequired(),
            'maxInput' => $this->getMaxInput(),
            'title' => $this->getTitle(),
            'placeholder' => $this->getPlaceholder(),
            'bgImage' => $this->getBgImage(),
            'options' => $options,
            'description' => $this->getDescription(),
            'id' => $this->getId(),
            'createTime' => $this->getCreateTime()?->format('Y-m-d H:i:s'),
            'updateTime' => $this->getUpdateTime()?->format('Y-m-d H:i:s'),
            'valid' => $this->isValid(),
            'extraConfig' => $this->getExtraConfig(),
            'extra' => $this->getExtra(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function retrieveApiArray(): array
    {
        $options = [];
        foreach ($this->getOptions() as $option) {
            $options[] = $option->retrieveApiArray();
        }

        return [
            'sn' => $this->getSn(),
            'type' => $this->getType(),
            'required' => $this->isRequired(),
            'maxInput' => $this->getMaxInput(),
            'title' => $this->getTitle(),
            'placeholder' => $this->getPlaceholder(),
            'bgImage' => $this->getBgImage(),
            'options' => $options,
            'description' => $this->getDescription(),
            'id' => $this->getId(),
            'createTime' => $this->getCreateTime()?->format('Y-m-d H:i:s'),
            'updateTime' => $this->getUpdateTime()?->format('Y-m-d H:i:s'),
            'valid' => $this->isValid(),
            'extraConfig' => $this->getExtraConfig(),
            'extra' => $this->getExtra(),
        ];
    }
}
