<?php

namespace DiyFormBundle\Entity;

use AntdCpBundle\Builder\Field\BraftEditor;
use Carbon\Carbon;
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
use Tourze\DoctrineIpBundle\Attribute\CreateIpColumn;
use Tourze\DoctrineIpBundle\Attribute\UpdateIpColumn;
use Tourze\DoctrineTimestampBundle\Attribute\CreateTimeColumn;
use Tourze\DoctrineTimestampBundle\Attribute\UpdateTimeColumn;
use Tourze\DoctrineTrackBundle\Attribute\TrackColumn;
use Tourze\DoctrineUserBundle\Attribute\CreatedByColumn;
use Tourze\DoctrineUserBundle\Attribute\UpdatedByColumn;
use Tourze\EasyAdmin\Attribute\Action\Creatable;
use Tourze\EasyAdmin\Attribute\Action\CurdAction;
use Tourze\EasyAdmin\Attribute\Action\Deletable;
use Tourze\EasyAdmin\Attribute\Action\Editable;
use Tourze\EasyAdmin\Attribute\Column\BoolColumn;
use Tourze\EasyAdmin\Attribute\Column\ExportColumn;
use Tourze\EasyAdmin\Attribute\Column\ListColumn;
use Tourze\EasyAdmin\Attribute\Event\BeforeCreate;
use Tourze\EasyAdmin\Attribute\Event\BeforeEdit;
use Tourze\EasyAdmin\Attribute\Field\FormField;
use Tourze\EasyAdmin\Attribute\Field\RichTextField;
use Tourze\EasyAdmin\Attribute\Filter\Filterable;
use Tourze\EasyAdmin\Attribute\Permission\AsPermission;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Yiisoft\Arrays\ArraySorter;

#[AsPermission(title: '表单配置', titleOverrideEnv: 'PAGE_TITLE_DIY_FORM')]
#[Deletable]
#[Editable]
#[Creatable]
#[ORM\Entity(repositoryClass: FormRepository::class)]
#[ORM\Table(name: 'diy_form_config', options: ['comment' => '表单配置'])]
class Form implements PlainArrayInterface, ApiArrayInterface, \Stringable
{
    #[ListColumn(order: -1)]
    #[ExportColumn]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private ?int $id = 0;

    public function getId(): ?int
    {
        return $this->id;
    }
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

    /**
     * order值大的排序靠前。有效的值范围是[0, 2^32].
     */
    #[IndexColumn]
    #[FormField]
    #[ListColumn(order: 95, sorter: true)]
    #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['default' => '0', 'comment' => '次序值'])]
    private ?int $sortNumber = 0;

    public function getSortNumber(): ?int
    {
        return $this->sortNumber;
    }

    public function setSortNumber(?int $sortNumber): self
    {
        $this->sortNumber = $sortNumber;

        return $this;
    }

    public function retrieveSortableArray(): array
    {
        return [
            'sortNumber' => $this->getSortNumber(),
        ];
    }

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

    #[FormField]
    #[Filterable]
    #[Groups(['restful_read'])]
    #[ListColumn]
    #[ORM\Column(type: Types::STRING, length: 120, unique: true, options: ['comment' => '标题'])]
    private string $title;

    /**
     * @var Collection<Field>
     */
    #[ListColumn(title: '字段/题目', width: 550)]
    #[CurdAction(label: '字段/题目', drawerWidth: '90%')]
    #[Ignore]
    #[ORM\OneToMany(mappedBy: 'form', targetEntity: Field::class, fetch: 'EXTRA_LAZY', orphanRemoval: true, indexBy: 'id')]
    private Collection $fields;

