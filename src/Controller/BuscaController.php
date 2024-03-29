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
     * @Route("/imoveis", name="busca_imoveis", methods={"GET", "POST"})
     * @Route("/veiculos", name="busca_veiculos", methods={"GET", "POST"})
     *
     * @param Request $request
     * @return mixed
     */
    public function busca(Request $request, LeilaoService $leilaoService)
    {
        $routeName = $request->attributes->get('_route');
        $page = intval($request->get('page', 1));
        if (!is_numeric($page) || $page < 1) {
            $page = 1;
        }
        $limit = $request->get('limit', 20);
        if (!is_numeric($limit) || $limit > 100 || $limit < 1) {
            $limit = 100;
        }
        $offset = ($page * $limit) - $limit;

        $tipoId = $request->get('tipoId');

        $requestFiltros = [];

        if ($routeName === 'busca_imoveis') {
            $requestFiltros['tipo'] = 'Imóveis';
        }

        if ($routeName === 'busca_veiculos') {
            $requestFiltros['tipo'] = 'Veículos';
        }

        if ($request->get('busca')) {
            $requestFiltros['busca'] = $request->get('busca');
        }

        if ($request->get('f-tipo')) {
            $requestFiltros['tipo'] = $request->get('f-tipo');
        } elseif ($request->get('tipo')) {
            $requestFiltros['tipo'] = $request->get('tipo');
        }

        if ($request->get('tipoPrincipal')) {
            $requestFiltros['tipoPrincipal'] = $request->get('tipoPrincipal');
        }

        if ($request->get('tipo-not')) {
            $requestFiltros['tipo-not'] = $request->get('tipo-not');
        }

        if ($request->get('ocupacao') !== null) {
            $requestFiltros['ocupacao'] = $request->get('ocupacao');
        }

        if ($request->get('judicial') !== null) {
            $requestFiltros['judicial'] = $request->get('judicial');
        }

        if ($request->get('vendaDireta') !== null) {
            $requestFiltros['vendaDireta'] = $request->get('vendaDireta');
        }

        if (!empty($tipoId)) {
            $requestFiltros['tipoId'] = $tipoId;
        }

        /**
         * @deprecated
         */
        if ($request->get('f-comitente')) {
            $requestFiltros['comitente'] = $request->get('f-comitente');
        }

        if ($request->get('comitente')) {
            $requestFiltros['comitente'] = $request->get('comitente');
        }

        if ($request->get('finalidade')) {
            $requestFiltros['finalidade'] = $request->get('finalidade');
        }

        if ($request->get('f-uf')) {
            $requestFiltros['uf'] = $request->get('f-uf');
        }

        /**
         * @deprecated
         */
        if ($request->get('f-cidade')) {
            $requestFiltros['cidade'] = $request->get('f-cidade');
        }
        if ($request->get('cidade')) {
            $requestFiltros['cidade'] = $request->get('cidade');
        }

        if ($request->get('precoMinimo')) {
            $requestFiltros['precoMinimo'] = $request->get('precoMinimo');
        }

        if ($request->get('precoMaximo')) {
            $requestFiltros['precoMaximo'] = $request->get('precoMaximo');
        }

        if ($request->get('intervaloPreco')) {
            $intervaloPreco = explode(';', $request->get('intervaloPreco'));
            if (count($intervaloPreco) === 2) {
                $requestFiltros['precoMinimo'] = $intervaloPreco[0];
                $requestFiltros['precoMaximo'] = $intervaloPreco[1];
            }
        }

        if ($request->get('marca')) {
            $requestFiltros['marca'] = $request->get('marca');
        }
        if ($request->get('modelo')) {
            $requestFiltros['modelo'] = $request->get('modelo');
        }

        if ($routeName === 'busca_vendaDireta') {
            $requestFiltros['vendaDireta'] = 1;
        }

        if ($request->get('order')) {
            $order = $request->get('order');
            $desc = $request->get('desc');
            switch ($order) {
                case 'valor':
                    $requestFiltros['order'] = ['l.valorInicial', $desc && ($desc == '1' || $desc === 'true') ? 'DESC' : 'ASC'];
                    break;
                case 'valorAsc':
                    $requestFiltros['order'] = ['l.valorInicial', 'ASC'];
                    break;
                case 'valorDesc':
                    $requestFiltros['order'] = ['l.valorInicial', 'DESC'];
                    break;
                case 'valorAvaliacao':
                    $requestFiltros['order'] = ['l.valorAvaliacao', $desc && ($desc == '1' || $desc === 'true') ? 'DESC' : 'ASC'];
                    break;
                case 'valorAvaliacaoAsc':
                    $requestFiltros['order'] = ['l.valorAvaliacao', 'ASC'];
                    break;
                case 'valorAvaliacaoDesc':
                    $requestFiltros['order'] = ['l.valorAvaliacao', 'DESC'];
                    break;
                case 'dataLeilao':
                    $requestFiltros['order'] = ['leilao.dataProximoLeilao', $desc && ($desc == '1' || $desc === 'true') ? 'DESC' : 'ASC'];
                    break;
                case 'dataLeilaoAsc':
                    $requestFiltros['order'] = ['leilao.dataProximoLeilao', 'ASC'];
                    break;
                case 'dataLeilaoDesc':
                    $requestFiltros['order'] = ['leilao.dataProximoLeilao', 'DESC'];
                    break;
            }
        } else {
            $requestFiltros['order'] = ['leilao.dataProximoLeilao', 'ASC'];
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
