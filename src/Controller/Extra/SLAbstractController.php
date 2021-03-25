<?php


namespace App\Controller\Extra;


use App\Entity\Content;
use App\Entity\Leilao;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SLAbstractController extends AbstractController
{

    public function getLeiloesHome()
    {
        $em = $this->getDoctrine()->getManager();
        $leiloes = $em->getRepository(Leilao::class)->carregaRecentes((new \DateTime())/*->modify('-1 day')*/);

        return $leiloes;
    }

    public function getTextos(array $textos)
    {
        // TODO: Cache, Performance
        $em = $this->getDoctrine()->getManager();
        $response = [];
        foreach ($textos as $texto) {
            /* @var \App\Entity\Content $textoEntity */
            $textoEntity = $em->getRepository(Content::class)->findOneByPageName($texto);
            if (!$textoEntity) {
                $response[$texto] = ['title' => '', 'pageName' => $texto, 'pageDescription' => '', 'template' => ''];
                continue;
            }
            $response[$texto] = ['title' => $textoEntity->getTitle(), 'pageName' => $texto, 'pageDescription' => $textoEntity->getPageDescription(), 'template' => $textoEntity->getTemplate()];
        }
        return $response;
    }

}