<?php

namespace SL\WebsiteBundle\Controller;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Query\QueryException;
use SL\WebsiteBundle\Repository\LoteCacheRepository;
use SL\WebsiteBundle\Repository\LoteRepository;
use SL\WebsiteBundle\Repository\LoteTipoCacheRepository;
use SL\WebsiteBundle\Services\LeilaoService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class FiltrosController extends AbstractController
{

    /**
     * @Route("/api/filtros/tipos", name="api_filtrosTipo", methods={"GET", "POST"})
     */
    public function filtrosTipos(Request $request, LoteCacheRepository $loteCacheRepository, LoteTipoCacheRepository $loteTipoCacheRepository)
    {
        try {
            $data = [];
            $filtros = [];
            $filtrosTipoBem = [];
            $query = 'SET sql_mode=(SELECT REPLACE(@@sql_mode, "ONLY_FULL_GROUP_BY", ""))';
            $conn = $this->getDoctrine()->getManager()->getConnection();
            $stmt = $conn->prepare($query);
            $stmt->execute();

            if(!$request->get('onlyTipoBem')) {
                /**
                 * Passar o tipo para filtrar
                 */
                $tipo = $request->get('tipo');
                if (!empty($tipo)) {
                    $filtros['lc.tipo'] = $tipo;
                }

                $parente = $request->get('parente');
                if (!empty($parente)) {
                    $filtros['lc.parente'] = $parente;
                }

                $qb = $loteCacheRepository->createQueryBuilder('lc')
                    ->select('lc')
                    ->orderBy('lc.valor', 'ASC');

                if (!empty($filtros)) {
                    $options = [];

                    foreach ($filtros as $key => $value) {
                        $options[] = Criteria::expr()->eq($key, $value);
                    }
                    $criteria = Criteria::create()->where(
                        Criteria::expr()->andX(
                            ...$options
                        )
                    );
                    $qb->addCriteria($criteria);
                }

                $data['tiposCache'] = $tiposCache = $qb->getQuery()->getResult();
            }

            if(!$request->get('onlyTipoCache')) {
                /**
                 * Passar o tipo de bem para filtrar
                 */
                $tipoBem = $request->get('tipoBem');
                $tipoBemPai = $request->get('tipoBemPai');
                if (!empty($tipoBem)) {
                    $filtrosTipoBem['ltc.tipo'] = $tipoBem;
                }
                if (!empty($tipoBemPai)) {
                    $filtrosTipoBem['ltc.tipoPai'] = $tipoBemPai;
                }

                $qb = $loteTipoCacheRepository->createQueryBuilder('ltc')
                    ->select('ltc')
                    ->orderBy('ltc.tipo', 'ASC');

                if (!empty($filtrosTipoBem)) {
                    $options = [];

                    foreach ($filtrosTipoBem as $key => $value) {
                        $options[] = Criteria::expr()->eq($key, $value);
                    }
                    $criteria = Criteria::create()->where(
                        Criteria::expr()->andX(
                            ...$options
                        )
                    );
                    $qb->addCriteria($criteria);
                }

                $data['tiposBem'] = $qb->getQuery()->getResult();
            }

            return $this->json($data);
        } catch (\Exception | QueryException $e) {
            return $this->json(['error: ' => $e->getMessage()]); // TODO: TALVEZ USAR O ERRORHANDLER?
        }
    }
}
