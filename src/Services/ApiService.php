<?php


namespace SL\WebsiteBundle\Services;


use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\RequestOptions;
use SL\WebsiteBundle\Entity\Banner;
use SL\WebsiteBundle\Entity\Content;
use SL\WebsiteBundle\Entity\Lance;
use SL\WebsiteBundle\Entity\Leilao;
use SL\WebsiteBundle\Entity\LeilaoCache;
use SL\WebsiteBundle\Entity\Lote;
use SL\WebsiteBundle\Entity\LoteTipoCache;
use SL\WebsiteBundle\Entity\Post;
use SL\WebsiteBundle\Helpers\Sluggable;
use SL\WebsiteBundle\Helpers\Utils;
use Symfony\Component\Security\Core\Security;

class ApiService
{
    private $em;
    private $apiUrl;
    private $apiClient;
    private $apiKey;
    private $security;
    public static $client;
    public static $clientAuthenticated;

    public function __construct(EntityManagerInterface $em, $apiUrl, $apiClient, $apiKey, Security $security)
    {
        $this->em = $em;
        $this->apiUrl = $apiUrl;
        $this->apiClient = $apiClient;
        $this->apiKey = $apiKey;
        $this->security = $security;
    }

    function getClient($token = null)
    {
        if ($token) {
            $token = $this->security->getUser()->getExtraFields()['token'];
            if (!isset(self::$clientAuthenticated)) {
                self::$clientAuthenticated = new Client(array(
                    'timeout' => 100,
                    'base_uri' => $this->apiUrl,
                    'headers' => [
                        'uloc-mi' => $this->apiClient,
                        'X-AUTH-TOKEN' => $this->apiKey,
                        'Authorization' => 'Bearer ' . $token,
                        'User-Agent' => @$_SERVER['HTTP_USER_AGENT'],
                        'X_FORWARDED_FOR' => Utils::get_client_ip_env(),
                    ],
                    'verify' => false
                ));
            }
            return self::$clientAuthenticated;
        } else {
            if (!isset(self::$client)) {
                self::$client = new Client(array(
                    'timeout' => 100,
                    'base_uri' => $this->apiUrl,
                    'headers' => [
                        'uloc-mi' => $this->apiClient,
                        'X-AUTH-TOKEN' => $this->apiKey,
                        'User-Agent' => @$_SERVER['HTTP_USER_AGENT'],
                        'X_FORWARDED_FOR' => Utils::get_client_ip_env(),
                    ],
                    'verify' => false
                ));
            }
            return self::$client;
        }
    }

    public function callApi($method, $endpoint, $data = [], $userAuth = false)
    {
        try {
            $response = $this->getClient($userAuth)->request($method, $endpoint, $data);
        } catch (ClientException $e) {
            $this->requestError($e);
        } catch (\Throwable $exception) {
            throw $exception;
        }
        return json_decode($response->getBody(), true);
    }

    protected function requestError($e)
    {
        $body = json_decode($e->getResponse()->getBody(), true);
        if (isset($body['detail'])) {
            throw new \Exception(is_array($body['detail']) ? serialize($body['detail']) : $body['detail']);
        }
        if (isset($body['error'])) {
            throw new \Exception((is_array($body['message']) ? (string)join($body['message'], ', ') : $body['message']));
        }
        try {
            throw new \Exception((string)$body);
        } catch (\Throwable $exception) {
            throw new \Exception((string)$e->getResponse()->getBody());
        }
    }

    public function requestToken($username, $password)
    {
        try {
            $response = $this->getClient()->request('POST', '/api/auth', [
                'form_params' => [
                    'user' => $username,
                    'pass' => $password,
                    'needRole' => 'ROLE_ARREMATANTE'
                ]
            ]);
        } catch (ClientException $e) {
            $body = json_decode($e->getResponse()->getBody(), true);
            if (isset($body['detail'])) {
                throw new \Exception($body['detail']);
            }
            throw new \Exception((string)$body);
        } catch (\Throwable $exception) {
            throw $exception;
        }
        return json_decode($response->getBody(), true);
    }

