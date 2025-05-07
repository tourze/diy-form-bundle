<?php

namespace DiyFormBundle\Repository;

use DiyFormBundle\Entity\SendLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use DoctrineEnhanceBundle\Repository\CommonRepositoryAware;

/**
 * @method SendLog|null find($id, $lockMode = null, $lockVersion = null)
 * @method SendLog|null findOneBy(array $criteria, array $orderBy = null)
 * @method SendLog[]    findAll()
 * @method SendLog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SendLogRepository extends ServiceEntityRepository
{
    use CommonRepositoryAware;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SendLog::class);
    }
}
