<?php

declare(strict_types=1);

namespace DiyFormBundle\Entity;

use DiyFormBundle\Repository\RecordRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\Ignore;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineIpBundle\Traits\IpTraceableAware;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineUserAgentBundle\Attribute\CreateUserAgentColumn;
use Tourze\DoctrineUserAgentBundle\Attribute\UpdateUserAgentColumn;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;

#[ORM\Entity(repositoryClass: RecordRepository::class)]
#[ORM\Table(name: 'diy_form_record', options: ['comment' => '提交记录'])]
class Record implements \Stringable
{
    use TimestampableAware;
    use BlameableAware;
    use IpTraceableAware;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private int $id = 0;

    #[Groups(groups: ['restful_read'])]
    #[Ignore]
    #[ORM\ManyToOne(targetEntity: Form::class, inversedBy: 'records', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Form $form = null;

    /**
     * @var Collection<int, Data>
     */
    #[ORM\OneToMany(mappedBy: 'record', targetEntity: Data::class, orphanRemoval: true)]
    private Collection $datas;

    #[ORM\ManyToOne(targetEntity: UserInterface::class)]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    private ?UserInterface $user = null;

    #[IndexColumn]
    #[Assert\Type(type: 'bool')]
    #[Groups(groups: ['restful_read'])]
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '是否完成'])]
    private ?bool $finished = null;

    #[Assert\Type(type: '\DateTimeImmutable')]
    #[Groups(groups: ['restful_read'])]
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, options: ['comment' => '开始时间'])]
    private ?\DateTimeImmutable $startTime = null;

    #[Assert\Type(type: '\DateTimeImmutable')]
    #[Groups(groups: ['restful_read'])]
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '完成时间'])]
    private ?\DateTimeImmutable $finishTime = null;

    /**
     * @var array<string, mixed>|null
     */
    #[Assert\Type(type: 'array')]
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '答题标签数据'])]
    private ?array $answerTags = [];

    /**
     * @var array<string, mixed>|null
     */
    #[Assert\Type(type: 'array')]
    #[ORM\Column(nullable: true, options: ['comment' => '原始提交数据'])]
    private ?array $submitData = [];

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?UserInterface $inviter = null;

    #[Assert\PositiveOrZero]
    #[ORM\Version]
    #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['default' => 1, 'comment' => '乐观锁版本号'])]
    private ?int $lockVersion = null;

    /**
     * @var array<string, mixed>|null
     */
    #[Assert\Type(type: 'array')]
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '额外信息'])]
    private ?array $extraData = [];

    #[Assert\Length(max: 65535)]
    #[CreateUserAgentColumn]
    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '创建时UA'])]
    private ?string $createdFromUa = null;

    #[Assert\Length(max: 65535)]
    #[UpdateUserAgentColumn]
    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '更新时UA'])]
    private ?string $updatedFromUa = null;

    public function __construct()
    {
        $this->datas = new ArrayCollection();
    }

    public function __toString(): string
    {
        if (0 === $this->getId()) {
            return '';
        }

        $formTitle = $this->getForm()?->getTitle() ?? '未知表单';
        $userId = $this->getUser()?->getUserIdentifier() ?? '匿名用户';
        $status = true === $this->isFinished() ? '已完成' : '未完成';

        return "记录#{$this->getId()} - {$formTitle} - {$userId} - {$status}";
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getForm(): ?Form
    {
        return $this->form;
    }

    public function setForm(?Form $form): void
    {
        $this->form = $form;
    }

    /**
     * @return Collection<int, Data>
     */
    public function getDatas(): Collection
    {
        return $this->datas;
    }

    public function addData(Data $data): void
    {
        if (!$this->datas->contains($data)) {
            $this->datas->add($data);
            $data->setRecord($this);
        }
    }

    public function removeData(Data $data): void
    {
        if ($this->datas->removeElement($data)) {
            // set the owning side to null (unless already changed)
            if ($data->getRecord() === $this) {
                $data->setRecord(null);
            }
        }
    }

    public function getUser(): ?UserInterface
    {
        return $this->user;
    }

    public function setUser(?UserInterface $user): void
    {
        $this->user = $user;
    }

    public function isFinished(): ?bool
    {
        return $this->finished;
    }

    public function setFinished(?bool $finished): void
    {
        $this->finished = $finished;
    }

    public function getStartTime(): ?\DateTimeImmutable
    {
        return $this->startTime;
    }

    public function setStartTime(?\DateTimeImmutable $startTime): void
    {
        $this->startTime = $startTime;
    }

    public function getFinishTime(): ?\DateTimeImmutable
    {
        return $this->finishTime;
    }

    public function setFinishTime(?\DateTimeImmutable $finishTime): void
    {
        $this->finishTime = $finishTime;
    }

    /**
     * @return array<string, Data>
     */
    #[Groups(groups: ['restful_read'])]
    public function getDataList(): array
    {
        $result = [];
        foreach ($this->getDatas() as $data) {
            if (null === $data->getField()) {
                continue;
            }

            if (false === $data->getField()->isValid()) {
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

    /**
     * @return array<string, mixed>|null
     */
    public function getAnswerTags(): ?array
    {
        return $this->answerTags;
    }

    /**
     * @param array<string, mixed>|null $answerTags
     */
    public function setAnswerTags(?array $answerTags): void
    {
        $this->answerTags = $answerTags;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getSubmitData(): ?array
    {
        return $this->submitData;
    }

    /**
     * @param array<string, mixed>|null $submitData
     */
    public function setSubmitData(?array $submitData): void
    {
        $this->submitData = $submitData;
    }

    public function getInviter(): ?UserInterface
    {
        return $this->inviter;
    }

    public function setInviter(?UserInterface $inviter): void
    {
        $this->inviter = $inviter;
    }

    public function getLockVersion(): ?int
    {
        return $this->lockVersion;
    }

    public function setLockVersion(?int $lockVersion): void
    {
        $this->lockVersion = $lockVersion;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getExtraData(): ?array
    {
        return $this->extraData;
    }

    /**
     * @param array<string, mixed>|null $extraData
     */
    public function setExtraData(?array $extraData): void
    {
        $this->extraData = $extraData;
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
    }
}
