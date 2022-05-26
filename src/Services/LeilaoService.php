<?php


namespace SL\WebsiteBundle\Services;


use Doctrine\ORM\EntityManagerInterface;
use SL\WebsiteBundle\Entity\Lote;

class LeilaoService
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param null $leilao
     * @param int $limit
     * @param int $offset
     * @param array $filtros
     *      $filtros = [
     *          'busca' => (string) Busca inteligente por lotes
     *          'precoMinimo' => (decimal) Valor mínimo
     *          'precoMaximo' => (decimal) Valor máximo
     *          'tipo' => (array|int) Tipo do Bem
     *          'tipoLeilao' => (array|int) 1 = Judicial; 2 = Extrajudicial;
     *          'relevancia' => (int) 0 = Relevância baseado no número e acessos e lances; 1 = Pela data do leilão (Crescente); 2 = Valor (Crescente); 3 = Valor (Decrescente)
     *          'qtdLeiloes' => (int) 0 = Leilão único; 1 - Primeiro leilão; 2 = Segundo leilão; 3 = Terceiro leilão (falência)
     *          'uf' => (array|mixed) UF do lote;
     *          'cidade' => (array|mixed) Cidade do lote;
     *          'bairro' => (array|mixed) Bairros do lote;
     *          'ocupado' => (bool/null) Se está ou não desocupado. Null para ambos.
     *          'classificacaoLeilao' => (array/int) Classificação do leilão. Iniciativa privada, Seguradoras etc.
     *          'latLng' => (array[0 => lat, 1 = lng, 2 = proximidade(10km por padrão)) Latitude e Longitude para disponibilizar imóveis próximos
     *      ]
     * @return array|Lote
     */
    public function buscarBens($leilao = null, $limit = 100, $offset = 0, $filtros = [])
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('l')->from(Lote::class, 'l');
        $qb->setMaxResults($limit)->setFirstResult($offset);

        // Filters

        return $qb->getQuery()->getResult();
    }

}