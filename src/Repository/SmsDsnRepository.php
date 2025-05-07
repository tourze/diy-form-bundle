<?php

namespace DiyFormBundle\Repository;

use DiyFormBundle\Entity\SmsDsn;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use DoctrineEnhanceBundle\Repository\CommonRepositoryAware;

/**
 * @method SmsDsn|null find($id, $lockMode = null, $lockVersion = null)
 * @method SmsDsn|null findOneBy(array $criteria, array $orderBy = null)
 * @method SmsDsn[]    findAll()
 * @method SmsDsn[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SmsDsnRepository extends ServiceEntityRepository
{
    use CommonRepositoryAware;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SmsDsn::class);
    }
}
