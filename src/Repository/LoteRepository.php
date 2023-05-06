<?php

namespace SL\WebsiteBundle\Repository;

use SL\WebsiteBundle\Entity\Lote;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\Persistence\ManagerRegistry;
use SL\WebsiteBundle\Entity\Leilao;
use SL\WebsiteBundle\Entity\Lance;
use SL\WebsiteBundle\Entity\LoteCache;
use SL\WebsiteBundle\Entity\LoteTipoCache;

/**
 * @method Lote|null find($id, $lockMode = null, $lockVersion = null)
 * @method Lote|null findOneBy(array $criteria, array $orderBy = null)
 * @method Lote[]    findAll()
 * @method Lote[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LoteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Lote::class);
    }

    public function totalLotesByTipo($leilao = null)
    {
        $rsm = (new ResultSetMapping())
            ->addScalarResult('tipo_pai', 'tipo_pai')
            ->addScalarResult('tipo_pai_id', 'tipo_pai_id')
            ->addScalarResult('total', 'total');

        $query = 'select distinct tipo_pai_id, tipo_pai, (select count(1) from lote l2 left join leilao on leilao.id = l2.leilao_id where l2.tipo_pai_id = lote.tipo_pai_id and l2.status < 5 and l2.deleted = 0 and (leilao.id is null or (leilao.status_tipo <= 2 and leilao.deleted = 0)) __UPDATE2__) total from lote where lote.status < 5 and lote.deleted = 0 and lote.active = 1 ORDER BY tipo_pai ASC';

        if ($leilao) {
            $query = $query . ' and lote.leilao_id = ' . $leilao;
            $query = str_replace('__UPDATE2__', ' and l2.leilao_id = ' . $leilao, $query);
        } else {
            $query = str_replace('__UPDATE2__', '', $query);
        }

        $query = $this->getEntityManager()
            ->createNativeQuery($query,
                $rsm
            );

        return $query->getResult();
    }

    public function totalLotesByTipoFilho($leilao = null)
    {
        $rsm = (new ResultSetMapping())
            ->addScalarResult('tipo', 'tipo')
            ->addScalarResult('tipo_id', 'tipo_id')
            ->addScalarResult('tipo_pai_id', 'tipo_pai_id')
            ->addScalarResult('total', 'total');

        $query = 'select distinct tipo_id, tipo, tipo_pai_id, (select count(1) from lote l2 left join leilao on leilao.id = l2.leilao_id where l2.tipo_id = lote.tipo_id and l2.status < 5 and l2.deleted = 0 and (leilao.id is null or (leilao.status_tipo <= 2 and leilao.deleted = 0)) __UPDATE2__) total from lote where lote.status < 5 and lote.deleted = 0 and lote.active = 1 ORDER BY tipo ASC';

        if ($leilao) {
            $query = $query . ' and lote.leilao_id = ' . $leilao;
            $query = str_replace('__UPDATE2__', ' and l2.leilao_id = ' . $leilao, $query);
        } else {
            $query = str_replace('__UPDATE2__', '', $query);
        }

        $query = $this->getEntityManager()
            ->createNativeQuery($query,
                $rsm
            );

        return $query->getResult();
    }

    public function montaCacheRelacoes()
    {

        $r = [];

        $this->getEntityManager()->getConnection()->executeQuery('TRUNCATE TABLE lote_cache', array(), array());
        //$this->getEntityManager()->clear();

        $queryFcn = function (&$array, $query, $column, $parent = null) {
            $result = $this->getEntityManager()->getConnection()
                ->prepare($query)
                ->executeQuery()
                ->fetchAllAssociative();

            if ($result && count($result)) {
                foreach ($result as $data) {
                    $array[] = [
                        'tipo' => $column,
                        'valor' => $data['valor'],
                        'total' => $data['total'],
                        'parente' => $parent ? @$data[$parent] : null
                    ];
                }
            }
        };

        $queryFcn(
            $r,
            'select distinct uf as valor, COUNT(uf) total from lote WHERE lote.deleted = 0 GROUP BY uf',
            'uf'
        );
        $queryFcn(
            $r,
            'select distinct cidade valor, uf, COUNT(cidade) total from lote WHERE lote.deleted = 0 GROUP BY cidade, uf',
            'cidade',
            'uf'
        );
        $queryFcn(
            $r,
            'select distinct cidade, bairro valor, COUNT(bairro) total from lote WHERE lote.deleted = 0 GROUP BY bairro, cidade',
            'bairro',
            'cidade'
        );
        //
        $queryFcn(
            $r,
            'select distinct marca as valor, COUNT(marca) total from lote WHERE lote.deleted = 0 GROUP BY marca',
            'marca',
            null
        );
        $queryFcn(
            $r,
            'select distinct modelo valor, marca, COUNT(marca) total from lote WHERE lote.deleted = 0 GROUP BY marca, modelo',
            'marca',
            'modelo'
        );

        foreach($r as $cache) {
            if(empty($cache['tipo']) || empty($cache['valor'])) {
                continue;
            }
            $e = new LoteCache();
            $e->setTipo($cache['tipo']);
            $e->setValor($cache['valor']);
            $e->setParente(@$cache['parente']);
            $e->setTotal($cache['total']);
            $this->getEntityManager()->persist($e);
        }

        $this->getEntityManager()->flush();

        return $r;
    }

    // /**
    //  * @return Lote[] Returns an array of Lote objects
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
    public function findOneBySomeField($value): ?Lote
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
