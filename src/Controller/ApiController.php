<?php

namespace SL\WebsiteBundle\Controller;

use SL\WebsiteBundle\Doctrine\DeletedFilter;
use SL\WebsiteBundle\Entity\Banner;
use SL\WebsiteBundle\Entity\Content;
use SL\WebsiteBundle\Entity\Lance;
use SL\WebsiteBundle\Entity\Leilao;
use SL\WebsiteBundle\Entity\LeilaoCache;
use SL\WebsiteBundle\Entity\Lote;
use SL\WebsiteBundle\Entity\LoteTipoCache;
use SL\WebsiteBundle\Entity\Post;
use SL\WebsiteBundle\Services\ApiService;
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
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ApiController extends AbstractController
{
    /**
     * @Route("/logout", name="api_logout", methods={"GET"})
     */
    public function logout(Request $request, TokenStorageInterface $tokenStorage)
    {
        $tokenStorage->setToken();
        $response = new RedirectResponse($this->generateUrl('home', [], UrlGeneratorInterface::ABSOLUTE_URL));
        return $response;
    }

    /**
     * @Route("/webhooks", name="api", methods={"POST"})
     * TODO: URGENTE! Implementar token
     */
    public function webhookCapture(Request $request, ApiService $apiService)
    {
        set_time_limit(60);
        $data = \json_decode($request->getContent(), true);
        if ($data === null) {
            return $this->json(['status' => 'KO'], Response::HTTP_BAD_REQUEST);
        }

        try {
            // dump($data);
            $this->validateToken($request->headers->get('Token'));
            $this->proccessHookData($data, $apiService);
            return $this->json(['status' => 'OK'], 200);
        } catch (\Throwable $e) {
            // @TODO: ENVIAR ERRO PARA API
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
    private function proccessHookData(array $hook, ApiService $apiService)
    {
        DeletedFilter::$disableDeletedFilter = true;
        if (!isset($hook['entity']) || !isset($hook['entityId']) || !isset($hook['data'])) {
            throw new \Exception('Para processar os dados do webhook é necessário passar os valores de `entity`, `entityId` e `data` com os dados.');
        }

        switch ($hook['entity']) {
            case "leilao":
                $apiService->processLeilao($hook);
                break;
            case "lote":
            case "bem":
                $apiService->processLote($hook);
                break;
            case "lance":
                $apiService->processLance($hook);
                break;
            case "content":
                $apiService->processContent($hook);
                break;
            case "banner":
                $apiService->processBanner($hook);
                break;
            case "post":
                $apiService->processPost($hook);
                break;
            default:
                throw new \Exception('Tipo de dados a ser processado não é compatível com este website');
        }
        DeletedFilter::$disableDeletedFilter = false;
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
}