    public function requestAllActiveSiteData()
    {
        return $this->getClient()->request('POST', '/api/public/site/all-data', [
        ]);
    }

    /**
     * Capture
     */
    public function processLeilao($data, $autoFlush = true)
    {
        if (isset($data['webhookStructure'])) {
            $data = $data['data'];
        }
        $entityId = $data['id'];
        //dump('Migrando leilão de ID ' . $entityId);
        if (intval($data['status']) === 0) {
            return; // Rascunho
        }
        // Verifica se já existe o leilao. Se não existir, cria um.
        $em = $this->em;
        $leilao = $em->getRepository(Leilao::class)->findOneByAid($entityId);
        if (!$leilao) {
            //dump('Leilão de ID ' . $entityId . ' não encontrado, criando novo!');
            if (@$data['deleted']) {
                return;
            }
            $leilao = new Leilao();
        }

        $leilao->setAid($entityId);
        $is_true = function ($val, $return_null = false) {
            $boolval = (is_string($val) ? filter_var($val, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) : (bool)$val);
            return ($boolval === null && !$return_null ? false : $boolval);
        };
        if ($leilao->getId()) {
            if ($data['deleted'] || !$is_true($data['publicarSite'])) {
                $leilao->setDeleted(true);
                if ($autoFlush) $em->flush();
                return;
            }
            $leilao->setAlastUpdate(new \DateTime());
        } else {
            $leilao->setAcreatedAt(new \DateTime());
        }

        $praca = isset($data['praca']) ? intval($data['praca']) : 1;
        $instancia = isset($data['instancia']) ? intval($data['instancia']) : 1;
        $data1 = isset($data['data1']) ? \DateTime::createFromFormat('Y-m-d H:i:s+', $data['data1']['date']) : null;
        $data2 = isset($data['data2']) ? \DateTime::createFromFormat('Y-m-d H:i:s+', $data['data2']['date']) : null;
        $data3 = isset($data['data3']) ? \DateTime::createFromFormat('Y-m-d H:i:s+', $data['data3']['date']) : null;
        $dataAbertura1 = isset($data['dataAbertura1']) ? \DateTime::createFromFormat('Y-m-d H:i:s+', $data['dataAbertura1']['date']) : null;
        $dataAbertura2 = isset($data['dataAbertura2']) ? \DateTime::createFromFormat('Y-m-d H:i:s+', $data['dataAbertura2']['date']) : null;
        $dataAbertura3 = isset($data['dataAbertura3']) ? \DateTime::createFromFormat('Y-m-d H:i:s+', $data['dataAbertura3']['date']) : null;
        $leilao->setData1($data1);
        $leilao->setData2($data2);
        $leilao->setData3($data3);
        $leilao->setDataAbertura1($dataAbertura1);
        $leilao->setDataAbertura2($dataAbertura2);
        $leilao->setDataAbertura3($dataAbertura3);

        $leilao->setSlug(substr($data['slug'], 0, 254));
        $leilao->setTitulo($data['titulo']);
        $leilao->setDescricao(@$data['descricao']);
        $leilao->setTipo($data['tipo']);


        $leilao->setDataProximoLeilao(isset($data['dataProximoLeilao']) ? \DateTime::createFromFormat('Y-m-d H:i:s+', $data['dataProximoLeilao']['date']) : null);

        $leilao->setActive($data['active'] ?: true);
        $leilao->setOrder($data['order'] ?: 99);
        $leilao->setDeleted($data['deleted'] ?: false);
        $leilao->setJudicial($data['judicial']);
        $leilao->setTotalLotes($data['totalLotes']);
        $leilao->setStatus($data['status']);
        $leilao->setStatusString(@$data['statusString']);
        $leilao->setStatusTipo(@$data['statusTipo']);
        $leilao->setPraca($praca);
        $leilao->setInstancia($data['instancia'] ?? null);
        $leilao->setLeiloeiro($data['leiloeiro']['nome']);
        $leilao->setLocal($data['patio']);
        $leilao->setInfoVisitacao($data['infoVisitacao']);
        $leilao->setInfoRetirada($data['infoRetirada']);
        $leilao->setObservacoes($data['observacao']);
        $leilao->setArquivos($data['documentos']);
        $leilao->setComitentes($data['comitentes']);
        $leilao->setExtra($data['extra']);
        $leilao->setDeleted($data['deleted']);
        $leilao->setImage($data['image']);
        $leilao->setDestaque($data['destaque']);

        $leilao->setCep(@$data['cep']);
        $leilao->setEndereco(@$data['endereco']);
        $leilao->setEnderecoNumero(@$data['enderecoNumero']);
        $leilao->setBairro(@$data['bairro']);
        $leilao->setEnderecoReferencia(@$data['enderecoReferencia']);

        $leilao->setTimezone(@$data['timezone']);
        $leilao->setVendaDireta(@$data['vendaDireta']);
        $leilao->setHabilitacao(@$data['habilitacao']);

        $leilao->setPermitirParcelamento(@$data['permitirParcelamento']);
        $leilao->setParcelamentoQtdParcelas(@$data['parcelamentoQtdParcelas']);
        $leilao->setParcelamentoIndices(@$data['parcelamentoIndices']);
        $leilao->setPermitirPropostas(@$data['permitirPropostas']);

        $leilao->setVideo(@$data['video']);
        $leilao->setRegras(@$data['regras']);
        $leilao->setTextoPropostas(@$data['textoPropostas']);
        $leilao->setSistemaTaxa($data['sistemaTaxa'] ?? null);

        $em->persist($leilao);
        #//dump('Persistindo leilão ID ' . $data['id']);
        if ($autoFlush) $em->flush();

        if (isset($data['lotes']) && is_array($data['lotes']) && count($data['lotes'])) {
            #//dump('Migrando lotes: ' . count($data['lotes']));
            foreach ($data['lotes'] as $lote) {
                $this->processLote($lote, true, false, $leilao);
            }
        }

        //$this->geraCacheLotes();
        $this->geraCacheLeilao($leilao);
        //$this->em->clear();
    }

