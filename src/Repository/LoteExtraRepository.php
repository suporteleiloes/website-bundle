<?php

namespace SL\WebsiteBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use SL\WebsiteBundle\Entity\LoteExtra;

/**
 * @method LoteExtra|null find($id, $lockMode = null, $lockVersion = null)
 * @method LoteExtra|null findOneBy(array $criteria, array $orderBy = null)
 * @method LoteExtra[]    findAll()
 * @method LoteExtra[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LoteExtraRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LoteExtra::class);
    }
}
