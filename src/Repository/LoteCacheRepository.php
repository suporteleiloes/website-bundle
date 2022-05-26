<?php

namespace SL\WebsiteBundle\Repository;

use SL\WebsiteBundle\Entity\LoteCache;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method LoteCache|null find($id, $lockMode = null, $lockVersion = null)
 * @method LoteCache|null findOneBy(array $criteria, array $orderBy = null)
 * @method LoteCache[]    findAll()
 * @method LoteCache[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LoteCacheRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LoteCache::class);
    }

    // /**
    //  * @return LoteCache[] Returns an array of LoteCache objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?LoteCache
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