    /**
     * @BraftEditor()
     */
    #[RichTextField]
    #[FormField]
    #[Groups(['restful_read'])]
    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '描述'])]
    private ?string $description = null;

    #[FormField]
    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '备注'])]
    private ?string $remark = null;

    #[FormField(span: 9)]
    #[Groups(['restful_read'])]
    #[ListColumn]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, options: ['comment' => '开始时间'])]
    private ?\DateTimeInterface $startTime = null;

    #[FormField(span: 9)]
    #[Assert\GreaterThan(propertyPath: 'startTime')]
    #[Groups(['restful_read'])]
    #[ListColumn]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, options: ['comment' => '结束时间'])]
    private ?\DateTimeInterface $endTime = null;

    /**
     * @var Collection<Analyse>
     */
    #[Ignore]
    #[CurdAction(label: '分析规则', drawerWidth: '70%')]
    #[ORM\OneToMany(mappedBy: 'form', targetEntity: Analyse::class, orphanRemoval: true)]
    private Collection $analyses;

    /**
     * @var Collection<Record>
     */
    #[Ignore]
    #[CurdAction(label: '提交记录')]
    #[ORM\OneToMany(mappedBy: 'form', targetEntity: Record::class, orphanRemoval: true)]
    private Collection $records;

    #[CreateIpColumn]
    #[ORM\Column(type: Types::STRING, length: 128, nullable: true, options: ['comment' => '创建者IP'])]
    private ?string $createdFromIp = null;

    #[UpdateIpColumn]
    #[ORM\Column(type: Types::STRING, length: 128, nullable: true, options: ['comment' => '更新者IP'])]
    private ?string $updatedFromIp = null;

    public function __construct()
    {
        $this->fields = new ArrayCollection();
        $this->analyses = new ArrayCollection();
        $this->records = new ArrayCollection();
    }

    public function __toString(): string
    {
        if (!$this->getId()) {
            return '';
        }

        return "{$this->getTitle()}({$this->getId()})";
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

    /**
     * @return Collection<int, Field>
     */
    public function getFields(): Collection
    {
        return $this->fields;
    }

    public function addField(Field $field): self
    {
        if (!$this->fields->contains($field)) {
            $this->fields[] = $field;
            $field->setForm($this);
        }

        return $this;
    }

    public function removeField(Field $field): self
    {
        if ($this->fields->removeElement($field)) {
            // set the owning side to null (unless already changed)
            if ($field->getForm() === $this) {
                $field->setForm(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Record>
     */
    public function getRecords(): Collection
    {
        return $this->records;
    }

    public function addRecord(Record $record): self
    {
        if (!$this->records->contains($record)) {
            $this->records[] = $record;
            $record->setForm($this);
        }

        return $this;
    }

    public function removeRecord(Record $record): self
    {
        if ($this->records->removeElement($record)) {
            // set the owning side to null (unless already changed)
            if ($record->getForm() === $this) {
                $record->setForm(null);
            }
        }

        return $this;
    }

    public function getTitle(): string
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

    public function getRemark(): ?string
    {
        return $this->remark;
    }

    public function setRemark(?string $remark): self
    {
        $this->remark = $remark;

        return $this;
    }

    public function getStartTime(): ?\DateTimeInterface
    {
        return $this->startTime;
    }

    public function setStartTime(\DateTimeInterface $startTime): self
    {
        $this->startTime = $startTime;

        return $this;
    }

    public function getEndTime(): ?\DateTimeInterface
    {
        return $this->endTime;
    }

    public function setEndTime(\DateTimeInterface $endTime): self
    {
        $this->endTime = $endTime;

        return $this;
    }

    /**
     * @return array|Field[]
     */
    public function getSortedFields(): array
    {
        // 过滤，只需要有效的
        $list = $this->getFields()
            ->filter(fn (Field $item) => $item->isValid())
            ->toArray();
        // 先通过序号倒序，再根据ID顺序
        ArraySorter::multisort($list, [
            fn (Field $item) => $item->getSortNumber(),
            fn (Field $item) => $item->getId(),
        ], [SORT_DESC, SORT_ASC]);

        return $list;
    }

    /**
     * @return Collection<int, Analyse>
     */
    public function getAnalyses(): Collection
    {
        return $this->analyses;
    }

    public function addAnalysis(Analyse $analysis): self
    {
        if (!$this->analyses->contains($analysis)) {
            $this->analyses[] = $analysis;
            $analysis->setForm($this);
        }

        return $this;
    }

    public function removeAnalysis(Analyse $analysis): self
    {
        if ($this->analyses->removeElement($analysis)) {
            // set the owning side to null (unless already changed)
            if ($analysis->getForm() === $this) {
                $analysis->setForm(null);
            }
        }

        return $this;
    }

    /**
     * @return array|Analyse[]
     */
    public function getSortedAnalyses(): array
    {
        // 过滤，只需要有效的
        $list = $this->getAnalyses()
            ->filter(fn (Analyse $item) => $item->isValid())
            ->toArray();
        // 先通过序号倒序，再根据ID顺序
        ArraySorter::multisort($list, [
            fn (Analyse $item) => $item->getSortNumber(),
            fn (Analyse $item) => $item->getId(),
        ], [SORT_DESC, SORT_ASC]);

        return $list;
    }

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

    public function retrieveApiArray(): array
    {
        $result = $this->retrievePlainArray();
        $result['fields'] = [];
        foreach ($this->getSortedFields() as $field) {
            $result['fields'][] = $field->retrieveApiArray();
        }

        return $result;
    }

    #[BeforeCreate]
    #[BeforeEdit]
    public function beforeCurdSave(array $form): void
    {
        $startTime = Carbon::parse($form['startTime']);
        $endTime = Carbon::parse($form['endTime']);
        if ($startTime->greaterThan($endTime)) {
            throw new ApiException('结束时间不能大于开始时间');
        }
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
