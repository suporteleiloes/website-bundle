<?php

namespace SL\WebsiteBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use SL\WebsiteBundle\Entity\LeilaoExtra;

/**
 * @method LeilaoExtra|null find($id, $lockMode = null, $lockVersion = null)
 * @method LeilaoExtra|null findOneBy(array $criteria, array $orderBy = null)
 * @method LeilaoExtra[]    findAll()
 * @method LeilaoExtra[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LeilaoExtraRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LeilaoExtra::class);
    }
}
