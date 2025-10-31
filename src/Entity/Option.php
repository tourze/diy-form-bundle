<?php

declare(strict_types=1);

namespace DiyFormBundle\Entity;

use DiyFormBundle\Enum\FieldType;
use DiyFormBundle\Repository\OptionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\Ignore;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\Arrayable\ApiArrayInterface;
use Tourze\Arrayable\PlainArrayInterface;
use Tourze\DoctrineIpBundle\Traits\IpTraceableAware;
use Tourze\DoctrineSnowflakeBundle\Attribute\SnowflakeColumn;
use Tourze\DoctrineSnowflakeBundle\Traits\SnowflakeKeyAware;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;

/**
 * @implements ApiArrayInterface<string, mixed>
 * @implements PlainArrayInterface<string, mixed>
 */
#[ORM\Entity(repositoryClass: OptionRepository::class)]
#[ORM\Table(name: 'diy_form_option', options: ['comment' => '选项'])]
class Option implements \Stringable, PlainArrayInterface, ApiArrayInterface
{
    use TimestampableAware;
    use BlameableAware;
    use SnowflakeKeyAware;
    use IpTraceableAware;

    #[Ignore]
    #[ORM\ManyToOne(targetEntity: Field::class, inversedBy: 'options')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Field $field = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 120)]
    #[Groups(groups: ['restful_read', 'admin_curd'])]
    #[SnowflakeColumn]
    #[ORM\Column(type: Types::STRING, length: 120, options: ['comment' => '序列号'])]
    private string $sn = '';

    #[Assert\NotBlank]
    #[Assert\Length(max: 1000)]
    #[Groups(groups: ['restful_read', 'admin_curd'])]
    #[ORM\Column(type: Types::STRING, length: 1000, options: ['comment' => '选项文本'])]
    private ?string $text = null;

    #[Assert\Length(max: 1000)]
    #[Groups(groups: ['restful_read', 'admin_curd'])]
    #[ORM\Column(type: Types::STRING, length: 1000, nullable: true, options: ['comment' => '说明文本'])]
    private ?string $description = '';

    #[Assert\Length(max: 600)]
    #[Groups(groups: ['admin_curd'])]
    #[ORM\Column(type: Types::STRING, length: 600, nullable: true, options: ['comment' => '标签'])]
    private ?string $tags = null;

    #[Assert\Length(max: 65535)]
    #[Groups(groups: ['admin_curd'])]
    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '显示规则'])]
    private ?string $showExpression = null;

    #[Assert\Length(max: 1000)]
    #[Groups(groups: ['restful_read', 'admin_curd'])]
    #[ORM\Column(type: Types::STRING, length: 1000, nullable: true, options: ['comment' => '互斥分组'])]
    private ?string $mutex = null;

    #[Assert\Type(type: 'bool')]
    #[Groups(groups: ['restful_read', 'admin_curd'])]
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '自由输入'])]
    private ?bool $allowInput = false;

    #[Assert\Type(type: 'bool')]
    #[Groups(groups: ['restful_read', 'admin_curd'])]
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '正确答案'])]
    private ?bool $answer = false;

    #[Assert\Length(max: 255)]
    #[Groups(groups: ['restful_read', 'admin_curd'])]
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => 'ICON'])]
    private ?string $icon = null;

    #[Assert\Length(max: 255)]
    #[Groups(groups: ['restful_read', 'admin_curd'])]
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '选中态ICON'])]
    private ?string $selectedIcon = null;

    public function __toString(): string
    {
        if (null === $this->getId()) {
            return '';
        }

        $str = $this->getText() ?? '';
        if (null !== $this->getTags()) {
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

        if (null !== $this->getShowExpression()) {
            $str = "{$str}。显示规则：{$this->getShowExpression()}";
        }

        return $str;
    }

    public function getField(): ?Field
    {
        return $this->field;
    }

    public function setField(?Field $field): void
    {
        $this->field = $field;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(?string $text): void
    {
        $this->text = $text;
    }

    public function getSn(): ?string
    {
        return $this->sn;
    }

    public function setSn(string $sn): void
    {
        $this->sn = $sn;
    }

    public function isAllowInput(): ?bool
    {
        return $this->allowInput;
    }

    public function setAllowInput(?bool $allowInput): void
    {
        $this->allowInput = $allowInput;
    }

    public function isAnswer(): ?bool
    {
        return $this->answer;
    }

    public function setAnswer(?bool $answer): void
    {
        $this->answer = $answer;
    }

    public function getTags(): ?string
    {
        return $this->tags;
    }

    /**
     * @return list<string>
     */
    public function getTagList(): array
    {
        $tags = $this->getTags();
        if (null === $tags || '' === $tags) {
            return [];
        }

        return explode(',', $tags);
    }

    public function setTags(?string $tags): void
    {
        $this->tags = $tags;
    }

    public function getShowExpression(): ?string
    {
        return $this->showExpression;
    }

    public function setShowExpression(?string $showExpression): void
    {
        $this->showExpression = $showExpression;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function setIcon(?string $icon): void
    {
        $this->icon = $icon;
    }

    public function getSelectedIcon(): ?string
    {
        return $this->selectedIcon;
    }

    public function setSelectedIcon(?string $selectedIcon): void
    {
        $this->selectedIcon = $selectedIcon;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getMutex(): ?string
    {
        return $this->mutex;
    }

    public function setMutex(?string $mutex): void
    {
        $this->mutex = $mutex;
    }

    /**
     * @return array<string, mixed>
     */
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

    /**
     * @return array<string, mixed>
     */
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