    public function processLote($data, $autoFlush = true, $enableCache = true, $leilao = null)
    {
        $isTree = false;
        if ($leilao) {
            $isTree = true;
        }
        if (isset($data['webhookStructure'])) {
            $_data = $data;
            $data = $data['data'];
        }
        $entityId = $data['id'];
        if (intval($data['status']) === 0) {
            #return; // Rascunho ###@TODO: REMOVER
        }
        // Verifica se já existe o lote. Se não existir, cria um.
        $em = $this->em;
        $lote = $em->getRepository(Lote::class)->findOneBy([
            'aid' => $entityId,
            'bemId' => $data['bem']['id']
        ]);
        if (!$lote) {
            if (@$data['deleted']) {
                return;
            }
            $lote = new Lote();
        } else {
            #//dump('Achou lote ' . $data['id']);
        }

        if ((isset($_data) && $_data['remove']) || $data['deleted']) {
            $em->remove($lote);
            if ($autoFlush) $em->flush();
            return;
        }

        $lote->setAid($entityId);
        if ($lote->getId()) {
            $lote->setAlastUpdate(new \DateTime());
        } else {
            $lote->setAcreatedAt(new \DateTime());
        }

        $lote->setSlug(!empty($data['slug']) ? substr($data['slug'], 0, 254) : (!empty($data['bem']['slug']) ? substr($data['bem']['slug'], 0, 254) : 'lote'));

        // Lt
        if (isset($data['active']) && $data['active']) {
            $lote->setActive(true);
        } else {
            $lote->setActive(intval($data['status']) < 5); // @TODO: Provisório ou permanente?
        }
        $lote->setNumero($data['numero']);
        $lote->setNumeroString($data['numeroString'] ?? null);
        $lote->setDeleted($data['deleted'] ?: false);
        $lote->setOrder($data['order'] ?? 99);
        $lote->setValorInicial($data['valorInicial']);
        $lote->setValorInicial2($data['valorInicial2']);
        $lote->setValorInicial3($data['valorInicial3']);
        $lote->setValorIncremento($data['valorIncremento']);
        $lote->setValorMercado($data['valorMercado']);
        $lote->setValorMinimo($data['valorMinimo']);
        $lote->setValorAvaliacao($data['valorAvaliacao']);
        $lote->setPermitirParcelamento(@$data['permitirParcelamento']);
        $lote->setParcelamentoQtdParcelas(@$data['parcelamentoQtdParcelas']);
        $lote->setParcelamentoIndices(@$data['parcelamentoIndices']);
        $lote->setPermitirPropostas(@$data['permitirPropostas']);
        $lote->setStatus($data['status']);
        $lote->setStatusString($data['statusString']);
        //$lote->setStatusCor($data['statusCor']);

        // Bem
        $lote->setBemId($data['bem']['id']);
        $lote->setTitulo($data['bem']['siteTitulo']);
        $lote->setSubtitulo(!empty($data['bem']['siteSubtitulo']) ? $data['bem']['siteSubtitulo'] : strip_tags(mb_substr($data['bem']['siteDescricao'], 0, 254)));
        $lote->setDescricao($data['bem']['siteDescricao']);
        $lote->setObservacao($data['bem']['siteObservacao']);
        $lote->setDocumentos($data['bem']['arquivos']);
        $lote->setFoto($data['bem']['image']);
        $lote->setComitenteId($data['bem']['comitente']['id']);
        $lote->setComitente($data['bem']['comitente']['pessoa']['name']);
        $lote->setComitenteLogo($data['bem']['comitente']['image']);
        $lote->setComitenteTipoId(@$data['bem']['comitente']['tipo']['id']);
        $lote->setComitenteTipo(@$data['bem']['comitente']['tipo']['nome']);
        $lote->setMarcaId(@$data['bem']['marca']['id']);
        $lote->setMarca(@$data['bem']['marca']['nome']);
        $lote->setModeloId(@$data['bem']['modelo']['id']);
        $lote->setModelo(@$data['bem']['modelo']['nome']);
        $lote->setAno(@$data['bem']['anoModelo']);
        $lote->setCidade(@$data['bem']['cidade']);
        $lote->setUf(@$data['bem']['uf']);
        $lote->setBairro(@$data['bem']['bairro']);
        $lote->setTipoId(@$data['bem']['tipo']['id']);
        $lote->setTipo(@$data['bem']['tipo']['nome']);
        $lote->setTipoSlug(Sluggable::slugify(@$data['bem']['tipo']['nome']));
        $lote->setTipoPaiId(isset($data['bem']['tipo']['parent']) ? $data['bem']['tipo']['parent']['id'] : @$data['bem']['tipoPaiId']);
        $lote->setTipoPai(isset($data['bem']['tipo']['parent']) ? $data['bem']['tipo']['parent']['nome'] : @$data['bem']['tipoPai']);
        $lote->setTipoPaiSlug(Sluggable::slugify(isset($data['bem']['tipo']['parent']) ? $data['bem']['tipo']['parent']['nome'] : @$data['bem']['tipoPai']));
        $lote->setExtra($data['extra'] ?? null);
        $lote->setBemExtra($data['bem']['extra'] ?? null);
        $lote->setDestaque(@$data['bem']['destaque']); // TODO: Bem ou lote ?
        $lote->setConservacaoId(@$data['bem']['conservacao']['id']);
        $lote->setConservacao(@$data['bem']['conservacao']['nome']);
        $lote->setProcesso(@$data['bem']['processoNumero']);
        $lote->setExequente(@$data['bem']['processoExequente']);
        $lote->setExecutado(@$data['bem']['processoExecutado']);
        $lote->setLocalizacaoUrlGoogleMaps(@$data['bem']['localizacaoUrlGoogleMaps']);
        $lote->setLocalizacaoUrlStreetView(@$data['bem']['localizacaoUrlStreetView']);
        $lote->setLocalizacaoMapEmbed(@$data['bem']['localizacaoMapEmbed']);
        $lote->setVideos(@$data['bem']['videos']);
        $lote->setCamposExtras(@$data['bem']['camposExtras']);
        $lote->setTour360(@$data['bem']['tour360']);

        if (isset($data['leilao']) || $leilao) {
            /* @var Leilao $leilao */
            $leilao = $leilao ?? $em->getRepository(Leilao::class)->findOneByAid($data['leilao']['id']);
            if ($leilao) {
                $lote->setLeilao($leilao);
            }
            $lote->setPermitirLance(@$data['permitidoLance']);
        } else {
            $lote->setVendaDireta(@$data['bem']['vendaDireta']);
            #//dump('Lote sem leilão');
        }
        if(!$leilao && (!isset($data['bem']['vendaDireta']) || !$data['bem']['vendaDireta'])) {
            return; // Lote atualizado de leilão encerrado e inexistente na base do site
        }
        #//dump('Persistindo lote ID ' . $data['id']);
        $em->persist($lote);

        if ($autoFlush) $em->flush();

        if (isset($data['lances']) && is_array($data['lances']) && count($data['lances'])) {
            foreach($data['lances'] as $lance) {
                $this->processLance($lance);
            }
        }

        //if (isset($leilao) && !$isTree) $this->geraCacheLeilao($leilao);
        if ($enableCache && !$isTree) $this->geraCacheLotes();

        // Atualiza total lotes
        if (isset($leilao)) {
            $leilao->setTotalLotes($em->createQueryBuilder()->select('count(1)')->from(Lote::class, 'l')->where('l.leilao = :leilao')->setParameter('leilao', $leilao->getId())->getQuery()->getSingleScalarResult());
            $this->geraCacheLeilao($leilao);
            $em->persist($leilao);
            if ($autoFlush) $em->flush();
        }
    }

