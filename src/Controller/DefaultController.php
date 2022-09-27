<?php

namespace SL\WebsiteBundle\Controller;

use SL\WebsiteBundle\Controller\Extra\SLAbstractController;
use SL\WebsiteBundle\Entity\Lote;
use SL\WebsiteBundle\Entity\Lance;
use SL\WebsiteBundle\Entity\Leilao;

use SL\WebsiteBundle\Entity\LoteTipoCache;
use Doctrine\Common\Collections\ArrayCollection;
use SL\WebsiteBundle\Services\LeilaoService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends SLAbstractController
{
    /**
     * @Route("/leiloes", name="leiloes")
     */
    public function leiloes(Request $request)
    {
        $filtro = $request->get('filtro');
        if (!$filtro || !in_array($filtro, ['recente', 'judiciais', 'extrajudiciais', 'encerrados', 'suspensos'])) {
            $filtro = 'recentes';
        }

        $em = $this->getDoctrine()->getManager();
        if ($filtro === 'judiciais' || $filtro === 'extrajudiciais') {
            $leiloes = $em->getRepository(Leilao::class)->carregaRecentes((new \DateTime())->modify('-1 days'), $filtro === 'judiciais');
        } elseif ($filtro === 'encerrados') {
            $leiloes = $em->getRepository(Leilao::class)->findBy(['status' => Leilao::STATUS_ENCERRADO], ['dataProximoLeilao' => 'DESC']);
        } elseif ($filtro === 'suspensos') {
            $leiloes = $em->getRepository(Leilao::class)->findBy(['status' => Leilao::STATUS_SUSPENSO], ['dataProximoLeilao' => 'DESC']);
        } else {
            $leiloes = $em->getRepository(Leilao::class)->carregaRecentes((new \DateTime())->modify('-1 days'));
        }

        return $this->render('default/leiloes.html.twig', [
            // 'bens' => [],
            'leiloes' => $leiloes,
            'filtro' => $filtro,
            'print_route' => $this->generateUrl('print_leiloes', $request->query->all())
        ]);
    }

    /**
     * @Route("/leiloes/{id}", name="leilao")
     * @Route("/print/leiloes/{id}", name="print_leilao")
     */
    public function leilao(Request $request, Leilao $leilao, LeilaoService $leilaoService)
    {
        if ($leilao) {
            if ($leilao->isEncerrado() && (!isset($_ENV['MOSTRAR_LEILAO_ENCERRADO']) || !$_ENV['MOSTRAR_LEILAO_ENCERRADO'])) {
                return $this->redirectToRoute('leilao_encerrado', ['id' => $leilao->getId()]);
            }
        }

        $page = $request->query->getInt('page', 1);
        $page = $page === 0 ? 1 : $page;
        $limit = 10;
        $offset = ($page * $limit) - $limit;

        $tipoId = $request->get('tipo');

        $filtros = [];
        $cache = $leilao->getCache();

        if ($cache) {
            $filtros = $cache->getFiltros() ?: [];
        }

        $requestFiltros = [];

        if ($request->get('busca')) {
            $requestFiltros['busca'] = $request->get('busca');
        }

        if ($request->get('f-tipo')) {
            $requestFiltros['tipo'] = $request->get('f-tipo');
        }

        if ($request->get('f-marca')) {
            $requestFiltros['marca'] = $request->get('f-marca');
        }

        if ($request->get('f-modelo')) {
            $requestFiltros['modelo'] = $request->get('f-modelo');
        }

        if ($request->get('f-ano')) {
            $requestFiltros['ano'] = $request->get('f-ano');
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

        $lotes = $leilaoService->buscarBens($leilao->getId(), false, $limit, $offset, $requestFiltros);


        $template = $request->attributes->get('_route') === 'print_leilao'
            ? 'default/print/leilao.html.twig'
            : 'default/leilao.html.twig';

        $tipos = $leilaoService->getTiposBem();
        $tiposPrincipais = [];
        if (count($tipos)) {
            $tiposPrincipais = array_filter($tipos, function ($t) {
                return !$t->isSubtipo();
            });
        }
        return $this->render($template, [
            'leilao' => $leilao,
            'lotes' => @$lotes['result'],
            'filtros' => $filtros,
            'lotesTipo' => $tiposPrincipais,
            'lotesTipoAll' => $tipos,
            'tipoId' => $tipoId,
            "totalLotes" => intval(@$lotes['total']),
            "totalPages" => ceil(intval(@$lotes['total']) / $limit),
            "paginaAtual" => $page,
        ]);
    }

    /**
     * @Route("/oferta/{tipoPai}/{tipo}/{id}/{slug}", name="lote")
     */
    public function lote(Lote $lote)
    {
        if ($lote->getLeilao() && $lote->getLeilao()->isEncerrado() && (!isset($_ENV['MOSTRAR_LEILAO_ENCERRADO']) || !$_ENV['MOSTRAR_LEILAO_ENCERRADO'])) {
            return $this->redirectToRoute('leilao_encerrado', ['id' => $lote->getLeilao()->getId()]);
        }

        $em = $this->getDoctrine()->getManager();
        /*$lances = $lote->getLances();
        $destaques = $em->getRepository(Lote::class)->findDestaques([$lote->getId()]);

        if ($lote->getNumero()) {
            // Loteado
            $proximo = $lote->getNumero() + 1;
            $anterior = $lote->getNumero() - 1;
            $next = $em->createQuery("SELECT l.id, l.slug FROM SLWebsiteBundle:Lote l WHERE l.leilao = :leilao and l.numero = :numero")
                ->setParameter('leilao', $lote->getLeilao()->getId())
                ->setParameter('numero', $proximo)
                ->setMaxResults(1)
                ->getOneOrNullResult();

            $prev = $em->createQuery("SELECT l.id, l.slug FROM SLWebsiteBundle:Lote l WHERE l.leilao = :leilao and l.numero = :numero")
                ->setParameter('leilao', $lote->getLeilao()->getId())
                ->setParameter('numero', $anterior)
                ->setMaxResults(1)
                ->getOneOrNullResult();
        } else {
            $next = $em->createQuery("SELECT l.id, l.slug FROM SLWebsiteBundle:Lote l WHERE l.leilao = :leilao and l.id > :lote ORDER BY l.id ASC")
                ->setParameter('leilao', $lote->getLeilao()->getId())
                ->setParameter('lote', $lote->getId())
                ->setMaxResults(1)
                ->getOneOrNullResult();

            $prev = $em->createQuery("SELECT l.id, l.slug FROM SLWebsiteBundle:Lote l WHERE l.leilao = :leilao and l.id < :lote ORDER BY l.id DESC")
                ->setParameter('leilao', $lote->getLeilao()->getId())
                ->setParameter('lote', $lote->getId())
                ->setMaxResults(1)
                ->getOneOrNullResult();
        }*/

        return $this->render('default/lote.html.twig', [
            'lote' => $lote,
            'leilao' => $lote->getLeilao(),
            'leilaoJson' => $lote->getLeilao() ? \json_encode($lote->getLeilao()->__serialize()) : null,
            'loteJson' => \json_encode($lote->getDadosParaJsonSite()),
            'lancesJson' => \json_encode($lote->getLancesArray()),
            'permitidoLances' => true, // $permitidoLances,
        ]);
    }

    /**
     * @Route("/leilao/{id}/findLote", name="leilao_findlote_numero")
     */
    public function loteByNumAction(Leilao $leilao, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $numero = $request->get('numero');
        $lote = $em->getRepository(Lote::class)->findOneBy(['numero' => $numero, 'leilao' => $leilao->getId()]);
        if (!$lote) {
            return $this->redirectToRoute('leilao', array('id' => $leilao->getId()));
        }
        return $this->redirectToRoute('lote', array('id' => $lote->getId(), 'slug' => $lote->getSlug()));
    }

    /**
     * @Route("/funcionamento", name="funcionamento")
     */
    public function funcionamento()
    {


        return $this->render('paginas/funcionamento.html.twig');
    }

    /**
     * @Route("/leiloeiro", name="leiloeiro")
     */
    public function leiloeiro()
    {
        $textos = $this->getTextos(['pag-leiloeiro-title', 'pag-leiloeiro-subtitulo', 'missao', 'visao', 'valores', 'pag-leiloeiro']);
        return $this->render('paginas/leiloeiro.html.twig', [
            'textos' => $textos
        ]);
    }

    /**
     * @Route("/duvidas", name="duvidas")
     */
    public function duvidas()
    {
        return $this->render('paginas/duvidas.html.twig', []);
    }

    /**
     * @Route("/indique", name="indique")
     */
    public function indique()
    {
        return $this->render('paginas/indique.html.twig', []);
    }

    /**
     * @Route("/contato", name="contato")
     */
    public function contato()
    {
        return $this->render('paginas/contato.html.twig', []);
    }

    /**
     * @Route("/contato-send", name="contato-send")
     */
    public function contatoSendAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        date_default_timezone_set('America/Fortaleza');
        $data = new \DateTime();
        $ip = $this->container->get('request_stack')->getMasterRequest()->getClientIp();

        $erros = array();

        if (0 === count($erros)) {
            $contato = new \SL\WebsiteBundle\Entity\Contato;

            $contato->setNome($request->get('nome'));
            $contato->setTipo($request->get('tipo'));
            $contato->setEmail($request->get('email'));
            $contato->setTelefone($request->get('telefone'));
            $contato->setAssunto($request->get('assunto'));
            $contato->setMensagem($request->get('mensagem'));
            $contato->setIp($ip);
            $contato->setData($data);

            $em->persist($contato);
            $em->flush();
        }

        return $this->redirectToRoute("sucesso-contato");
    }

    /**
     * @Route("/sucesso-contato", name="sucesso-contato")
     */
    public function sucessoContatoAction(Request $request)
    {
        return $this->render('paginas/sucesso-contato.html.twig');
    }

    /**
     * @Route("/documentos", name="documentos")
     */
    public function documentos()
    {
        return $this->render('paginas/documentos.html.twig', []);
    }

    /**
     * @Route("/favoritos", name="favoritos")
     */
    public function favoritos()
    {
        return $this->render('paginas/favoritos.html.twig', []);
    }

    /**
     * @Route("/blog", name="blog")
     */
    public function blog()
    {
        return $this->render('paginas/blog.html.twig', []);
    }

    /**
     * @Route("/quero-vender", name="quero-vender")
     */
    public function queroVender()
    {
        return $this->render('paginas/quero-vender.html.twig', []);
    }

    /**
     * @Route("/manual", name="manual")
     */
    public function manual()
    {

        return $this->render('paginas/manual.html.twig', [
            'textos' => null
        ]);
    }

    /**
     * @Route("/leiloes/{id}/encerrado", name="leilao_encerrado")
     */
    public function leilaoEncerrado(Leilao $leilao)
    {

        return $this->render('paginas/leilao.encerrado.html.twig', [
        ]);
    }


    /**
     * @Route("/newsletter-send", name="newsletter-send")
     */
    public function newsletterSendAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        date_default_timezone_set('America/Fortaleza');
        $data = new \DateTime();
        $ip = $this->container->get('request_stack')->getMasterRequest()->getClientIp();

        $erros = array();

        if (0 === count($erros)) {
            $newsletter = new \SL\WebsiteBundle\Entity\Newsletter;

            $newsletter->setEmail($request->get('email'));
            $newsletter->setIp($ip);
            $newsletter->setData($data);

            $em->persist($newsletter);
            $em->flush();
        }

        return $this->redirectToRoute("sucesso-newsletter");
    }

    /**
     * @Route("/sucesso-newsletter", name="sucesso-newsletter")
     */
    public function sucessoNewsletterAction(Request $request)
    {
        return $this->render('paginas/sucesso-newsletter.html.twig');
    }
}
