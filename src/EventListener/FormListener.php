<?php

declare(strict_types=1);

namespace DiyFormBundle\EventListener;

use DiyFormBundle\Entity\Form;
use DiyFormBundle\Repository\RecordRepository;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;

#[AsEntityListener(event: Events::preRemove, method: 'preRemove', entity: Form::class)]
class FormListener
{
    public function __construct(private readonly RecordRepository $recordRepository)
    {
    }

    /**
     * 删除之前，我们检查是否已经有提交过记录.
     */
    public function preRemove(Form $object): void
    {
        $c = $this->recordRepository->count([
            'form' => $object,
        ]);
        if ($c > 0) {
            throw new \RuntimeException('该调查问卷已被使用，无法删除');
        }
    }
}