    public function processLance($data, $autoFlush = true)
    {
        if (isset($data['webhookStructure'])) {
            $data = $data['data'];
        }
        $entityId = $data['id'];
        // Verifica se já existe o lance. Se não existir, cria um.
        $em = $this->em;

        $lance = $em->getRepository(Lance::class)->findOneByAid($entityId);
        if (!$lance) {
            if ($data['deleted']) {
                return;
            }
            $lance = new Lance();
        }


        $lance->setAid($entityId);
        if ($lance->getId()) {
            if ($data['deleted']) {
                $em->remove($lance);
                if ($autoFlush) $em->flush();
                return;
            }
            $lance->setAlastUpdate(new \DateTime());
        } else {
            $lance->setAcreatedAt(new \DateTime());
        }

        $lance->setData(\DateTime::createFromFormat('Y-m-d H:i:s+', $data['data']['date']));
        $lance->setArrematanteId(isset($data['autor']) ? $data['autor']['id'] : $data['arrematante']['id']);
        $lance->setApelido(isset($data['autor']) ? $data['autor']['apelido'] : $data['arrematante']['apelido']);
        $lance->setNome($data['arrematante']['pessoa']['name']);
        $lance->setCidade(isset($data['autor']) ? $data['autor']['cidade'] : null);
        $lance->setUf(isset($data['autor']) ? $data['autor']['uf'] : null);
        $lance->setValor($data['valor']);
        $lance->setActive($data['active'] ?: true);
        $lance->setDeleted($data['deleted'] ?: false);
        /* @var Lote $lote */
        $lote = $em->getRepository(Lote::class)->findOneByAid($data['lote']['id']);
        if ($lote) {
            $lance->setLote($lote);
            $lote->addLance($lance);
        } else {
            #//dump('Lance sem lote.');
            #//dump($data);
        }
        #//dump('Persistindo lance ID ' . $data['id'] . ' do lote ID ' . $data['lote']['id']);
        $em->persist($lance);
        if ($autoFlush) $em->flush();
    }

