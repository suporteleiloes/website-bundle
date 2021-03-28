<?php

namespace SL\WebsiteBundle\Repository;

use SL\WebsiteBundle\Entity\Lote;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\Persistence\ManagerRegistry;
use SL\WebsiteBundle\Entity\Leilao;
use SL\WebsiteBundle\Entity\Lance;

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

    public function findDestaques($notIds = [])
    {
        $qb = $this->createQueryBuilder('l');
        $qb
            ->where('l.destaque = :destaque')
            ->andWhere('l.status <= :status')
            ->setParameter('destaque', true)
            ->setParameter('status', Lote::STATUS_ABERTO_PARA_LANCES);

        if (is_array($notIds) && @count($notIds) > 0) {
            $qb->andWhere('l.id NOT IN (:ids)')->setParameter('ids', $notIds);
        }

        return $qb->getQuery()
            ->getResult();
    }

    public function findAllSimpleBasic($leilao, $limit = 100, $offset = 0, $filtros = null, $busca = null, $tipoId = null)
    {

        $query = $this->getEntityManager()->createQueryBuilder()
            ->select('l')
            ->from(Lote::class, "l")
            ->join("l.leilao", "leilao");

        if ($tipoId !== null) {
            $query->where('l.tipoPaiId = :tipo')->setParameter('tipo', $tipoId);
            $query->andWhere('l.status < :status')->setParameter('status', Lote::STATUS_HOMOLOGANDO);
        } elseif (intval($leilao) > 0) {
            $query->where('l.leilao = :leilao')->setParameter('leilao', $leilao);
        }
        if (!$busca) {
            // $query->where('l.leilao = :leilao')->setParameter('leilao', $leilao);
        } else {
            // $query->where('l.titulo LIKE :busca or l.descricao LIKE :busca')->setParameter('busca', "%$busca%");
            // $query->andWhere('l.status = :status')->setParameter('status', Lote::STATUS_ABERTO_PARA_LANCES);
        }


        $queryCount = $this->getEntityManager()->createQueryBuilder()
            ->select('COUNT(1) total')
            ->from(Lote::class, "l")
            ->join("l.leilao", "leilao");

        if (!$busca) {
            // $queryCount->where('l.leilao = :leilao')->setParameter('leilao', $leilao);
        } else {
            // $queryCount->where('l.titulo LIKE :busca or l.descricao LIKE :busca')->setParameter('busca', "%$busca%");
            // $queryCount->andWhere('l.status = :status')->setParameter('status', Lote::STATUS_ABERTO_PARA_LANCES);
        }

        if ($busca) {
            $filtroBuscaCriteria = Criteria::create()
                ->where(Criteria::expr()->orX(
                    Criteria::expr()->contains('l.descricao', $busca),
                    Criteria::expr()->contains('l.tipo', $busca),
                    Criteria::expr()->contains('l.titulo', $busca)
                ));
            $query->addCriteria($filtroBuscaCriteria);
            $queryCount->addCriteria($filtroBuscaCriteria);
        }

        //Busca
        if (isset($filtros['busca'])) {
            $busca = $filtros['busca'];
            $filtroBuscaCriteria = Criteria::create()
                ->where(Criteria::expr()->orX(
                    Criteria::expr()->contains('l.descricao', $busca),
                    Criteria::expr()->contains('l.tipo', $busca),
                    Criteria::expr()->contains('l.titulo', $busca)
                ));
            $query->addCriteria($filtroBuscaCriteria);
            $queryCount->addCriteria($filtroBuscaCriteria);
        }

        //Filtro por Origem
        if (isset($filtros['origem'])) {
            $query->leftJoin('l.cache', 'cache');
            $queryCount->leftJoin('l.cache', 'cache');
            $filtroOrigemCriteria = Criteria::create()
                ->where(Criteria::expr()->in('cache.origemId', $filtros['origem']));
            $query->addCriteria($filtroOrigemCriteria);
            $queryCount->addCriteria($filtroOrigemCriteria);
            //$query->andWhere('l.tipo = :tipo')->setParameter('tipo', $filtros['tipo']);
        }

        //Filtro por Condição
        if (isset($filtros['condicao'])) {
            $filtroCondicaoCriteria = Criteria::create()
                ->where(Criteria::expr()->in('l.condicao', $filtros['condicao']));
            $query->addCriteria($filtroCondicaoCriteria);
            $queryCount->addCriteria($filtroCondicaoCriteria);
            //$query->andWhere('l.tipo = :tipo')->setParameter('tipo', $filtros['tipo']);
        }

        //Filtro por Tipo
        if (isset($filtros['tipo'])) {
            $filtroTipoCriteria = Criteria::create()
                ->where(Criteria::expr()->in('l.tipo', $filtros['tipo']));
            $query->addCriteria($filtroTipoCriteria);
            $queryCount->addCriteria($filtroTipoCriteria);
        }

        //Filtro por Marca
        if (isset($filtros['marca'])) {
            $filtroMarcaCriteria = Criteria::create()
                ->where(Criteria::expr()->in('l.marca', $filtros['marca']));
            $query->addCriteria($filtroMarcaCriteria);
            $queryCount->addCriteria($filtroMarcaCriteria);
            //$query->andWhere('l.marca = :marca')->setParameter('marca', $filtros['marca']);
        }

        if (isset($filtros['ano'])) {
            $busca = $filtros['ano'];
            if (!is_array($busca)) {
                $busca = [$busca];
            }
            if (!isset($filtros['origem'])) {
                $query->leftJoin('l.cache', 'cache');
                $queryCount->leftJoin('l.cache', 'cache');
            }
            $filtroBuscaCriteria = Criteria::create();
            if (!empty($busca[1])) {
                if (empty($busca[0])) {
                    $filtroBuscaCriteria->where(Criteria::expr()->orX(
                        Criteria::expr()->lte('cache.ano1', intval($busca[1])),
                        Criteria::expr()->lte('cache.ano2', intval($busca[1]))
                    ));
                } else {
                    $filtroBuscaCriteria->where(Criteria::expr()->gte('cache.ano1', intval($busca[0])));
                    $filtroBuscaCriteria->andWhere(Criteria::expr()->lte('cache.ano2', intval($busca[1])));
                }
            } else {
                $filtroBuscaCriteria->where(Criteria::expr()->orX(
                    Criteria::expr()->eq('cache.ano1', intval($busca[0])),
                    Criteria::expr()->eq('cache.ano2', intval($busca[0]))
                ));
            }

            $query->addCriteria($filtroBuscaCriteria);
            $queryCount->addCriteria($filtroBuscaCriteria);
        }

        //Filtro por Comitente
        /*if (isset($filtros['comitente'])) {
            $filtroComitenteCriteria = Criteria::create()
                ->where(Criteria::expr()->eq('l.comitente', $filtros['comitente']));
            $query->addCriteria($filtroComitenteCriteria);
            $queryCount->addCriteria($filtroComitenteCriteria);
            //$query->andWhere('l.comitente = :comitente')->setParameter('comitente', $filtros['comitente']);
        }*/

        //Filtro por Status
        if (isset($filtros['naoExibirRascunho'])) {
            $filtroComitenteCriteria = Criteria::create()
                ->where(Criteria::expr()->neq('l.status', Lote::STATUS_RASCUNHO));
            $query->addCriteria($filtroComitenteCriteria);
            $queryCount->addCriteria($filtroComitenteCriteria);
        }

        //Filtro por Blindado
        if (isset($filtros['blindado'])) {
            $query->andWhere('l.descricao LIKE :blindado')
                ->setParameter('blindado', '%blindado%');
            $queryCount->andWhere('l.descricao LIKE :blindado')
                ->setParameter('blindado', '%blindado%');
        }

        // $query->andWhere('leilao.status IN (:statusLeilao)')->setParameter('statusLeilao', [Leilao::STATUS_EM_BREVE, Leilao::STATUS_EM_LOTEAMENTO, /*Leilao::STATUS_VER_MAIS,*/ Leilao::STATUS_ABERTO_PARA_LANCES]);
        // $queryCount->andWhere('leilao.status IN (:statusLeilao)')->setParameter('statusLeilao', [Leilao::STATUS_EM_BREVE, Leilao::STATUS_EM_LOTEAMENTO, /*Leilao::STATUS_VER_MAIS,*/ Leilao::STATUS_ABERTO_PARA_LANCES]);

        $query->addOrderBy('l.numero', 'ASC');

        $query = $query->getQuery()
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->setHydrationMode(\Doctrine\ORM\Query::HYDRATE_ARRAY);;
        //->getArrayResult();
        //return $paginator = new Paginator($query, $fetchJoinCollection = true);
        return [
            'result' => $query->getResult(), //getArrayResult
            'total' => $queryCount->getQuery()->getSingleScalarResult()
        ];
    }

    public function totalLotesByTipo()
    {
        $rsm = (new ResultSetMapping())
            ->addScalarResult('tipo_pai', 'tipo_pai')
            ->addScalarResult('tipo_pai_id', 'tipo_pai_id')
            ->addScalarResult('total', 'total');

        $query = $this->getEntityManager()
            ->createNativeQuery('select distinct tipo_pai_id, tipo_pai, (select count(1) from lote l2 where l2.tipo_pai_id = lote.tipo_pai_id and l2.status < 5) total from lote where lote.status < 5',
                $rsm
            );

        return $query->getResult();
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
