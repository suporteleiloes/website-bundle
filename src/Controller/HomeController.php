<?php

namespace App\Controller;

use App\Controller\Extra\SLAbstractController;
use App\Entity\Banner;
use App\Entity\Lote;
use App\Entity\Leilao;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends SLAbstractController
{
    /**
     * TODO: Cache
     * @Route("/", name="home")
     */
    public function index()
    {
        // return $this->redirect('https://leilao.suedpeterleiloes.com.br');
        $em = $this->getDoctrine();

        $leiloes = $this->getLeiloesHome();
        $leiloesEncerrados = $em->getRepository(Leilao::class)->findBy(['status' => 99], ['id' => 'DESC'], 8);

        /*$leiloesAtuais = new ArrayCollection();
        $leiloesEncerrados = new ArrayCollection();
        /* @var \App\Entity\Leilao $leilao *1/
        foreach ($leiloes as $leilao) {
            if ($leilao->getDataPraca1() > (new \DateTime())->modify('-2 days')) {
                $leiloesAtuais->add($leilao);
            } else {
                $leiloesEncerrados->add($leilao);
            }
        }*/

        $destaques = $em->getRepository(Lote::class)->findDestaques();

        $textos = $this->getTextos(['pag-leiloeiro-title', 'pag-leiloeiro-subtitulo', 'missao', 'visao', 'valores', 'aviso-home']);

        $banners = $em->getRepository(Banner::class)->findAll(); // TODO

        return $this->render('home.html.twig', [
            'lotes' => $destaques,
            'leiloesAtuais' => $leiloes,
            'textos' => $textos,
            'banners' => $banners,
            'leiloesEncerrados' => $leiloesEncerrados,
        ]);
    }
}
