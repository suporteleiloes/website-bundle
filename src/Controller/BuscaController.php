<?php

namespace SL\WebsiteBundle\Controller;

use SL\WebsiteBundle\Entity\Leilao;
use SL\WebsiteBundle\Entity\Lote;
use SL\WebsiteBundle\Services\LeilaoService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Busca controller for Web View.
 *
 */
class BuscaController extends AbstractController
{
    /**
     * @Route("/busca", name="busca", methods={"GET", "POST"})
     * @Route("/ofertas", name="busca_vendaDireta", methods={"GET", "POST"})
     *
     * @param Request $request
     * @return mixed
     */
    public function busca(Request $request, LeilaoService $leilaoService)
    {
        $routeName = $request->attributes->get('_route');
        $page = $request->query->getInt('page', 1);
        $page = $page === 0 ? 1 : $page;
        $limit = $request->query->getInt('limit', 20);
        $offset = ($page * $limit) - $limit;

        $tipoId = $request->get('tipo');

        $requestFiltros = [];

        if ($request->get('busca')) {
            $requestFiltros['busca'] = $request->get('busca');
        }

        if ($request->get('f-tipo')) {
            $requestFiltros['tipo'] = $request->get('f-tipo');
        } elseif($request->get('tipo')) {
            $requestFiltros['tipo'] = $request->get('tipo');
        }

        if ($request->get('f-comitente')) {
            $requestFiltros['comitente'] = $request->get('f-comitente');
        }

        if ($request->get('f-uf')) {
            $requestFiltros['uf'] = $request->get('f-uf');
        }

        if ($request->get('f-cidade')) {
            $requestFiltros['cidade'] = $request->get('f-cidade');
        }

        if ($routeName === 'busca_vendaDireta') {
            $requestFiltros['vendaDireta'] = 1;
        }

        if ($request->get('order')) {
            $order = $request->get('order');
            switch ($order) {
                case 'valorAsc':
                    $requestFiltros['order'] = ['l.valorInicial', 'ASC'];
                    break;
                case 'valorDesc':
                    $requestFiltros['order'] = ['l.valorInicial', 'DESC'];
                    break;
                case 'dataLeilaoAsc':
                    $requestFiltros['order'] = ['leilao.dataProximoLeilao', 'ASC'];
                    break;
                case 'dataLeilaoDesc':
                    $requestFiltros['order'] = ['leilao.dataProximoLeilao', 'DESC'];
                    break;
            }
        }

        $lotes = $leilaoService->buscarBens(
            null,
            true,
            $limit,
            $offset,
            $requestFiltros
        );


        $template = $request->attributes->get('_route') === 'print_leilao'
            ? 'busca.print.html.twig'
            : 'busca.html.twig';

        $tipos = $leilaoService->getTiposBem();
        $tiposPrincipais = [];
        if (count($tipos)) {
            $tiposPrincipais = array_filter($tipos, function ($t) {
                return !$t->isSubtipo();
            });
        }
        return $this->render($template, [
            'lotes' => @$lotes['result'],
            'filtros' => $requestFiltros,
            'lotesTipo' => $tiposPrincipais,
            'lotesTipoAll' => $tipos,
            'tipoId' => $tipoId,
            'busca' => $request->get('busca'),
            "totalLotes" => intval(@$lotes['total']),
            "totalPages" => ceil(intval(@$lotes['total']) / $limit),
            "paginaAtual" => $page,
        ]);
    }

    /**
     * @Route("/busca-tipo/{tipoId}/{tipoNome}", name="busca_tipo")
     */
    public function buscaPorTipoBem(Request $request, Leilao $leilao = null, $busca = null, $tipoId = null, $tipoNome = null)
    {

    }
}
