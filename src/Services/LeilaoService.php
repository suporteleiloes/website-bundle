<?php


namespace SL\WebsiteBundle\Services;


use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use SL\WebsiteBundle\Entity\Banner;
use SL\WebsiteBundle\Entity\Lance;
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
     *          'tipo' => (array|int|string(separator: ;)) Tipo do Bem
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
     *          'comUltimoLance' => (boolean) Adiciona o último lance à query
     *          'order' => (array) [field, orderType]
     *          'responseType' => (string|'array','object' ? default = 'object') Adiciona o último lance à query
     *      ]
     * @return array|Lote
     */
    public function buscarBens($leilao = null, $somenteAtivos = true, $limit = 100, $offset = 0, $filtros = [])
    {
        $searchCriteria = Criteria::create();

        $hoje = new \DateTime();
        $joins = [];

        $convertArray = function ($value, $separador = null) {
            if ($separador && !is_array($value)) {
                return explode($separador, $value);
            }
            return is_array($value) ? $value : [$value];
        };

        $isTrue = function ($value) {
            $values = is_string($value) ? strtolower($value) : $value;
            return $value === true || $values == 1 || $values == 's' || $values == 'sim' || $values == 'y';
        };

        if ($leilao) {
            $searchCriteria->andWhere(Criteria::expr()->eq('l.leilao', $leilao));
        }

        $joins[] = ['l.leilao', 'leilao', true];

        if ($somenteAtivos) {
            $searchCriteria->andWhere(Criteria::expr()->eq('l.active', true));
            $searchCriteria->andWhere(Criteria::expr()->lt('l.status', 5));
            $searchCriteria->andWhere(
                Criteria::expr()->orX(
                    Criteria::expr()->eq('l.leilao', null),
                    Criteria::expr()->lte('leilao.statusTipo', 2),
                    Criteria::expr()->eq('l.vendaDireta', true)
                )
            );
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
            if ($filtros['vendaDireta'] == 0) {
                $vdCrit = Criteria::expr()->orX(
                    Criteria::expr()->eq('l.vendaDireta', false),
                    Criteria::expr()->orX(
                        Criteria::expr()->isNull('l.leilao'),
                        Criteria::expr()->eq('leilao.vendaDireta', false)
                    )
                );
            } else {
                $vdCrit = Criteria::expr()->andX(
                    Criteria::expr()->eq('l.vendaDireta', true),
                    Criteria::expr()->orX(
                        Criteria::expr()->isNull('l.leilao'),
                        Criteria::expr()->eq('leilao.vendaDireta', true)
                    )
                );
            }
            $searchCriteria->andWhere(
                $vdCrit
            );
        }

        if (isset($filtros['ocupacao'])) {
            $searchCriteria->andWhere(
                Criteria::expr()->eq('l.ocupado', $isTrue($filtros['ocupacao']))
            );
        }

        if (isset($filtros['judicial'])) {
            $searchCriteria->andWhere(
                Criteria::expr()->eq('leilao.judicial', $isTrue($filtros['judicial']))
            );
        }

        if (isset($filtros['tipo'])) {
            $tipoArr = $convertArray($filtros['tipo'], ';');
            if (count($tipoArr) > 0) {
                $tipoSearch = Criteria::expr()->orX(
                    Criteria::expr()->in('l.tipo', $tipoArr),
                    Criteria::expr()->in('l.tipoPai', $tipoArr)
                );
            } else {
                $tipoSearch = Criteria::expr()->in('l.tipoId', $convertArray($filtros['tipo']));
                if (!is_array($filtros['tipo'])) {
                    if (!is_numeric($filtros['tipo'])) {
                        $tipoSearch = Criteria::expr()->orX(
                            Criteria::expr()->eq('l.tipo', $filtros['tipo']),
                            Criteria::expr()->eq('l.tipo', $filtros['tipo'] . 's'),
                            Criteria::expr()->eq('l.tipoPai', $filtros['tipo']),
                            Criteria::expr()->eq('l.tipoPai', $filtros['tipo'] . 's')
                        );
                    }
                }
            }
            $searchCriteria->andWhere(
                $tipoSearch
            );
        }

        if (isset($filtros['tipo-not'])) {
            $filtros['tipo-not'] = $convertArray($filtros['tipo-not'], ',');
            $tipoSearch = Criteria::expr()->andX(
                Criteria::expr()->notIn('l.tipo', $filtros['tipo-not']),
                Criteria::expr()->notIn('l.tipoPai', $filtros['tipo-not']),
            );
            $searchCriteria->andWhere(
                $tipoSearch
            );
        }

        if (isset($filtros['tipo-id-not'])) {
            $filtros['tipo-id-not'] = $convertArray($filtros['tipo-id-not'], ',');
            $tipoSearch = Criteria::expr()->orX(
                Criteria::expr()->in('l.tipoId', $filtros['tipo-id-not']),
                Criteria::expr()->in('l.tipoPaiId', $filtros['tipo-id-not']),
            );
            $searchCriteria->andWhere(
                $tipoSearch
            );
        }

        if (isset($filtros['tipoId'])) {
            if (!is_array($filtros['tipoId'])) {
                $filtros['tipoId'] = explode(',', $filtros['tipoId']);
            }
            $searchCriteria->andWhere(
                Criteria::expr()->in('l.tipoId', $filtros['tipoId'])
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
                Criteria::expr()->in('l.comitenteId', $convertArray($filtros['comitente'], ','))
            );
        }

        if (isset($filtros['finalidade'])) {
            $searchCriteria->andWhere(
                Criteria::expr()->in('l.finalidade', $convertArray($filtros['finalidade'], ','))
            );
        }

        if (isset($filtros['uf'])) {
            $searchCriteria->andWhere(
                Criteria::expr()->in('l.uf', $convertArray($filtros['uf']))
            );
        }

        if (isset($filtros['cidade'])) {
            $searchCriteria->andWhere(
                Criteria::expr()->in('l.cidade', $convertArray($filtros['cidade'], ','))
            );
        }

        if (isset($filtros['bairro'])) {
            $searchCriteria->andWhere(
                Criteria::expr()->in('l.bairro', $convertArray($filtros['bairro']))
            );
        }

        if (isset($filtros['codigo'])) {
            $searchCriteria->andWhere(
                Criteria::expr()->in('l.aid', $convertArray($filtros['codigo']))
            );
        }

        if (isset($filtros['precoMinimo'])) {
            $searchCriteria->andWhere(
                Criteria::expr()->orX(
                    Criteria::expr()->gte('l.valorInicial', $filtros['precoMinimo']),
                    Criteria::expr()->gte('l.valorInicial2', $filtros['precoMinimo']),
                )
            );
        }

        if (isset($filtros['precoMaximo'])) {
            $searchCriteria->andWhere(
                Criteria::expr()->orX(
                    Criteria::expr()->lte('l.valorInicial', $filtros['precoMaximo']),
                    Criteria::expr()->lte('l.valorInicial2', $filtros['precoMaximo']),
                )
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
                    Criteria::expr()->contains('l.comitente', $filtros['busca'])
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

        if (isset($filtros['comUltimoLance'])) {
            $qbLance = $this->em->createQueryBuilder()
                ->select('MAX(lance.id) maiorLance')
                ->from(Lance::class, 'lance')
                ->where('lance.lote = l')
                ->setMaxResults(1);

            $qb->leftJoin('l.lances', 'lances', Join::WITH, $qb->expr()->eq('lances.id', '(' . $qbLance->getDQL() . ')'));
            $qb->addSelect('lances');
        }

        if (isset($filtros['addLeftjoin'])) {
            $qb->leftJoin($filtros['addLeftjoin'][0], $filtros['addLeftjoin'][1]);
        }
        if (isset($filtros['addSelect'])) {
            $qb->addSelect($filtros['addSelect']);
        }

        $qb->setMaxResults($limit)->setFirstResult($offset);

        if (isset($filtros['order'])) {
            $qb->addOrderBy($filtros['order'][0], $filtros['order'][1]);
        } else {
            $qb->addOrderBy('l.order', 'ASC');

            if ($leilao) {
                $qb->addOrderBy('l.numero', 'ASC');
            }
        }

        $total = intval($qbCount->getQuery()->getSingleScalarResult());

        if (isset($filtros['responseType']) && $filtros['responseType'] === 'array') {
            $result = $qb->getQuery()->getArrayResult();
        } else {
            $result = $qb->getQuery()->getResult();
        }

        return [
            'result' => $result,
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
     *          'status' => (mixed|int) Status do Leilão
     *          'statusTipo' => (mixed|int) Tipo do Status do Leilão (prop statusTipo)
     *          'tipoLeilao' => (int) 1 = Judicial; 2 = Extrajudicial; Null para ambos.
     *          'relevancia' => (int) 0 = Relevância baseado no número e acessos e lances; 1 = Pela data do leilão (Crescente) [Default]; 2 = Valor (Crescente); 3 = Valor (Decrescente)
     *          'classificacaoLeilao' => (array/int) Classificação do leilão. Iniciativa privada, Seguradoras etc.
     *          'comPrimeiroLote' => (boolean) Faz o join do primeiro lote
     *          'vendaDireta' => (int) 0/Null = Ambos; 1 = Somente Venda Direta; 2 = Somente Leilões
     *          'modalidade' => (int) 1 = Online; 2 = Presencial; Null para ambos.
     *          'order' => (array) Define por qual campo ordenar o resultado. Ex.: ['l.dataProximoLeilao', 'DESC']
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
            $searchCriteria->andWhere(
                Criteria::expr()->in('l.statusTipo', is_array($filtros['statusTipo']) ? $filtros['statusTipo'] : [$filtros['statusTipo']]));
        }

        if (isset($filtros['status'])) {
            $searchCriteria->andWhere(
                Criteria::expr()->in('l.status', is_array($filtros['status']) ? $filtros['status'] : [$filtros['status']]));
        }

        if (isset($filtros['tipoLeilao'])) {
            if (!in_array($filtros['tipoLeilao'], [1, 2])) {
                throw new \Exception('Filtro tipoLeilao inválido');
            }
            $searchCriteria->andWhere(
                Criteria::expr()->eq('l.judicial', $filtros['tipoLeilao'] === 1)
            );
        }

        if (isset($filtros['modalidade'])) {
            if (!in_array($filtros['modalidade'], [1, 2])) {
                throw new \Exception('Filtro modalidade inválido');
            }
            $searchCriteria->andWhere(
                Criteria::expr()->eq('l.tipo', $filtros['modalidade'])
            );
        }

        if (isset($filtros['busca'])) {
            $searchCriteria->andWhere(
                Criteria::expr()->orX(
                    Criteria::expr()->contains('l.titulo', $filtros['busca']),
                    Criteria::expr()->contains('l.descricao', $filtros['busca']),
                    Criteria::expr()->contains('l.leiloeiro', $filtros['busca'])
                )
            );
        }

        if (isset($filtros['vendaDireta']) && $filtros['vendaDireta']) {
            $searchCriteria->andWhere(
                Criteria::expr()->eq('l.vendaDireta', $filtros['vendaDireta'] === 1)
            );
        }

        $qb = $this->em->createQueryBuilder();
        $qb->select('l')->from(Leilao::class, 'l');

        $qbCount = $this->em->createQueryBuilder()
            ->select('COUNT(1) total')
            ->from(Leilao::class, 'l');

        if (isset($filtros['order'])) {
            if (!is_array($filtros['order'])) {
                $order = $filtros['order'];
                $orderType = 'ASC';
            } else {
                $order = $filtros['order'][0];
                $orderType = $filtros['order'][1];
            }
            $qb->orderBy($order, $orderType);
        } elseif (!isset($filtros['relevancia']) || $filtros['relevancia'] == '1') {
            $qb->orderBy('l.dataProximoLeilao', 'ASC');
        }

        foreach ($joins as $join) {
            $qb->leftJoin($join[0], $join[1]);
            if ($join[2]) {
                $qbCount->leftJoin($join[0], $join[1]);
            }
        }

        if (isset($filtros['comPrimeiroLote'])) {
            /*$qbLance = $this->em->createQueryBuilder()
                ->select('MAX(lance.id) maiorLance')
                ->from(Lance::class, 'lance')
                ->join('lance.lote', 'loteLance')
                ->where('loteLance.leilao = l')
                ->setMaxResults(1);*/

            $qbLote = $this->em->createQueryBuilder()
                ->select('MIN(lote.id)')
                ->from(Lote::class, 'lote')
                ->where('lote.leilao = l')
                ->orderBy('lote.order', 'ASC')
                ->addOrderBy('lote.numero', 'ASC')
                ->setMaxResults(1);
            $qb->leftJoin('l.lotes', 'lotes', Join::WITH, $qb->expr()->eq('lotes.id', '(' . $qbLote->getDQL() . ')'));
            //$qb->leftJoin('lotes.lances', 'lances', Join::WITH, $qb->expr()->eq( 'lances.id', '('.$qbLance->getDQL().')' ));
            //$qb->addSelect('lotes, lances');
            $qb->addSelect('lotes');
        }

        $qb->addCriteria($searchCriteria);
        $qbCount->addCriteria($searchCriteria);

        $qb->setMaxResults($limit)->setFirstResult($offset);

        // Filters

        $total = intval($qbCount->getQuery()->getSingleScalarResult());

        if (isset($filtros['responseType']) && $filtros['responseType'] === 'array') {
            $result = $qb->getQuery()->getArrayResult();
        } else {
            /*$paginator = new Paginator($qb->getQuery(), true);
            $result = (array)$paginator->getIterator();
            $total = count($paginator);*/
            $result = $qb->getQuery()->getResult();
        }
        $page = ($offset + $limit) / $limit;
        return [
            'result' => $result,
            'limit' => $limit,
            'page' => $page,
            'offset' => $offset,
            'offsetEnd' => $total > $limit ? ($offset + $limit) : $total,
            "totalPages" => intval(ceil(intval($total) / $limit)),
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