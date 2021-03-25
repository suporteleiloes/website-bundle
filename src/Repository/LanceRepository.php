<?php

namespace App\Repository;

use App\Entity\Lance;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Lance|null find($id, $lockMode = null, $lockVersion = null)
 * @method Lance|null findOneBy(array $criteria, array $orderBy = null)
 * @method Lance[]    findAll()
 * @method Lance[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LanceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Lance::class);
    }

    // /**
    //  * @return Lance[] Returns an array of Lance objects
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
    public function findOneBySomeField($value): ?Lance
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
