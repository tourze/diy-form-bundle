<?php

namespace DiyFormBundle\Entity;

use AntdCpBundle\Builder\Field\LongTextField;
use DiyFormBundle\Enum\FieldType;
use DiyFormBundle\Repository\OptionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\Ignore;
use Tourze\Arrayable\ApiArrayInterface;
use Tourze\Arrayable\PlainArrayInterface;
use Tourze\DoctrineIpBundle\Attribute\CreateIpColumn;
use Tourze\DoctrineIpBundle\Attribute\UpdateIpColumn;
use Tourze\DoctrineSnowflakeBundle\Attribute\SnowflakeColumn;
use Tourze\DoctrineSnowflakeBundle\Service\SnowflakeIdGenerator;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineUserBundle\Attribute\CreatedByColumn;
use Tourze\DoctrineUserBundle\Attribute\UpdatedByColumn;
use Tourze\EasyAdmin\Attribute\Field\ImagePickerField;

#[ORM\Entity(repositoryClass: OptionRepository::class)]
#[ORM\Table(name: 'diy_form_option', options: ['comment' => '选项'])]
class Option implements \Stringable, PlainArrayInterface, ApiArrayInterface
{
    use TimestampableAware;

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

    #[Ignore]
    #[ORM\ManyToOne(targetEntity: Field::class, inversedBy: 'options')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Field $field = null;

    #[Groups(['restful_read', 'admin_curd'])]
    #[SnowflakeColumn]
    #[ORM\Column(type: Types::STRING, length: 120, options: ['comment' => '序列号'])]
    private ?string $sn = '';

    /**
     * @LongTextField()
     */
    #[Groups(['restful_read', 'admin_curd'])]
    #[ORM\Column(type: Types::STRING, length: 1000, options: ['comment' => '选项文本'])]
    private ?string $text = null;

    /**
     * @LongTextField()
     */
    #[Groups(['restful_read', 'admin_curd'])]
    #[ORM\Column(type: Types::STRING, length: 1000, nullable: true, options: ['comment' => '说明文本'])]
    private ?string $description = '';

    /**
     * @LongTextField()
     */
    #[Groups(['admin_curd'])]
    #[ORM\Column(type: Types::STRING, length: 600, nullable: true, options: ['comment' => '标签'])]
    private ?string $tags = null;

    #[Groups(['admin_curd'])]
    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '显示规则'])]
    private ?string $showExpression = null;

    /**
     * 参考了问卷星的设计 https://www.wjx.cn/help/help.aspx?helpid=149.
     *
     * @LongTextField()
     */
    #[Groups(['restful_read', 'admin_curd'])]
    #[ORM\Column(type: Types::STRING, length: 1000, nullable: true, options: ['comment' => '互斥分组'])]
    private ?string $mutex = null;

    #[Groups(['restful_read', 'admin_curd'])]
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '自由输入'])]
    private ?bool $allowInput = false;

    #[Groups(['restful_read', 'admin_curd'])]
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '正确答案'])]
    private ?bool $answer = false;

    #[ImagePickerField]
    #[Groups(['restful_read', 'admin_curd'])]
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => 'ICON'])]
    private ?string $icon = null;

    #[ImagePickerField]
    #[Groups(['restful_read', 'admin_curd'])]
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '选中态ICON'])]
    private ?string $selectedIcon = null;

    #[CreateIpColumn]
    #[ORM\Column(type: Types::STRING, length: 128, nullable: true, options: ['comment' => '创建者IP'])]
    private ?string $createdFromIp = null;

    #[UpdateIpColumn]
    #[ORM\Column(type: Types::STRING, length: 128, nullable: true, options: ['comment' => '更新者IP'])]
    private ?string $updatedFromIp = null;

    public function __toString(): string
    {
        if (!$this->getId()) {
            return '';
        }

        $str = $this->getText();
        if ($this->getTags()) {
            $str = "[{$this->getTags()}]{$str}";
        }

        if (FieldType::SINGLE_SELECT === $this->getField()?->getType()) {
            $str = "○{$str}";
        }

        if (FieldType::RADIO_SELECT === $this->getField()?->getType()) {
            $str = "○{$str}";
        }

        if (FieldType::MULTIPLE_SELECT === $this->getField()?->getType()) {
            $str = "□{$str}";
        }

        if (FieldType::CHECKBOX_SELECT === $this->getField()?->getType()) {
            $str = "□{$str}";
        }

        if ($this->getShowExpression()) {
            $str = "{$str}。显示规则：{$this->getShowExpression()}";
        }

        return $str;
    }

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

    public function getField(): ?Field
    {
        return $this->field;
    }

    public function setField(?Field $field): self
    {
        $this->field = $field;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;

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

    public function isAllowInput(): ?bool
    {
        return $this->allowInput;
    }

    public function setAllowInput(?bool $allowInput): self
    {
        $this->allowInput = $allowInput;

        return $this;
    }

    public function isAnswer(): ?bool
    {
        return $this->answer;
    }

    public function setAnswer(?bool $answer): self
    {
        $this->answer = $answer;

        return $this;
    }

    public function getTags(): ?string
    {
        return $this->tags;
    }

    public function getTagList(): array
    {
        if (empty($this->getTags())) {
            return [];
        }

        return explode(',', $this->getTags());
    }

    public function setTags(?string $tags): self
    {
        $this->tags = $tags;

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

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function setIcon(?string $icon): self
    {
        $this->icon = $icon;

        return $this;
    }

    public function getSelectedIcon(): ?string
    {
        return $this->selectedIcon;
    }

    public function setSelectedIcon(?string $selectedIcon): self
    {
        $this->selectedIcon = $selectedIcon;

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

    public function getMutex(): ?string
    {
        return $this->mutex;
    }

    public function setMutex(?string $mutex): self
    {
        $this->mutex = $mutex;

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

    public function retrievePlainArray(): array
    {
        return [
            'id' => $this->getId(),
            'sn' => $this->getSn(),
            'text' => $this->getText(),
            'description' => $this->getDescription(),
            'tags' => $this->getTags(),
            'showExpression' => $this->getShowExpression(),
            'mutex' => $this->getMutex(),
            'allowInput' => $this->isAllowInput(),
            'answer' => $this->isAnswer(),
            'icon' => $this->getIcon(),
            'selectedIcon' => $this->getSelectedIcon(),
            'createTime' => $this->getCreateTime()?->format('Y-m-d H:i:s'),
        ];
    }

    public function retrieveApiArray(): array
    {
        return [
            'id' => $this->getId(),
            'createTime' => $this->getCreateTime()?->format('Y-m-d H:i:s'),
            'updateTime' => $this->getUpdateTime()?->format('Y-m-d H:i:s'),
            'sn' => $this->getSn(),
            'text' => $this->getText(),
            'description' => $this->getDescription(),
            'mutex' => $this->getMutex(),
            'allowInput' => $this->isAllowInput(),
            'answer' => $this->isAnswer(),
            'icon' => $this->getIcon(),
            'selectedIcon' => $this->getSelectedIcon(),
        ];
    }
}
