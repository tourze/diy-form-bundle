<?php

declare(strict_types=1);

namespace DiyFormBundle\Entity;

use DiyFormBundle\Repository\SmsDsnRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineSnowflakeBundle\Traits\SnowflakeKeyAware;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineTrackBundle\Attribute\TrackColumn;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;

#[ORM\Entity(repositoryClass: SmsDsnRepository::class)]
#[ORM\Table(name: 'sms_dsn', options: ['comment' => 'SMS配置'])]
class SmsDsn implements \Stringable
{
    use TimestampableAware;
    use BlameableAware;
    use SnowflakeKeyAware;

    #[Assert\Type(type: 'bool')]
    #[IndexColumn]
    #[TrackColumn]
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '有效', 'default' => 0])]
    private ?bool $valid = false;

    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    #[IndexColumn]
    #[ORM\Column(type: Types::STRING, length: 100, options: ['comment' => '名称'])]
    private ?string $name = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 65535)]
    #[ORM\Column(type: Types::TEXT, options: ['comment' => 'DSN'])]
    private ?string $dsn = null;

    #[Assert\NotNull]
    #[Assert\PositiveOrZero]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '权重'])]
    private ?int $weight = null;

    public function __toString(): string
    {
        if (null === $this->getId()) {
            return '';
        }

        $validStr = true === $this->isValid() ? '有效' : '无效';

        return "{$this->getName()} - {$validStr}";
    }

    public function isValid(): ?bool
    {
        return $this->valid;
    }

    public function setValid(?bool $valid): void
    {
        $this->valid = $valid;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getDsn(): ?string
    {
        return $this->dsn;
    }

    public function setDsn(?string $dsn): void
    {
        $this->dsn = $dsn;
    }

    public function getWeight(): ?int
    {
        return $this->weight;
    }

    public function setWeight(?int $weight): void
    {
        $this->weight = $weight;
    }
}