    public function geraCacheLeilao(Leilao $leilao, $autoFlush = true)
    {
        //dump('Gerando cache do leilão ' . $leilao->getId());
        $filtros = $this->em->getRepository(Leilao::class)->filtros($leilao->getId());
        $cache = $this->em->getRepository(LeilaoCache::class)->findOneByLeilao($leilao->getId()) ?? new LeilaoCache();
        $cache->setFiltros($filtros);
        $cache->setLeilao($leilao);
        //$leilao->setCache($cache);
        $em = $this->em;
        $em->persist($cache);
        //$em->persist($leilao);
        if ($autoFlush) $em->flush();
    }

    public function processContent($data, $autoFlush = true)
    {
        $entityId = $data['entityId'];
        $em = $this->em;
        $entity = $em->getRepository(Content::class)->findOneByAid($entityId);
        if (!$entity) {
            $entity = new Content();
        }

        $data = $data['data'];

        if ($entity->getId()) {
            if ($data['deleted']) {
                $em->remove($entity);
                if ($autoFlush) $em->flush();
                return;
            }
            $entity->setAlastUpdate(new \DateTime());
        } else {
            $entity->setAcreatedAt(new \DateTime());
        }

        $entity->setAid(@$data['id']);
        $entity->setActive(@$data['active']);
        $entity->setTitle(@$data['title']);
        $entity->setPageName(@$data['pageName']);
        $entity->setPageDescription(@$data['pageDescription']);
        $entity->setTemplate(@$data['template']);

        $em->persist($entity);
        if ($autoFlush) $em->flush();
    }

