<?php

namespace App\Repository;

use App\Entity\LeilaoCache;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method LeilaoCache|null find($id, $lockMode = null, $lockVersion = null)
 * @method LeilaoCache|null findOneBy(array $criteria, array $orderBy = null)
 * @method LeilaoCache[]    findAll()
 * @method LeilaoCache[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LeilaoCacheRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LeilaoCache::class);
    }

    // /**
    //  * @return LeilaoCache[] Returns an array of LeilaoCache objects
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
    public function findOneBySomeField($value): ?LeilaoCache
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
