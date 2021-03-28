<?php

namespace SL\WebsiteBundle\Controller;

use SL\WebsiteBundle\Entity\Banner;
use SL\WebsiteBundle\Entity\Content;
use SL\WebsiteBundle\Entity\Lance;
use SL\WebsiteBundle\Entity\Leilao;
use SL\WebsiteBundle\Entity\LeilaoCache;
use SL\WebsiteBundle\Entity\Lote;
use SL\WebsiteBundle\Entity\LoteTipoCache;
use SL\WebsiteBundle\Services\DatabaseOperationsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use GuzzleHttp\Client;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ApiController extends AbstractController
{
    /**
     * @Route("/login/createSession", name="api_login_session", methods={"POST"})
     */
    public function createLoginSession(Request $request)
    {
        $data = \json_decode($request->getContent(), true);
        if ($data === null) {
            return $this->json(['status' => 'KO'], Response::HTTP_BAD_REQUEST);
        }

        try {
            // dump($data);
            $token = $data['session'];
            if (empty($token)) {
                throw new \Exception('Invalid session (t)');
            }
            $host = 'https://' . $request->server->get('HTTP_HOST');
            $client = new Client(array(
                'timeout' => 5,
                'base_uri' => $_ENV['SL_API'],
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Referer' => $host
                ],
                '',
                'verify' => false
            ));
            $session = $client->request('GET', '/api/credentials');
            if ($session->getStatusCode() !== 200) {
                throw new \Exception('Invalid token');
            }
            // ->withDomain('.suporteleiloes.com')
            // ->withSecure(true);
            $data = json_decode($session->getBody()->getContents(), true);
            $response = new JsonResponse(['status' => 'OK', 'session' => $data]);
            $cookieExpires = time() + 86400; // TODO: Definir cookie baseado na data de expiração do token
            $cookieSession = Cookie::create('sl_session')->withValue((string)$session->getBody())->withExpires($cookieExpires)->withSecure(true);
            $cookieToken = Cookie::create('sl_session-token')->withValue($token)->withExpires($cookieExpires)->withSecure(true);
            $cookiePerson = Cookie::create('sl_session-person')->withValue($data['user']['name'])->withExpires($cookieExpires)->withSecure(true);
            $cookieUsername = Cookie::create('sl_session-username')->withValue($data['user']['username'])->withExpires($cookieExpires)->withSecure(true);
            $response->headers->setCookie($cookieSession);
            $response->headers->setCookie($cookieToken);
            $response->headers->setCookie($cookiePerson);
            $response->headers->setCookie($cookieUsername);
            return $response;
        } catch (\Exception $e) {
            return $this->json(['status' => 'KO', 'error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/logout", name="api_logout", methods={"GET"})
     */
    public function logout(Request $request)
    {
        $response = new RedirectResponse($this->generateUrl('home', [], UrlGeneratorInterface::ABSOLUTE_URL));
        $response->headers->clearCookie('sl_session');
        $response->headers->clearCookie('sl_session-token');
        $response->headers->clearCookie('sl_session-person');
        $response->headers->clearCookie('sl_session-username');
        return $response;
    }

    /**
     * @Route("/webhooks", name="api", methods={"POST"})
     * TODO: URGENTE! Implementar token
     */
    public function webhookCapture(Request $request)
    {
        $data = \json_decode($request->getContent(), true);
        if ($data === null) {
            return $this->json(['status' => 'KO'], Response::HTTP_BAD_REQUEST);
        }

        try {
            // dump($data);
            $this->validateToken($request->headers->get('Token'));
            $this->proccessHookData($data);
            return $this->json(['status' => 'OK'], 200);
        } catch (\Exception $e) {
            return $this->json(['status' => 'KO', 'error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/api/delete-cache-site", name="api_delete_cache_site", methods={"DELETE"})
     * @param DatabaseOperationsService $service
     * @return JsonResponse
     * @throws \Exception
     */
    public function deleteCacheSiteAction(DatabaseOperationsService $service)
    {

        try {
            $service->clearAllTables();

            return $this->json(['status' => 'OK'], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            throw new \Exception('Erro ao excluir dados. Motivo: ' . $e->getMessage());
        }
    }

    /**
     * @param array $hook
     * @throws \Exception
     */
    private function proccessHookData(array $hook)
    {
        if (!isset($hook['entity']) || !isset($hook['entityId']) || !isset($hook['data'])) {
            throw new \Exception('Para processar os dados do webhook é necessário passar os valores de `entity`, `entityId` e `data` com os dados.');
        }

        switch ($hook['entity']) {
            case "leilao":
                $this->processLeilao($hook);
                break;
            case "lote":
                $this->processLote($hook);
                break;
            case "lance":
                $this->processLance($hook);
                break;
            case "content":
                $this->processContent($hook);
                break;
            case "banner":
                $this->processBanner($hook);
                break;
            default:
                throw new \Exception('Tipo de dados a ser processado não é compatível com este website');
        }
    }

    private function processLeilao($data)
    {
        $entityId = $data['entityId'];
        // Verifica se já existe o leilao. Se não existir, cria um.
        $em = $this->getDoctrine()->getManager();
        $leilao = $em->getRepository(Leilao::class)->findOneByAid($entityId);
        if (!$leilao) {
            $leilao = new Leilao();
        }

        $data = $data['data'];

        $leilao->setAid($entityId);
        if ($leilao->getId()) {
            $leilao->setAlastUpdate(new \DateTime());
        } else {
            $leilao->setAcreatedAt(new \DateTime());
        }

        $praca = isset($data['praca']) ? intval($data['praca']) : 1;
        $dataPraca1 = \DateTime::createFromFormat('Y-m-d H:i:s+', $data['dataAbertura']['date']);
        $dataPraca2 = isset($data['dataAberturaPraca2']) ? \DateTime::createFromFormat('Y-m-d H:i:s+', $data['dataAberturaPraca2']['date']) : null;
        $dataFimPraca1 = isset($data['dataFimPraca1']) ? \DateTime::createFromFormat('Y-m-d H:i:s+', $data['dataFimPraca1']['date']) : null;
        $dataFimPraca2 = isset($data['dataFimPraca2']) ? \DateTime::createFromFormat('Y-m-d H:i:s+', $data['dataFimPraca2']['date']) : null;

        $leilao->setSlug($data['slug']);
        $leilao->setTitulo($data['titulo']);
        $leilao->setDescricao(@$data['descricao']);
        $leilao->setTipo($data['tipo']);
        $leilao->setDataPraca1($dataPraca1);
        $leilao->setDataPraca2($dataPraca2);
        $leilao->setDataFimPraca1($dataFimPraca1);
        $leilao->setDataFimPraca2($dataFimPraca2);

        if ($praca === 2) {
            $abertura = $dataPraca2;
            $encerramento = $dataFimPraca2;
        } else {
            $abertura = $dataPraca1;
            $encerramento = $dataFimPraca1;
        }

        if ($abertura < (new \DateTime())) {
            $dataProximoLeilao = $encerramento ?: $abertura;
        } else {
            $dataProximoLeilao = $abertura;
        }
        $leilao->setDataProximoLeilao($dataProximoLeilao);

        $leilao->setJudicial($data['judicial']);
        $leilao->setTotalLotes($data['totalLotes']);
        $leilao->setStatus($data['status']);
        $leilao->setPraca($praca);
        $leilao->setInstancia(@$data['instancia']);
        $leilao->setLeiloeiro($data['leiloeiro']['nome']);
        ## $leilao->setLeiloeiroLogo($data['leiloeiro']['image']);
        $leilao->setLocal($data['patio']);
        ## $leilao->setLocalLat($data['']);
        ## $leilao->setLocalLng($data['']);
        ## $leilao->setLocalGoogleMaps($data['']);
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

        $em->persist($leilao);
        $em->flush();

        $this->geraCacheLeilao($leilao);
    }

    private function processLote($data)
    {
        $entityId = $data['entityId'];
        // Verifica se já existe o lote. Se não existir, cria um.
        $em = $this->getDoctrine()->getManager();
        $lote = $em->getRepository(Lote::class)->findOneByAid($entityId);
        if (!$lote) {
            $lote = new Lote();
        }

        if ($data['remove']) {
            $em->remove($lote);
            $em->flush();

        } else {

            $data = $data['data'];

            $lote->setAid($entityId);
            if ($lote->getId()) {
                $lote->setAlastUpdate(new \DateTime());
            } else {
                $lote->setAcreatedAt(new \DateTime());
            }

            $lote->setSlug($data['slug']);
            $lote->setNumero($data['numero']);
            $lote->setTitulo($data['bem']['siteTitulo']);
            $lote->setDescricao($data['bem']['siteDescricao']);
            $lote->setObservacao($data['bem']['siteObservacao']);
            $lote->setValorInicial($data['valorInicial']);
            $lote->setValorInicial2($data['valorInicial2']);
            $lote->setValorIncremento($data['valorIncremento']);
            $lote->setValorMercado($data['valorMercado']);
            $lote->setValorMinimo($data['valorMinimo']);
            $lote->setValorAvaliacao($data['valorAvaliacao']);
            ## $lote->setInfoVisitacao($data['infoVisitacao']);
            ## $lote->setInfoImportante($data['infoImportante']);
            $lote->setDocumentos($data['bem']['arquivos']);
            $lote->setFoto($data['bem']['image']);
            ##! $lote->setUltimoLance(@$data['ultimoLance']);
            $lote->setStatus($data['status']);
            $lote->setComitenteId($data['bem']['comitente']['id']);
            $lote->setComitente($data['bem']['comitente']['pessoa']['name']);
            $lote->setComitenteLogo($data['bem']['comitente']['image']);
            $lote->setComitenteTipoId(@$data['bem']['comitente']['tipo']['id']);
            $lote->setComitenteTipo(@$data['bem']['comitente']['tipo']['nome']);
            ##! $lote->setMostrarComitente($data['bem']['comitente']['mostrarSite']);
            $lote->setMarcaId(@$data['bem']['marca']['id']);
            $lote->setMarca(@$data['bem']['marca']['nome']);
            $lote->setModeloId(@$data['bem']['modelo']['id']);
            $lote->setModelo(@$data['bem']['modelo']['nome']);
            $lote->setAno(@$data['bem']['anoModelo']);
            $lote->setCidade(@$data['bem']['cidade']);
            $lote->setUf(@$data['bem']['uf']);
            $lote->setTipoId(@$data['bem']['tipo']['id']);
            $lote->setTipo(@$data['bem']['tipo']['nome']);
            $lote->setTipoPaiId(@$data['bem']['tipoPaiId']);
            $lote->setTipoPai(@$data['bem']['tipoPai']);
            $lote->setExtra(@$data['bem']['extra']);
            $lote->setDestaque(@$data['bem']['destaque']); // TODO: Bem ou lote ?
            $lote->setConservacaoId(@$data['bem']['conservacao']['id']);
            $lote->setConservacao(@$data['bem']['conservacao']['nome']);
            $lote->setProcesso(@$data['bem']['processoNumero']);
            $lote->setExequente(@$data['bem']['processoExequente']);
            $lote->setExecutado(@$data['bem']['processoExecutado']);

            $lote->setLocalizacaoUrlGoogleMaps(@$data['bem']['localizacaoUrlGoogleMaps']);
            $lote->setLocalizacaoUrlStreetView(@$data['bem']['localizacaoUrlStreetView']);
            $lote->setLocalizacaoMapEmbed(@$data['bem']['localizacaoMapEmbed']);
            $lote->setPermitirParcelamento(@$data['permitirParcelamento']);
            $lote->setParcelamentoQtdParcelas(@$data['parcelamentoQtdParcelas']);
            $lote->setParcelamentoIndices(@$data['parcelamentoIndices']);
            $lote->setPermitirPropostas(@$data['permitirPropostas']);
            $lote->setVideos(@$data['bem']['videos']);
            $lote->setCamposExtras(@$data['bem']['camposExtras']);
            /* @var Leilao $leilao */
            $leilao = $em->getRepository(Leilao::class)->findOneByAid($data['leilao']['id']);
            $lote->setLeilao($leilao);
            $em->persist($lote);
            $em->flush();
        }

        $this->geraCacheLeilao($leilao);
        $this->geraCacheLotes();

        // Atualiza total lotes
        $leilao->setTotalLotes($leilao->getLotes()->count()); // TODO: Use count query instead this
    }

    private function processLance($data)
    {
        $entityId = $data['entityId'];
        // Verifica se já existe o lance. Se não existir, cria um.
        $em = $this->getDoctrine()->getManager();
        $lance = $em->getRepository(Lance::class)->findOneByAid($entityId);
        if (!$lance) {
            $lance = new Lance();
        }

        $data = $data['data'];

        $lance->setAid($entityId);
        if ($lance->getId()) {
            $lance->setAlastUpdate(new \DateTime());
        } else {
            $lance->setAcreatedAt(new \DateTime());
        }

        $lance->setData(\DateTime::createFromFormat('Y-m-d H:i:s+', $data['data']['date']));
        $lance->setApelido(isset($data['autor']) ? $data['autor']['apelido'] : $data['arrematante']['apelido']);
        $lance->setNome($data['arrematante']['pessoa']['name']);
        $lance->setCidade(isset($data['autor']) ? $data['autor']['cidade'] : null);
        $lance->setUf(isset($data['autor']) ? $data['autor']['uf'] : null);
        $lance->setValor($data['valor']);
        /* @var Lote $lote */
        $lote = $em->getRepository(Lote::class)->findOneByAid($data['lote']['id']);
        if ($lote) {
            $lance->setLote($lote);
            $lote->addLance($lance);
        }
        $em->persist($lance);
        $em->flush();
    }

    public function geraCacheLeilao(Leilao $leilao)
    {
        $filtros = $this->getDoctrine()->getRepository(Leilao::class)->filtros($leilao->getId());
        $cache = $leilao->getCache() ?: new LeilaoCache();
        $cache->setFiltros($filtros);
        $cache->setLeilao($leilao);
        $leilao->setCache($cache);
        $em = $this->getDoctrine()->getManager();
        $em->persist($cache);
        $em->persist($leilao);
        $em->flush();
    }

    private function processContent($data)
    {
        $entityId = $data['entityId'];
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository(Content::class)->findOneByAid($entityId);
        if (!$entity) {
            $entity = new Content();
        }

        $data = $data['data'];

        $entity->setAid($entityId);
        if ($entity->getId()) {
            $entity->setAlastUpdate(new \DateTime());
        } else {
            $entity->setAcreatedAt(new \DateTime());
        }

        $entity->setTitle(@$data['title']);
        $entity->setPageName(@$data['pageName']);
        $entity->setPageDescription(@$data['pageDescription']);
        $entity->setTemplate(@$data['template']);

        $em->persist($entity);
        $em->flush();
    }

    private function processBanner($data)
    {
        $entityId = $data['entityId'];
        $em = $this->getDoctrine()->getManager();
        $banner = $em->getRepository(Banner::class)->findOneByAid($entityId);
        if (!$banner) {
            $banner = new Banner();
        }

        $data = $data['data'];

        $banner->setAid($entityId);
        if ($banner->getId()) {
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

        $em->persist($banner);
        $em->flush();
    }

    public function validateToken($token)
    {
        if (!$this->checkSiteToken($token)) {
            throw new \Exception('Invalid token');
        }
    }

    public function checkSiteToken($token)
    {
        return strcmp($token, $_ENV['APP_SECRET']) === 0;
    }

    public function geraCacheLotes()
    {
        $em = $this->getDoctrine()->getManager();
        $em->getRepository(LoteTipoCache::class)->flushData();
        $em->flush();
        $totais = $this->getDoctrine()->getRepository(Lote::class)->totalLotesByTipo();
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
            $item->setTipoId($total['tipo_pai_id']);
            $item->setTipo($total['tipo_pai']);
            $item->setTotal($total['total']);
            $em->persist($item);
        }
        $em->flush();
    }
}
