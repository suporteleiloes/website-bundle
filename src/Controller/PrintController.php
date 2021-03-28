<?php

namespace SL\WebsiteBundle\Controller;

use SL\WebsiteBundle\Controller\Extra\SLAbstractController;
use SL\WebsiteBundle\Entity\Lote;
use SL\WebsiteBundle\Entity\Lance;
use SL\WebsiteBundle\Entity\Leilao;

use SL\WebsiteBundle\Entity\LoteTipoCache;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PrintController extends SLAbstractController
{
    /**
     * @Route("/print/leiloes", name="print_leiloes")
     */
    public function leiloes(Request $request)
    {
        $filtro = $request->get('filtro');
        if (!$filtro || !in_array($filtro, ['recente', 'judiciais', 'extrajudiciais', 'encerrados', 'suspensos'])) {
            $filtro = 'recentes';
        }

        $em = $this->getDoctrine()->getManager();
        if ($filtro === 'judiciais' || $filtro === 'extrajudiciais') {
            $leiloes = $em->getRepository(Leilao::class)->carregaRecentes((new \DateTime())->modify('-3 days'), $filtro === 'judiciais');
        } elseif ($filtro === 'encerrados') {
            $leiloes = $em->getRepository(Leilao::class)->findBy(['status' => Leilao::STATUS_ENCERRADO], ['dataProximoLeilao' => 'DESC']);
        } elseif ($filtro === 'suspensos') {
            $leiloes = $em->getRepository(Leilao::class)->findBy(['status' => Leilao::STATUS_SUSPENSO], ['dataProximoLeilao' => 'DESC']);
        } else {
            $leiloes = $em->getRepository(Leilao::class)->carregaRecentes((new \DateTime())->modify('-3 days'));
        }

        return $this->render('default/print/leiloes.html.twig', [
            // 'bens' => [],
            'leiloes' => $leiloes,
            'filtro' => $filtro
        ]);
    }

}
