<?php

namespace DiyFormBundle\Entity;

use AntdCpBundle\Builder\Field\BraftEditor;
use AntdCpBundle\Builder\Field\DynamicFieldSet;
use DiyFormBundle\Enum\FieldType;
use DiyFormBundle\Repository\FieldRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\Ignore;
use Tourze\Arrayable\ApiArrayInterface;
use Tourze\Arrayable\PlainArrayInterface;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineIpBundle\Attribute\CreateIpColumn;
use Tourze\DoctrineIpBundle\Attribute\UpdateIpColumn;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineTrackBundle\Attribute\TrackColumn;
use Tourze\DoctrineUserBundle\Attribute\CreatedByColumn;
use Tourze\DoctrineUserBundle\Attribute\UpdatedByColumn;
use Tourze\EasyAdmin\Attribute\Action\Creatable;
use Tourze\EasyAdmin\Attribute\Action\Editable;
use Tourze\EasyAdmin\Attribute\Action\Listable;
use Tourze\EasyAdmin\Attribute\Field\ImagePickerField;
use Tourze\EasyAdmin\Attribute\Field\RichTextField;
use Yiisoft\Json\Json;

/**
 * 如果使用场景是调查问卷的话，那么这里的意思实际就是题目
 *
 * @see https://symfony.com/doc/current/components/expression_language.html
 */
