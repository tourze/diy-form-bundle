<?php

declare(strict_types=1);

namespace DiyFormBundle\Entity;

use Carbon\CarbonImmutable;
use DiyFormBundle\Repository\FormRepository;
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
use Yiisoft\Arrays\ArraySorter;

/**
 * @implements ApiArrayInterface<string, mixed>
 * @implements PlainArrayInterface<string, mixed>
 */
#[ORM\Entity(repositoryClass: FormRepository::class)]
#[ORM\Table(name: 'diy_form_config', options: ['comment' => '表单配置'])]
class Form implements PlainArrayInterface, ApiArrayInterface, \Stringable
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

    #[Assert\NotBlank]
    #[Assert\Length(max: 120)]
    #[Groups(groups: ['restful_read'])]
    #[ORM\Column(type: Types::STRING, length: 120, unique: true, options: ['comment' => '标题'])]
    private string $title;

    /**
     * @var Collection<int, Field>
     */
    #[Ignore]
    #[ORM\OneToMany(mappedBy: 'form', targetEntity: Field::class, fetch: 'EXTRA_LAZY', orphanRemoval: true, indexBy: 'id')]
    private Collection $fields;

    #[Assert\Length(max: 65535)]
    #[Groups(groups: ['restful_read'])]
    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '描述'])]
    private ?string $description = null;

    #[Assert\Length(max: 65535)]
    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '备注'])]
    private ?string $remark = null;

    #[Assert\NotNull]
    #[Groups(groups: ['restful_read'])]
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, options: ['comment' => '开始时间'])]
    private ?\DateTimeInterface $startTime = null;

    #[Assert\NotNull]
    #[Assert\GreaterThan(propertyPath: 'startTime')]
    #[Groups(groups: ['restful_read'])]
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, options: ['comment' => '结束时间'])]
    private ?\DateTimeInterface $endTime = null;

    /**
     * @var Collection<int, Analyse>
     */
    #[Ignore]
    #[ORM\OneToMany(mappedBy: 'form', targetEntity: Analyse::class, orphanRemoval: true)]
    private Collection $analyses;

    /**
     * @var Collection<int, Record>
     */
    #[Ignore]
    #[ORM\OneToMany(mappedBy: 'form', targetEntity: Record::class, orphanRemoval: true)]
    private Collection $records;

    public function __construct()
    {
        $this->fields = new ArrayCollection();
        $this->analyses = new ArrayCollection();
        $this->records = new ArrayCollection();
    }

    public function __toString(): string
    {
        if (0 === $this->getId()) {
            return '';
        }

        return "{$this->getTitle()}({$this->getId()})";
    }

    public function isValid(): ?bool
    {
        return $this->valid;
    }

    public function setValid(?bool $valid): void
    {
        $this->valid = $valid;
    }

    /**
     * @return Collection<int, Field>
     */
    public function getFields(): Collection
    {
        return $this->fields;
    }

    public function addField(Field $field): void
    {
        if (!$this->fields->contains($field)) {
            $this->fields->add($field);
            $field->setForm($this);
        }
    }

    public function removeField(Field $field): void
    {
        if ($this->fields->removeElement($field)) {
            // set the owning side to null (unless already changed)
            if ($field->getForm() === $this) {
                $field->setForm(null);
            }
        }
    }

    /**
     * @return Collection<int, Record>
     */
    public function getRecords(): Collection
    {
        return $this->records;
    }

    public function addRecord(Record $record): void
    {
        if (!$this->records->contains($record)) {
            $this->records->add($record);
            $record->setForm($this);
        }
    }

    public function removeRecord(Record $record): void
    {
        if ($this->records->removeElement($record)) {
            // set the owning side to null (unless already changed)
            if ($record->getForm() === $this) {
                $record->setForm(null);
            }
        }
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

    public function getRemark(): ?string
    {
        return $this->remark;
    }

    public function setRemark(?string $remark): void
    {
        $this->remark = $remark;
    }

    public function getStartTime(): ?\DateTimeInterface
    {
        return $this->startTime;
    }

    public function setStartTime(\DateTimeInterface $startTime): void
    {
        $this->startTime = $startTime;
    }

    public function getEndTime(): ?\DateTimeInterface
    {
        return $this->endTime;
    }

    public function setEndTime(\DateTimeInterface $endTime): void
    {
        $this->endTime = $endTime;
    }

    /**
     * @return list<Field>
     */
    public function getSortedFields(): array
    {
        // 过滤，只需要有效的
        $list = $this->getFields()
            ->filter(fn (Field $item, int $key): bool => true === $item->isValid())
            ->toArray()
        ;
        // 先通过序号倒序，再根据ID顺序
        ArraySorter::multisort($list, [
            fn (Field $item) => $item->getSortNumber(),
            fn (Field $item) => $item->getId(),
        ], [SORT_DESC, SORT_ASC]);

        /** @var list<Field> */
        return array_values($list);
    }

    /**
     * @return Collection<int, Analyse>
     */
    public function getAnalyses(): Collection
    {
        return $this->analyses;
    }

    public function addAnalysis(Analyse $analysis): void
    {
        if (!$this->analyses->contains($analysis)) {
            $this->analyses->add($analysis);
            $analysis->setForm($this);
        }
    }

    public function removeAnalysis(Analyse $analysis): void
    {
        if ($this->analyses->removeElement($analysis)) {
            // set the owning side to null (unless already changed)
            if ($analysis->getForm() === $this) {
                $analysis->setForm(null);
            }
        }
    }

    /**
     * @return list<Analyse>
     */
    public function getSortedAnalyses(): array
    {
        // 过滤，只需要有效的
        $list = $this->getAnalyses()
            ->filter(fn (Analyse $item, int $key): bool => true === $item->isValid())
            ->toArray()
        ;
        // 先通过序号倒序，再根据ID顺序
        ArraySorter::multisort($list, [
            fn (Analyse $item) => $item->getSortNumber(),
            fn (Analyse $item) => $item->getId(),
        ], [SORT_DESC, SORT_ASC]);

        /** @var list<Analyse> */
        return array_values($list);
    }

    /**
     * @return array<string, mixed>
     */
    public function retrievePlainArray(): array
    {
        return [
            'id' => $this->getId(),
            'title' => $this->getTitle(),
            'description' => $this->getDescription(),
            'startTime' => $this->getStartTime()?->format('Y-m-d H:i:s'),
            'endTime' => $this->getEndTime()?->format('Y-m-d H:i:s'),
            'createTime' => $this->getCreateTime()?->format('Y-m-d H:i:s'),
            'updateTime' => $this->getUpdateTime()?->format('Y-m-d H:i:s'),
            'valid' => $this->isValid(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function retrieveApiArray(): array
    {
        $result = $this->retrievePlainArray();
        $result['fields'] = [];
        foreach ($this->getSortedFields() as $field) {
            $result['fields'][] = $field->retrieveApiArray();
        }

        return $result;
    }

    /**
     * @param array<string, mixed> $form
     */
    public function beforeCurdSave(array $form): void
    {
        $startTime = CarbonImmutable::parse(self::convertTimeToString($form['startTime']));
        $endTime = CarbonImmutable::parse(self::convertTimeToString($form['endTime']));
        if ($startTime->greaterThan($endTime)) {
            throw new \InvalidArgumentException('结束时间不能大于开始时间');
        }
    }

    /**
     * 安全地将时间值转换为字符串
     */
    private static function convertTimeToString(mixed $value): string
    {
        if (is_string($value)) {
            return $value;
        }

        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d H:i:s');
        }

        if (is_null($value)) {
            return '';
        }

        if (is_scalar($value)) {
            return (string) $value;
        }

        // 对于复杂类型，尝试JSON编码
        return json_encode($value, JSON_THROW_ON_ERROR);
    }
}
