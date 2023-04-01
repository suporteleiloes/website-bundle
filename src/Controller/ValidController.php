<?php

namespace SL\WebsiteBundle\Controller;

use SL\WebsiteBundle\Services\ApiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ValidController extends AbstractController
{
    /**
     * @Route("/valide", name="valida_nota", methods={"GET", "POST"})
     * @Route("/valide/{numero}", name="valida_notaByNumero", methods={"GET", "POST"})
     */
    public function validaNotaArrematacao(Request $request, ApiService $apiService, $numero = null)
    {
        $nota = null;
        $erro = null;
        if ($request->get('numero') || $numero) {
            try {
                $nota = $apiService->consultaNotaArrematacao($request->get('numero') ?: $numero);
            } catch (\Throwable $exception) {
                $erro = $exception->getMessage();
            }
        }

        return $this->render('@SLWebsite/valid/validaNota.html.twig', [
            'nota' => $nota,
            'erro' => $erro
        ]);
    }

}
