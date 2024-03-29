<?php

namespace SL\WebsiteBundle\Repository;

use SL\WebsiteBundle\Entity\Banner;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Banner|null find($id, $lockMode = null, $lockVersion = null)
 * @method Banner|null findOneBy(array $criteria, array $orderBy = null)
 * @method Banner[]    findAll()
 * @method Banner[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BannerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Banner::class);
    }

    /**
     * @return mixed
     */
    public function findAtivos(){

        $dql = 'SELECT b FROM SL\WebsiteBundle\Entity\Banner b WHERE b.dateStartExhibition <= :dataatual AND b.dateEndExhibition >= :dataatual AND b.active = 1 order by b.position, b.dateEndExhibition'; // AND b.situacao = true
        $query = $this->getEntityManager()->createQuery($dql)->setParameter('dataatual', (new \DateTime())->format('Y-m-d ') . '00:00:00');
        return $query->getResult();
    }

    public function findAtivosSemSecao(){

        $dql = 'SELECT b FROM SL\WebsiteBundle\Entity\Banner b WHERE b.dateStartExhibition <= :dataatual AND b.dateEndExhibition >= :dataatual AND b.active = 1 and (b.secao = :secao or b.secao is null) order by b.position, b.dateEndExhibition'; // AND b.situacao = true
        $query = $this->getEntityManager()->createQuery($dql)
            ->setParameter('dataatual', (new \DateTime())->format('Y-m-d ') . '00:00:00')
            ->setParameter('secao', '0')
        ;
        return $query->getResult();
    }
}