#[Listable(sortColumn: ['sortNumber' => 'DESC', 'id' => 'ASC'])]
#[Creatable(drawerWidth: 1380)]
#[Editable(drawerWidth: 1380)]
#[ORM\Entity(repositoryClass: FieldRepository::class)]
#[ORM\Table(name: 'diy_form_field', options: ['comment' => '字段配置'])]
#[ORM\UniqueConstraint(name: 'diy_form_field_idx_uniq', columns: ['form_id', 'sn'])]
class Field implements \Stringable, PlainArrayInterface, ApiArrayInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private ?int $id = 0;

    public function getId(): ?int
    {
        return $this->id;
    }
    use TimestampableAware;

    #[CreatedByColumn]
    #[ORM\Column(nullable: true, options: ['comment' => '创建人'])]
    private ?string $createdBy = null;

    #[UpdatedByColumn]
    #[ORM\Column(nullable: true, options: ['comment' => '更新人'])]
    private ?string $updatedBy = null;

    #[IndexColumn]
    #[TrackColumn]
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '有效', 'default' => 0])]
    private ?bool $valid = false;

    #[Ignore]
    #[ORM\ManyToOne(targetEntity: Form::class, inversedBy: 'fields')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Form $form = null;

    /**
     * 这个字段实际也可能由前端生成
     * 前端生成的话，那么跳题配置就可以由前端去控制
     * TODO 前端生成的话，需要防止前端乱传东西，确保必须是一个随机字符串.
     */
    #[Groups(['restful_read', 'admin_curd'])]
    #[ORM\Column(type: Types::STRING, length: 120, options: ['comment' => '序列号'])]
    private ?string $sn = '';

    #[Groups(['restful_read', 'admin_curd'])]
    #[ORM\Column(type: Types::STRING, length: 100, enumType: FieldType::class, options: ['comment' => '类型'])]
    private ?FieldType $type = null;

    #[Groups(['admin_curd'])]
    #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['comment' => '排序', 'default' => 0])]
    private ?int $sortNumber = null;

    #[Groups(['restful_read', 'admin_curd'])]
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '必填'])]
    private ?bool $required = null;

    #[Groups(['restful_read', 'admin_curd'])]
    #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['comment' => '最大输入/选择'])]
    private ?int $maxInput = null;

    #[Groups(['restful_read', 'admin_curd'])]
    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => '标题'])]
    private ?string $title = '';

    #[Groups(['restful_read', 'admin_curd'])]
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '提示文本'])]
    private ?string $placeholder = '';

    #[ImagePickerField]
    #[Groups(['restful_read', 'admin_curd'])]
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '背景图'])]
    private ?string $bgImage = null;

    /**
     * 不是所有题型都有选项的.
     *
     * @DynamicFieldSet()
     *
     * @var Collection<Option>
     */
    #[Groups(['restful_read', 'admin_curd'])]
    #[ORM\OneToMany(mappedBy: 'field', targetEntity: Option::class, cascade: ['persist'], fetch: 'EXTRA_LAZY', orphanRemoval: true)]
    private Collection $options;

    /**
     * @BraftEditor()
     */
    #[RichTextField]
    #[Groups(['restful_read', 'admin_curd'])]
    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '描述'])]
    private ?string $description = null;

    #[Groups(['admin_curd'])]
    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '显示规则'])]
    private ?string $showExpression = null;

    #[Groups(['restful_read', 'admin_curd'])]
    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '额外信息'])]
    private ?string $extra = null;

    #[CreateIpColumn]
    #[ORM\Column(type: Types::STRING, length: 128, nullable: true, options: ['comment' => '创建者IP'])]
    private ?string $createdFromIp = null;

    #[UpdateIpColumn]
    #[ORM\Column(type: Types::STRING, length: 128, nullable: true, options: ['comment' => '更新者IP'])]
    private ?string $updatedFromIp = null;

    public function __construct()
    {
        $this->options = new ArrayCollection();
    }

    public function __toString(): string
    {
        if (!$this->getId()) {
            return '';
        }

        $str = "{$this->getType()->getLabel()} {$this->getTitle()}";
        if ($this->getShowExpression()) {
            $str = "【如果 {$this->getShowExpression()}】{$str}";
        }

        return "{$this->getSn()}.{$str}";
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

    public function getForm(): ?Form
    {
        return $this->form;
    }

    public function setForm(?Form $form): self
    {
        $this->form = $form;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getType(): ?FieldType
    {
        return $this->type;
    }

    public function setType(FieldType $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return Collection<int, Option>
     */
    public function getOptions(): Collection
    {
        return $this->options;
    }

    public function addOption(Option $option): self
    {
        if (!$this->options->contains($option)) {
            $this->options[] = $option;
            $option->setField($this);
        }

        return $this;
    }

    public function removeOption(Option $option): self
    {
        if ($this->options->removeElement($option)) {
            // set the owning side to null (unless already changed)
            if ($option->getField() === $this) {
                $option->setField(null);
            }
        }

        return $this;
    }

    public function getSn(): ?string
    {
        return $this->sn;
    }

    public function setSn(string $sn): self
    {
        $this->sn = $sn;

        return $this;
    }

    public function isRequired(): ?bool
    {
        return (bool) $this->required;
    }

    public function setRequired(?bool $required): self
    {
        $this->required = $required;

        return $this;
    }

    public function getSortNumber(): ?int
    {
        return $this->sortNumber;
    }

    public function setSortNumber(?int $sortNumber): self
    {
        $this->sortNumber = $sortNumber;

        return $this;
    }

    public function getShowExpression(): ?string
    {
        return $this->showExpression;
    }

    public function setShowExpression(?string $showExpression): self
    {
        $this->showExpression = $showExpression;

        return $this;
    }

    public function getPlaceholder(): ?string
    {
        return $this->placeholder;
    }

    public function setPlaceholder(?string $placeholder): self
    {
        $this->placeholder = $placeholder;

        return $this;
    }

    public function getBgImage(): ?string
    {
        return $this->bgImage;
    }

    public function setBgImage(?string $bgImage): self
    {
        $this->bgImage = $bgImage;

        return $this;
    }

    public function getExtra(): ?string
    {
        return $this->extra;
    }

    #[Groups('restful_read')]
    public function getExtraConfig(): array
    {
        if (empty($this->getExtra())) {
            return [];
        }

        try {
            return Json::decode($this->getExtra());
        } catch (\Throwable) {
            return [];
        }
    }

    public function setExtra(?string $extra): self
    {
        $this->extra = $extra;

        return $this;
    }

    public function getMaxInput(): ?int
    {
        return $this->maxInput;
    }

    public function setMaxInput(?int $maxInput): self
    {
        $this->maxInput = $maxInput;

        return $this;
    }

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
