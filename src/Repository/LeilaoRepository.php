<?php

namespace SL\WebsiteBundle\Repository;

use SL\WebsiteBundle\Entity\Leilao;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Leilao|null find($id, $lockMode = null, $lockVersion = null)
 * @method Leilao|null findOneBy(array $criteria, array $orderBy = null)
 * @method Leilao[]    findAll()
 * @method Leilao[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LeilaoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Leilao::class);
    }

    public function carregaRecentes(\DateTime $dataLimite, $judicial = null)
    {
        $qb = $this->createQueryBuilder('l')
            #->where('l.dataPraca1 >= :data OR (l.dataPraca2 >= :data and l.status <= 4) OR l.dataFimPraca1 >= :data OR (l.dataFimPraca2 >= :data and l.status <= 4)')
            ->where('l.dataProximoLeilao >= :data')
            ->setParameter('data', $dataLimite->format('Y-m-d') . ' 00:00:00');

        if (null !== $judicial) {
            $qb->andWhere('l.judicial = :judicial')->setParameter('judicial', $judicial);
        }

        $query = $qb->orderBy('l.dataProximoLeilao', 'ASC')->getQuery();

        return $query->execute();

    }

    public function filtros($leilao)
    {

        $simpleCacheQuery = function ($field, $filter = '', $noId = false) use ($leilao) {
            if ($noId) {
                $id = '%1$s';
            } else {
                $id = '%1$s_id';
            }
            $string = 'SELECT count(c.id) totalItens, c.__ID__ id, MAX(c.%1$s) nome, (SELECT GROUP_CONCAT(l.id) FROM lote l where l.leilao_id = :leilao and l.__ID__ = c.__ID__) lotes  FROM lote c WHERE c.leilao_id = :leilao %2$s GROUP BY c.__ID__ ORDER BY nome';
            $string = str_replace('__ID__', $id, $string);
            $string = str_replace(':leilao', $leilao, $string);
            return sprintf(
                $string,
                $field, $filter
            );
        };

        $rsm = (new ResultSetMapping())
            ->addScalarResult('totalItens', 'totalItens')
            ->addScalarResult('id', 'id')
            ->addScalarResult('nome', 'nome')
            ->addScalarResult('lotes', 'lotes');

        // Status
        $queryStatus= $this->getEntityManager()
            ->createNativeQuery($simpleCacheQuery('status', '', true),
                $rsm
            );

        // Tipo de Comitentes
        $queryTipoComitentes = $this->getEntityManager()
            ->createNativeQuery($simpleCacheQuery('comitente_tipo', '', true),
                $rsm
            );

        // Comitentes
        $queryComitentes = $this->getEntityManager()
            ->createNativeQuery($simpleCacheQuery('comitente'),
                $rsm
            );
        #$queryComitentes->setParameter('leilao', $leilao);

        // Tipo
        $queryTipos = $this->getEntityManager()
            ->createNativeQuery($simpleCacheQuery('tipo'), $rsm);
        #$queryTipos->setParameter('leilao', $leilao);

        // Marca
        $queryMarcas = $this->getEntityManager()
            ->createNativeQuery($simpleCacheQuery('marca'), $rsm);
        #$queryMarcas->setParameter('leilao', $leilao);

        // Modelo
        $queryModelos = $this->getEntityManager()
            ->createNativeQuery($simpleCacheQuery('modelo'), $rsm);
        #$queryModelos->setParameter('leilao', $leilao);

        // Ano
        $queryAnos = $this->getEntityManager()
            ->createNativeQuery($simpleCacheQuery('ano', '', true), $rsm);
        #$queryAnos->setParameter('leilao', $leilao);

        // Uf
        $queryUfs = $this->getEntityManager()
            ->createNativeQuery($simpleCacheQuery('uf', '', true), $rsm);
        #$queryUfs->setParameter('leilao', $leilao);

        // Cidades
        $queryCidades = $this->getEntityManager()
            ->createNativeQuery($simpleCacheQuery('cidade', '', true), $rsm);
        #$queryCidades->setParameter('leilao', $leilao);

        return [
            "status" => ['tipo' => 'status', 'tipoNome' => 'Status', 'values' => $queryStatus->getResult()],
            "tipoComitente" => ['tipo' => 'tipoComitente', 'tipoNome' => 'Tipo de Comitente', 'values' => $queryTipoComitentes->getResult()],
            "comitentes" => ['tipo' => 'comitente', 'tipoNome' => 'Comitentes', 'values' => $queryComitentes->getResult()],
            "tipos" => ['tipo' => 'tipo', 'tipoNome' => 'Tipo do Bem', 'values' => $queryTipos->getResult()],
            "marcas" => ['tipo' => 'marca', 'tipoNome' => 'Marca/Montadora', 'values' => $queryMarcas->getResult()],
            "modelos" => ['tipo' => 'modelo', 'tipoNome' => 'Modelo', 'values' => $queryModelos->getResult()],
            "anos" => ['tipo' => 'ano', 'tipoNome' => 'Ano', 'values' => $queryAnos->getResult()],
            "ufs" => ['tipo' => 'uf', 'tipoNome' => 'UF', 'values' => $queryUfs->getResult()],
            "cidades" => ['tipo' => 'cidade', 'tipoNome' => 'Cidade', 'values' => $queryCidades->getResult()],
        ];
    }

    // /**
    //  * @return Leilao[] Returns an array of Leilao objects
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
    public function findOneBySomeField($value): ?Leilao
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