    public function processBanner($data, $autoFlush = true)
    {
        $entityId = $data['entityId'];
        $em = $this->em;
        $banner = $em->getRepository(Banner::class)->findOneByAid($entityId);
        if (!$banner) {
            $banner = new Banner();
        }

        $data = $data['data'];

        $banner->setAid($entityId);
        if ($banner->getId()) {
            if ($data['deleted']) {
                $em->remove($banner);
                if ($autoFlush) $em->flush();
                return;
            }
            $banner->setAlastUpdate(new \DateTime());
        } else {
            $banner->setAcreatedAt(new \DateTime());
        }

        $banner->setType(@$data['type']);
        $banner->setTitle(@$data['title']);
        $banner->setPosition(@$data['position']);
        $banner->setDateStartExhibition(isset($data['dateStartExhibition']) ? \DateTime::createFromFormat('Y-m-d H:i:s+', $data['dateStartExhibition']['date']) : null);
        $banner->setDateEndExhibition(isset($data['dateEndExhibition']) ? \DateTime::createFromFormat('Y-m-d H:i:s+', $data['dateEndExhibition']['date']) : null);
        $banner->setHasVideo(@$data['hasVideo']);
        $banner->setImage(@$data['image']);
        $banner->setLink(@$data['link']);
        $banner->setActive(@$data['active']);
        $banner->setDeleted(@$data['deleted']);

        $em->persist($banner);
        if ($autoFlush) $em->flush();
    }

    public function processPost($data, $autoFlush = true)
    {
        $entityId = $data['entityId'];
        $em = $this->em;
        $entity = $em->getRepository(Post::class)->findOneByAid($entityId);
        if (!$entity) {
            $entity = new Post();
        }

        $data = $data['data'];

        $entity->setAid($entityId);
        if ($entity->getId()) {
            if ($data['deleted']) {
                $em->remove($entity);
                if ($autoFlush) $em->flush();
                return;
            }
            $entity->setAlastUpdate(new \DateTime());
        } else {
            $entity->setAcreatedAt(new \DateTime());
        }

        $entity->setTitle(@$data['title']);
        $entity->setImage(@$data['image']);
        $entity->setDescription(@$data['description']);
        $entity->setTemplate(@$data['template']);
        $entity->setUrl(@$data['url']);
        $entity->setActive(@$data['active']);
        $entity->setOrder(@$data['order']);
        if (isset($data['category'])) {
            $entity->setCategoryId(@$data['category']['id']);
            $entity->setCategory(@$data['category']['name']);
        }

        $em->persist($entity);
        if ($autoFlush) $em->flush();
    }

