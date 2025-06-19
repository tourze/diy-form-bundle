<?php

namespace DiyFormBundle\Entity;

use AntdCpBundle\Builder\Field\DateRangePickerField;
use DiyFormBundle\Repository\RecordRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\Ignore;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineIpBundle\Attribute\CreateIpColumn;
use Tourze\DoctrineIpBundle\Attribute\UpdateIpColumn;
use Tourze\DoctrineTimestampBundle\Attribute\CreateTimeColumn;
use Tourze\DoctrineTimestampBundle\Attribute\UpdateTimeColumn;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineUserAgentBundle\Attribute\CreateUserAgentColumn;
use Tourze\DoctrineUserAgentBundle\Attribute\UpdateUserAgentColumn;
use Tourze\DoctrineUserBundle\Attribute\CreatedByColumn;
use Tourze\DoctrineUserBundle\Attribute\UpdatedByColumn;
use Tourze\EasyAdmin\Attribute\Filter\Filterable;

#[ORM\Entity(repositoryClass: RecordRepository::class)]
#[ORM\Table(name: 'diy_form_record', options: ['comment' => '提交记录'])]
class Record
{
    use TimestampableAware;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private ?int $id = 0;

    #[Groups(['restful_read'])]
    #[Ignore]
    #[ORM\ManyToOne(targetEntity: Form::class, inversedBy: 'records')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Form $form;

    /**
     * @var Collection<Data>
     */
    #[ORM\OneToMany(mappedBy: 'record', targetEntity: Data::class, orphanRemoval: true)]
    private Collection $datas;

    #[ORM\ManyToOne(targetEntity: UserInterface::class)]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    private ?UserInterface $user = null;

    #[IndexColumn]
    #[Groups(['restful_read'])]
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '是否完成'])]
    private ?bool $finished = null;

    #[Groups(['restful_read'])]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, options: ['comment' => '开始时间'])]
    private ?\DateTimeInterface $startTime = null;

    #[Groups(['restful_read'])]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '完成时间'])]
    private ?\DateTimeInterface $finishTime = null;

    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '答题标签数据'])]
    private ?array $answerTags = [];

    #[ORM\Column(nullable: true, options: ['comment' => '原始提交数据'])]
    private ?array $submitData = [];

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?UserInterface $inviter = null;

    #[ORM\Version]
    #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['default' => 1, 'comment' => '乐观锁版本号'])]
    private ?int $lockVersion = null;

    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '额外信息'])]
    private ?array $extraData = [];

    #[CreatedByColumn]
    #[ORM\Column(nullable: true, options: ['comment' => '创建人'])]
    private ?string $createdBy = null;

    #[UpdatedByColumn]
    #[ORM\Column(nullable: true, options: ['comment' => '更新人'])]
    private ?string $updatedBy = null;

    #[CreateUserAgentColumn]
    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '创建时UA'])]
    private ?string $createdFromUa = null;

    #[UpdateUserAgentColumn]
    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '更新时UA'])]
    private ?string $updatedFromUa = null;

    /**
     * @DateRangePickerField()
     */
    #[IndexColumn]
    #[Filterable(label: '提交时间')]
    #[CreateTimeColumn]
    #[Groups(['restful_read', 'admin_curd'])]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '创建时间'])]#[UpdateTimeColumn]
    #[Groups(['admin_curd'])]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '更新时间'])]#[CreateIpColumn]
    #[ORM\Column(type: Types::STRING, length: 128, nullable: true, options: ['comment' => '创建者IP'])]
    private ?string $createdFromIp = null;

    #[UpdateIpColumn]
    #[ORM\Column(type: Types::STRING, length: 128, nullable: true, options: ['comment' => '更新者IP'])]
    private ?string $updatedFromIp = null;

    public function __construct()
    {
        $this->datas = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getForm(): Form
    {
        return $this->form;
    }

    public function setForm(Form $form): self
    {
        $this->form = $form;

        return $this;
    }

    /**
     * @return Collection<int, Data>
     */
    public function getDatas(): Collection
    {
        return $this->datas;
    }

    public function addData(Data $data): self
    {
        if (!$this->datas->contains($data)) {
            $this->datas[] = $data;
            $data->setRecord($this);
        }

        return $this;
    }

    public function removeData(Data $data): self
    {
        if ($this->datas->removeElement($data)) {
            // set the owning side to null (unless already changed)
            if ($data->getRecord() === $this) {
                $data->setRecord(null);
            }
        }

        return $this;
    }

    public function getUser(): ?UserInterface
    {
        return $this->user;
    }

    public function setUser(?UserInterface $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function isFinished(): ?bool
    {
        return $this->finished;
    }

    public function setFinished(?bool $finished): self
    {
        $this->finished = $finished;

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

    public function getFinishTime(): ?\DateTimeInterface
    {
        return $this->finishTime;
    }

    public function setFinishTime(?\DateTimeInterface $finishTime): self
    {
        $this->finishTime = $finishTime;

        return $this;
    }

    /**
     * @return array|Data[]
     */
    #[Groups(['restful_read'])]
    public function getDataList(): array
    {
        $result = [];
        foreach ($this->getDatas() as $data) {
            if (!$data->getField()) {
                continue;
            }

            if (!$data->getField()->isValid()) {
                continue;
            }

            $result[$data->getField()->getSn()] = $data;
        }

        return $result;
    }

    /**
     * 检查是否答过这个题目.
     */
    public function checkHasAnswered(Field $field): bool
    {
        foreach ($this->getDatas() as $data) {
            if ($data->getField()?->getId() === $field->getId()) {
                return true;
            }
        }

        return false;
    }

    /**
     * 根据SN读取Data对象
     */
    public function obtainDataBySN(string $sn): ?Data
    {
        foreach ($this->getDatas() as $data) {
            if ($data->getField()?->getSn() === $sn) {
                return $data;
            }
        }

        return null;
    }

    /**
     * 根据SN读取当前选择的值
     */
    public function obtainInputBySN(string $sn): ?string
    {
        return $this->obtainDataBySN($sn)?->getInput();
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

    public function getSubmitData(): ?array
    {
        return $this->submitData;
    }

    public function setSubmitData(?array $submitData): self
    {
        $this->submitData = $submitData;

        return $this;
    }

    public function getInviter(): ?UserInterface
    {
        return $this->inviter;
    }

    public function setInviter(?UserInterface $inviter): self
    {
        $this->inviter = $inviter;

        return $this;
    }

    public function getLockVersion(): ?int
    {
        return $this->lockVersion;
    }

    public function setLockVersion(?int $lockVersion): self
    {
        $this->lockVersion = $lockVersion;

        return $this;
    }

    public function getExtraData(): ?array
    {
        return $this->extraData;
    }

    public function setExtraData(?array $extraData): self
    {
        $this->extraData = $extraData;

        return $this;
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

    public function setCreatedFromUa(?string $createdFromUa): void
    {
        $this->createdFromUa = $createdFromUa;
    }

    public function getCreatedFromUa(): ?string
    {
        return $this->createdFromUa;
    }

    public function setUpdatedFromUa(?string $updatedFromUa): void
    {
        $this->updatedFromUa = $updatedFromUa;
    }

    public function getUpdatedFromUa(): ?string
    {
        return $this->updatedFromUa;
    }public function getCreatedFromIp(): ?string
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
