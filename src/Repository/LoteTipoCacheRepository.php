<?php

namespace SL\WebsiteBundle\Repository;

use SL\WebsiteBundle\Entity\LoteTipoCache;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method LoteTipoCache|null find($id, $lockMode = null, $lockVersion = null)
 * @method LoteTipoCache|null findOneBy(array $criteria, array $orderBy = null)
 * @method LoteTipoCache[]    findAll()
 * @method LoteTipoCache[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LoteTipoCacheRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LoteTipoCache::class);
    }

    public function flushData(){
        $this->getEntityManager()->createQueryBuilder()->delete()->from(LoteTipoCache::class, 'cache')->getQuery()->execute();
        return;
    }

    // /**
    //  * @return LoteTipoCache[] Returns an array of LoteTipoCache objects
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
    public function findOneBySomeField($value): ?LoteTipoCache
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
