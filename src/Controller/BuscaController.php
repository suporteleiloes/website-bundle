<?php

namespace SL\WebsiteBundle\Controller;

use SL\WebsiteBundle\Entity\Lote;
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
     * @!Route("/busca", name="busca", methods={"GET"})
     *
     * @param Request $request
     * @return mixed
     */
    public function buscaAction(Request $request)
    {

        $page = $request->query->getInt('page', 1);
        $page = $page === 0 ? 1 : $page;
        $limit = 20;
        $offset = ($page * $limit) - $limit;

        $em = $this->getDoctrine()->getManager();

        $busca = $request->get('s');

        if(strlen($busca) < 3){
            return $this->render('paginas/busca-invalida.html.twig');
        }

        //Analisar performance da query. Possibilidade de criar um select específico. Verificar necessidade de cache
        $lotes = $em->getRepository(Lote::class)->findAllSimpleBasic(null, $limit, $offset, null, $busca);


        $response = $this->render('paginas/busca.html.twig', array(
            'busca' => $busca,
            'leilao' => null,
            'lotes' => $lotes['result'],
            "totalLotes" => intval($lotes['total']),
            "totalPages" => ceil(intval($lotes['total']) / $limit),
            "paginaAtual" => $page,
            'filtros' => null,
            'hasFiltros' => false,
            'disableFilters' => true
        ));

        return $response;
    }
}