    public function geraCacheLotes($autoFlush = true)
    {
        $em = $this->em;
        $em->getRepository(LoteTipoCache::class)->flushData();
        if ($autoFlush) $em->flush();
        $check = [];
        $totais = $this->em->getRepository(Lote::class)->totalLotesByTipo();
        if (count($totais) < 1) {
            return;
        }
        foreach ($totais as $total) {
            // $item = $em->getRepository(LoteTipoCache::class)->find($total['tipo_pai_id']);
            // if (!$item) {
            $item = new LoteTipoCache();
            // }
            if (empty($total['tipo_pai_id']) || empty($total['tipo_pai'])) {
                continue;
            }
            $check[] = $total['tipo_pai_id'];
            $item->setTipoId($total['tipo_pai_id']);
            $item->setTipo($total['tipo_pai']);
            $item->setTotal($total['total']);
            $item->setSubtipo(false);
            $em->persist($item);
        }

        $totaisFilho = $this->em->getRepository(Lote::class)->totalLotesByTipoFilho(); // @TODO: Melhorar este código para reduzir linhas
        if (count($totaisFilho) < 1) {
            return;
        }
        foreach ($totaisFilho as $total) {
            $item = new LoteTipoCache();
            // }
            if (empty($total['tipo_id']) || empty($total['tipo']) || in_array($total['tipo_id'], $check)) {
                continue;
            }
            $item->setTipoId($total['tipo_id']);
            $item->setTipoPaiId($total['tipo_pai_id']);
            $item->setTipo($total['tipo']);
            $item->setTotal($total['total']);
            $item->setSubtipo(true);
            $em->persist($item);
        }

        $this->em->getRepository(Lote::class)->montaCacheRelacoes();
        if ($autoFlush) $em->flush();
    }

    public function cadastro($nome, $email, $celular, $senha)
    {
        try {
            $response = $this->getClient()->request('POST', '/api/public/arrematantes/cadastro', [
                RequestOptions::JSON => [
                    'precadastro' => true,
                    'password' => $senha,
                    'pessoa' => [
                        'name' => $nome,
                        'emails' => [
                            [
                                'email' => $email
                            ]
                        ],
                        'phoneNumbers' => [
                            [
                                'areaCode' => '+55',
                                'phoneNumber' => preg_replace('/\D/', '$1', $celular)
                            ]
                        ],
                        'type' => 1,
                        'ipRegistration' => Utils::get_client_ip_env(),
                        'deviceRegistration' => Utils::detectPlatform(),
                        'browserRegistration' => Utils::getBrowser()['name'],
                    ]
                ]
            ]);
        } catch (ClientException $e) {
            $body = json_decode($e->getResponse()->getBody(), true);
            if (isset($body['detail'])) {
                throw new \Exception(is_array($body['detail']) ? serialize($body['detail']) : $body['detail']);
            }
            if (isset($body['error'])) {
                throw new \Exception((is_array($body['message']) ? (string)join($body['message'], ', ') : $body['message']));
            }
            try {
                throw new \Exception((string)$body);
            } catch (\Throwable $exception) {
                throw new \Exception((string)$e->getResponse()->getBody());
            }
        } catch (\Throwable $exception) {
            throw $exception;
        }
        return json_decode($response->getBody(), true);
    }

    public function recuperarSenha($username)
    {
        try {
            $response = $this->getClient()->request('POST', 'api/public/arrematantes/service/recupera-senha', [
                RequestOptions::JSON => [
                    'login' => $username,
                    'ipRegistration' => Utils::get_client_ip_env(),
                    'deviceRegistration' => Utils::detectPlatform(),
                    'browserRegistration' => Utils::getBrowser()['name'],
                ]
            ]);
        } catch (ClientException $e) {
            $body = json_decode($e->getResponse()->getBody(), true);
            if (isset($body['detail'])) {
                throw new \Exception(is_array($body['detail']) ? serialize($body['detail']) : $body['detail']);
            }
            if (isset($body['error'])) {
                throw new \Exception((is_array($body['message']) ? (string)join($body['message'], ', ') : $body['message']));
            }
            try {
                throw new \Exception((string)$body);
            } catch (\Throwable $exception) {
                throw new \Exception((string)$e->getResponse()->getBody());
            }
        } catch (\Throwable $exception) {
            throw $exception;
        }
        return json_decode($response->getBody(), true);
    }

    public function recuperarSenhaSalvar($id, $token, $password)
    {
        $data = [RequestOptions::JSON => [
            'id' => $id,
            'token' => $token,
            'password' => $password
        ]];
        return $this->callApi('PUT', '/api/public/arrematantes/service/recupera-senha', $data);
    }

    public function getCategorias()
    {
        return $this->callApi('GET', '/api/public/service/tiposBem');
    }


}