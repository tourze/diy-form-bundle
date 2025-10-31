<?php

declare(strict_types=1);

namespace DiyFormBundle\Entity;

use DiyFormBundle\Repository\AnalyseRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\Ignore;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\Arrayable\ApiArrayInterface;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineIpBundle\Traits\IpTraceableAware;
use Tourze\DoctrineSnowflakeBundle\Traits\SnowflakeKeyAware;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineTrackBundle\Attribute\TrackColumn;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;

/**
 * @implements ApiArrayInterface<string, mixed>
 */
#[ORM\Entity(repositoryClass: AnalyseRepository::class)]
#[ORM\Table(name: 'diy_form_analyse', options: ['comment' => '分析规则'])]
class Analyse implements \Stringable, ApiArrayInterface
{
    use TimestampableAware;
    use BlameableAware;
    use SnowflakeKeyAware;
    use IpTraceableAware;

    #[Assert\Length(max: 65535)]
    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '备注', 'default' => ''])]
    private ?string $remark = null;

    public function getRemark(): ?string
    {
        return $this->remark;
    }

    public function setRemark(?string $remark): void
    {
        $this->remark = $remark;
    }

    #[Assert\PositiveOrZero]
    #[IndexColumn]
    #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['default' => '0', 'comment' => '次序值'])]
    private ?int $sortNumber = 0;

    public function getSortNumber(): ?int
    {
        return $this->sortNumber;
    }

    public function setSortNumber(?int $sortNumber): void
    {
        $this->sortNumber = $sortNumber;
    }

    /**
     * @return array<string, int|null>
     */
    public function retrieveSortableArray(): array
    {
        return [
            'sortNumber' => $this->getSortNumber(),
        ];
    }

    #[Assert\Type(type: 'bool')]
    #[IndexColumn]
    #[TrackColumn]
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '有效', 'default' => 0])]
    private ?bool $valid = false;

    #[Ignore]
    #[ORM\ManyToOne(targetEntity: Form::class, inversedBy: 'analyses')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Form $form = null;

    #[Assert\Length(max: 200)]
    #[Groups(groups: ['restful_read'])]
    #[ORM\Column(type: Types::STRING, length: 200, nullable: true, options: ['comment' => '分类', 'default' => '默认'])]
    private ?string $category = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 200)]
    #[Groups(groups: ['restful_read'])]
    #[ORM\Column(type: Types::STRING, length: 200, options: ['comment' => '标题'])]
    private string $title = '';

    #[Assert\NotBlank]
    #[Assert\Length(max: 2000)]
    #[ORM\Column(type: Types::STRING, length: 2000, options: ['comment' => '判断条件'])]
    private string $rule = '';

    #[Assert\NotBlank]
    #[Assert\Length(max: 65535)]
    #[Groups(groups: ['restful_read'])]
    #[ORM\Column(type: Types::TEXT, options: ['comment' => '结果'])]
    private string $result = '';

    #[Assert\Length(max: 255)]
    #[Groups(groups: ['restful_read'])]
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '缩略图'])]
    private ?string $thumb = null;

    public function __toString(): string
    {
        if (null === $this->getId()) {
            return '';
        }

        return "#{$this->getId()}[" . ($this->getCategory() ?? '') . "] {$this->getTitle()}";
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

    public function getRule(): string
    {
        return $this->rule;
    }

    public function setRule(string $rule): void
    {
        $this->rule = $rule;
    }

    public function getResult(): string
    {
        return $this->result;
    }

    public function setResult(string $result): void
    {
        $this->result = $result;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(?string $category): void
    {
        $this->category = $category;
    }

    public function getThumb(): ?string
    {
        return $this->thumb;
    }

    public function setThumb(?string $thumb): void
    {
        $this->thumb = $thumb;
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
            'category' => $this->getCategory(),
            'title' => $this->getTitle(),
            'result' => $this->getResult(),
            'thumb' => $this->getThumb(),
        ];
    }
}
