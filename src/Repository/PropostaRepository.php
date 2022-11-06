<?php

namespace SL\WebsiteBundle\Repository;

use SL\WebsiteBundle\Entity\Proposta;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Proposta|null find($id, $lockMode = null, $lockVersion = null)
 * @method Proposta|null findOneBy(array $criteria, array $orderBy = null)
 * @method Proposta[]    findAll()
 * @method Proposta[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PropostaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Proposta::class);
    }

    // /**
    //  * @return Proposta[] Returns an array of Proposta objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Proposta
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
