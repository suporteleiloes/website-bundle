<?php

namespace SL\WebsiteBundle\Repository;

use SL\WebsiteBundle\Entity\ApiSync;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ApiSync|null find($id, $lockMode = null, $lockVersion = null)
 * @method ApiSync|null findOneBy(array $criteria, array $orderBy = null)
 * @method ApiSync[]    findAll()
 * @method ApiSync[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ApiSyncRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ApiSync::class);
    }

    // /**
    //  * @return ApiSync[] Returns an array of ApiSync objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ApiSync
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
