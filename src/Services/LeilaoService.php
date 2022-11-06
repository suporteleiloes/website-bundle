<?php


namespace SL\WebsiteBundle\Services;


use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use SL\WebsiteBundle\Entity\Banner;
use SL\WebsiteBundle\Entity\Leilao;
use SL\WebsiteBundle\Entity\Lote;
use SL\WebsiteBundle\Entity\LoteTipoCache;

class LeilaoService
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param null $leilao
     * @param bool $somenteAtivos
     * @param int $limit
     * @param int $offset
     * @param array $filtros
     *      $filtros = [
     *          'busca' => (string) Busca inteligente por lotes
     *          'precoMinimo' => (decimal) Valor mínimo
     *          'precoMaximo' => (decimal) Valor máximo
     *          'comitente' => (array|int) Tipo do Bem
     *          'tipo' => (array|int) Tipo do Bem
     *          'tipoLeilao' => (array|int) 1 = Judicial; 2 = Extrajudicial;
     *          'relevancia' => (int) 0 = Relevância baseado no número e acessos e lances; 1 = Pela data do leilão (Crescente); 2 = Valor (Crescente); 3 = Valor (Decrescente)
     *          'qtdLeiloes' => (int) 0 = Leilão único; 1 - Primeiro leilão; 2 = Segundo leilão; 3 = Terceiro leilão (falência)
     *          'uf' => (array|mixed) UF do lote;
     *          'cidade' => (array|mixed) Cidade do lote;
     *          'bairro' => (array|mixed) Bairros do lote;
     *          'ocupado' => (bool/null) Se está ou não desocupado. Null para ambos.
     *          'classificacaoLeilao' => (array/int) Classificação do leilão. Iniciativa privada, Seguradoras etc.
     *          'latLng' => (array[0 => lat, 1 = lng, 2 = proximidade(10km por padrão)) Latitude e Longitude para disponibilizar imóveis próximos
     *          'ignorarLeilaoEncerrado' => (boolean) Se verdadeiro, encontra bens mesmo estando em leilões já encerrados
     *          'status' => (mixed|int) Status do lote.
     *          'destaque' => (boolean) Buscar pelo destaque ou não.
     *          'destaqueComVendaDireta' => (boolean) Adicionar bens em venda direta aos destaques
     *          'vendaDireta' => (boolean) Buscar por venda direta ou não. Null não aplica filtro
     *      ]
     * @return array|Lote
     */
    public function buscarBens($leilao = null, $somenteAtivos = true, $limit = 100, $offset = 0, $filtros = [])
    {
        $searchCriteria = Criteria::create();

        $hoje = new \DateTime();
        $joins = [];

        $convertArray = function ($value) {
            return is_array($value) ? $value : [$value];
        };

        if ($leilao) {
            $searchCriteria->andWhere(Criteria::expr()->eq('l.leilao', $leilao));
        }

        if ($somenteAtivos) {
            $searchCriteria->andWhere(Criteria::expr()->eq('l.active', true));
            $searchCriteria->andWhere(
                Criteria::expr()->orX(
                    Criteria::expr()->eq('l.leilao', null),
                    Criteria::expr()->lte('leilao.statusTipo', 2)
                )
            );
            $joins[] = ['l.leilao', 'leilao', true];
        }

        if (isset($filtros['relevancia'])) {
        }

        if (isset($filtros['destaque'])) {
            if (isset($filtros['destaqueComVendaDireta']) && $filtros['destaqueComVendaDireta']) {
                $searchCriteria->andWhere(
                    Criteria::expr()->orX(
                        Criteria::expr()->eq('l.destaque', $filtros['destaque']),
                        Criteria::expr()->eq('l.vendaDireta', true)
                    )
                );
            } else {
                $searchCriteria->andWhere(
                    Criteria::expr()->eq('l.destaque', $filtros['destaque'])
                );
            }
        }

        if (isset($filtros['vendaDireta'])) {
            $searchCriteria->andWhere(
                Criteria::expr()->eq('l.vendaDireta', $filtros['vendaDireta'])
            );
        }

        if (isset($filtros['tipo'])) {
            $tipoSearch = Criteria::expr()->in('l.tipoId', $convertArray($filtros['tipo']));
            if (!is_array($filtros['tipo'])) {
                if (!is_numeric($filtros['tipo'])) {
                    $tipoSearch = Criteria::expr()->orX(
                        Criteria::expr()->contains('l.tipo', $filtros['tipo']),
                        Criteria::expr()->contains('l.tipoPai', $filtros['tipo'])
                    );
                }
            }
            $searchCriteria->andWhere(
                $tipoSearch
            );
        }

        if (isset($filtros['marca'])) {
            $marcaSearch = Criteria::expr()->in('l.marcaId', $convertArray($filtros['marca']));
            if (!is_array($filtros['marca'])) {
                if (!is_numeric($filtros['marca'])) {
                    $marcaSearch = Criteria::expr()->orX(
                        Criteria::expr()->contains('l.marca', $filtros['marca']),
                        Criteria::expr()->contains('l.marcaId', $filtros['marca'])
                    );
                }
            }
            $searchCriteria->andWhere(
                $marcaSearch
            );
        }

        if (isset($filtros['modelo'])) {
            $modeloSearch = Criteria::expr()->in('l.modeloId', $convertArray($filtros['modelo']));
            if (!is_array($filtros['modelo'])) {
                if (!is_numeric($filtros['modelo'])) {
                    $modeloSearch = Criteria::expr()->orX(
                        Criteria::expr()->contains('l.modelo', $filtros['modelo']),
                        Criteria::expr()->contains('l.modeloId', $filtros['modelo'])
                    );
                }
            }
            $searchCriteria->andWhere(
                $modeloSearch
            );
        }

        if (isset($filtros['ano'])) {
            $anoSearch = Criteria::expr()->in('l.ano', $convertArray($filtros['ano']));
            if (!is_array($filtros['ano'])) {
                if (!is_numeric($filtros['ano'])) {
                    $anoSearch = Criteria::expr()->orX(
                        Criteria::expr()->contains('l.ano', $filtros['ano'])
                    );
                }
            }
            $searchCriteria->andWhere(
                $anoSearch
            );
        }

        if (isset($filtros['comitente'])) {
            $searchCriteria->andWhere(
                Criteria::expr()->in('l.comitenteId', $convertArray($filtros['comitente']))
            );
        }

        if (isset($filtros['uf'])) {
            $searchCriteria->andWhere(
                Criteria::expr()->in('l.uf', $convertArray($filtros['uf']))
            );
        }

        if (isset($filtros['cidade'])) {
            $searchCriteria->andWhere(
                Criteria::expr()->in('l.cidade', $convertArray($filtros['cidade']))
            );
        }

        if (isset($filtros['busca'])) {
            $buscaOnlyDigits = preg_replace('/\D/', '$1', $filtros['busca']);
            if (empty(trim($buscaOnlyDigits))) {
                $buscaOnlyDigits = '000000000000000';
            }
            $searchCriteria->andWhere(
                Criteria::expr()->orX(
                    Criteria::expr()->contains('l.titulo', $filtros['busca']),
                    Criteria::expr()->contains('l.subtitulo', $filtros['busca']),
                    Criteria::expr()->contains('l.numero', $filtros['busca']),
                    Criteria::expr()->contains('l.numeroString', $filtros['busca']),
                    Criteria::expr()->contains('l.descricao', $filtros['busca']),
                    Criteria::expr()->contains('l.marca', $filtros['busca']),
                    Criteria::expr()->contains('l.modelo', $filtros['busca']),
                    Criteria::expr()->contains('l.ano', $filtros['busca']),
                    Criteria::expr()->contains('l.uf', $filtros['busca']),
                    Criteria::expr()->contains('l.cidade', $filtros['busca']),
                    Criteria::expr()->contains('l.bairro', $filtros['busca']),
                    Criteria::expr()->contains('l.processo', $filtros['busca']),
                    Criteria::expr()->contains('l.processo', $buscaOnlyDigits),
                    Criteria::expr()->contains('l.executado', $filtros['busca']),
                    Criteria::expr()->contains('l.exequente', $filtros['busca']),
                )
            );
        }

        $qb = $this->em->createQueryBuilder();
        $qb->select('l')->from(Lote::class, 'l');

        $qbCount = $this->em->createQueryBuilder()
            ->select('COUNT(1) total')
            ->from(Lote::class, 'l');

        foreach ($joins as $join) {
            $qb->leftJoin($join[0], $join[1]);
            if ($join[2]) {
                $qbCount->leftJoin($join[0], $join[1]);
            }
        }

        $qb->addCriteria($searchCriteria);
        $qbCount->addCriteria($searchCriteria);

        $qb->setMaxResults($limit)->setFirstResult($offset);

        if ($leilao) {
            $qb->orderBy('l.numero', 'ASC');
        }

        $total = intval($qbCount->getQuery()->getSingleScalarResult());
        return [
            'result' => $qb->getQuery()->getResult(),
            'limit' => $limit,
            'page' => ($offset + $limit) / $limit,
            'offset' => $offset,
            'offsetEnd' => $total > $limit ? ($offset + $limit) : $total,
            'total' => $total
        ];
    }

    /**
     * @param null $leilao
     * @param int $limit
     * @param int $offset
     * @param array $filtros
     *      $filtros = [
     *          'somenteAtivos' => (boolean) Busca somente leilões ativos
     *          'busca' => (string) Busca inteligente por leilões
     *          'data1' => (datetime) Data inicial
     *          'data2' => (datetime) Data final
     *          'statusTipo' => (mixed|int) Tipo do Status do Leilão (prop statusTipo)
     *          'tipoLeilao' => (array|int) 1 = Judicial; 2 = Extrajudicial;
     *          'relevancia' => (int) 0 = Relevância baseado no número e acessos e lances; 1 = Pela data do leilão (Crescente) [Default]; 2 = Valor (Crescente); 3 = Valor (Decrescente)
     *          'classificacaoLeilao' => (array/int) Classificação do leilão. Iniciativa privada, Seguradoras etc.
     *      ]
     * @return array|Lote
     */
    public function buscarLeiloes($limit = 100, $offset = 0, $filtros = [])
    {
        $searchCriteria = Criteria::create();

        $hoje = new \DateTime();
        $joins = [];
        //$joins[] = ['l.cache', 'cache', false];

        /**
         * Somente ativos lista os leilões com data igual ou superior ao dia de hoje e leilões que ainda estão com status ativo
         * O filtro da data se faz necessário para não ocultar leilões do dia que foram encerrados. Mesmo encerrado, ele deve ficar
         * no site até o fim do dia.
         */
        if (isset($filtros['somenteAtivos']) && ($filtros['somenteAtivos'] == '1' || $filtros['somenteAtivos'] === true || $filtros['somenteAtivos'] == 'true')) {
            /*$qb->andWhere('l.dataProximoLeilao > :hoje or l.statusTipo IN (:statusTipo)')
                ->setParameter('hoje', $hoje->format('Y-m-d ') . '00:00:00')
                ->setParameter('statusTipo', [Leilao::STATUS_TIPO_ABERTO, Leilao::STATUS_TIPO_EM_LEILAO]);*/

            $searchCriteria->andWhere(
                Criteria::expr()->orX(
                    Criteria::expr()->gt('l.dataProximoLeilao', $hoje->format('Y-m-d ') . '00:00:00'),
                    Criteria::expr()->in('l.statusTipo', [Leilao::STATUS_TIPO_ABERTO, Leilao::STATUS_TIPO_EM_LEILAO])
                )
            );
        }

        if (isset($filtros['statusTipo'])) {
            if (!in_array($filtros['statusTipo'], [Leilao::STATUS_TIPO_ABERTO, Leilao::STATUS_TIPO_EM_LEILAO, Leilao::STATUS_TIPO_ENCERRADO])) {
                throw new \Exception('Filtro statusTipo inválido');
            }
            /*$qb->andWhere('l.statusTipo IN (:statusTipo)')
                ->setParameter('statusTipo', is_array($filtros['statusTipo']) ? $filtros['statusTipo'] : [$filtros['statusTipo']]);*/
            $searchCriteria->andWhere(
                Criteria::expr()->in('l.statusTipo', is_array($filtros['statusTipo']) ? $filtros['statusTipo'] : [$filtros['statusTipo']]));
        }


        $qb = $this->em->createQueryBuilder();
        $qb->select('l')->from(Leilao::class, 'l');

        $qbCount = $this->em->createQueryBuilder()
            ->select('COUNT(1) total')
            ->from(Leilao::class, 'l');

        if (!isset($filtros['relevancia']) || $filtros['relevancia'] == '1') {
            $qb->orderBy('l.dataProximoLeilao', 'ASC');
        }

        foreach ($joins as $join) {
            $qb->leftJoin($join[0], $join[1]);
            if ($join[2]) {
                $qbCount->leftJoin($join[0], $join[1]);
            }
        }

        $qb->addCriteria($searchCriteria);
        $qbCount->addCriteria($searchCriteria);

        $qb->setMaxResults($limit)->setFirstResult($offset);

        // Filters

        $total = intval($qbCount->getQuery()->getSingleScalarResult());
        return [
            'result' => $qb->getQuery()->getResult(),
            'limit' => $limit,
            'page' => ($offset + $limit) / $limit,
            'offset' => $offset,
            'offsetEnd' => $total > $limit ? ($offset + $limit) : $total,
            'total' => $total
        ];
    }

    /**
     * Retorna os tipos de bem baseado na montagem de cache
     */
    public function getTiposBem()
    {
        return $this->em->getRepository(LoteTipoCache::class)->findBy([], ['tipo' => 'ASC']);
    }

    public function getBanners($filtros = [])
    {
        return $this->em->getRepository(Banner::class)->findAtivos();
    }

}