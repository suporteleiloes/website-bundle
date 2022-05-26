<?php

namespace SL\WebsiteBundle\Repository;

use SL\WebsiteBundle\Entity\LeilaoData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method LeilaoData|null find($id, $lockMode = null, $lockVersion = null)
 * @method LeilaoData|null findOneBy(array $criteria, array $orderBy = null)
 * @method LeilaoData[]    findAll()
 * @method LeilaoData[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LeilaoDataRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LeilaoData::class);
    }
}
