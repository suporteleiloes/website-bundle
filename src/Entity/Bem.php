<?php

namespace SL\WebsiteBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use SL\WebsiteBundle\Repository\BemRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(indexes={
 *     @ORM\Index(name="tipo_id", columns={"tipo_id"}),
 *     @ORM\Index(name="tipo", columns={"tipo"}),
 *     @ORM\Index(name="tipo_slug", columns={"tipo_slug"}),
 *     @ORM\Index(name="tipo_pai_id", columns={"tipo_pai_id"}),
 *     @ORM\Index(name="tipo_pai", columns={"tipo_pai"}),
 *     @ORM\Index(name="tipo_pai_slug", columns={"tipo_pai_slug"}),
 *     @ORM\Index(name="cidade", columns={"cidade"}),
 *     @ORM\Index(name="uf", columns={"uf"}),
 *     @ORM\Index(name="bairro", columns={"bairro"}),
 *     @ORM\Index(name="valorMinimo", columns={"valor_minimo"}),
 *     @ORM\Index(name="comitente", columns={"comitente"}),
 *     @ORM\Index(name="comitenteId", columns={"comitente_id"}),
 *     @ORM\Index(name="visitas", columns={"visitas"}),
 *     @ORM\Index(name="venda_direta", columns={"venda_direta"}),
 *     @ORM\Index(name="processo", columns={"processo"}),
 *     @ORM\Index(name="valor_minimo", columns={"valor_minimo"}),
 *     @ORM\Index(name="siteTitulo", columns={"site_titulo"}),
 *     @ORM\Index(name="finalidade", columns={"finalidade"}),
 * })
 * @ORM\Entity(repositoryClass=BemRepository::class)
 */
class Bem extends ApiSync
{

    /**
     * @ORM\OneToOne(targetEntity="Veiculo", mappedBy="bem")
     */
    private $veiculo;

    /**
     * @ORM\OneToOne(targetEntity="Imovel", mappedBy="bem")
     */
    private $imovel;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $siteTitulo;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $siteSubtitulo;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $siteDescricao;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $siteObservacao;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $status = 0;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $statusString;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $tipoId;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $tipo;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $tipoSlug;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $tipoPaiId;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $tipoPai;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $tipoPaiSlug;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $comitenteId;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $comitente;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $comitenteLogo;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $comitenteTipoId;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $comitenteTipo;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $mostrarComitente;

    /**
     * @ORM\Column(type="string", length=50, nullable=true, options={"default": 0})
     */
    private $pais;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $cep;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $endereco;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $endNumero;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $endComplemento;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $uf;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $cidade;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $bairro;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $conservacaoId;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $conservacao;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $processo;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $executado;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $vara;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $comarca;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $exequente;

    /**
     * @ORM\Column(type="decimal", precision=15, scale=2, nullable=true)
     */
    private $valorDebitos;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $localizacao;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $localizacaoUrlGoogleMaps;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $localizacaoUrlStreetView;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $localizacaoMapEmbed;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $videos;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $campos;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $tags;

    /**
     * @ORM\Column(type="integer", options={"default": 0})
     */
    private $visitas = 0;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $tour360;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $destaque = false;

    /**
     * @ORM\Column(type="decimal", precision=15, scale=2, nullable=true)
     */
    private $valorIncremento = 200.00;

    /**
     * @ORM\Column(type="decimal", precision=15, scale=2, nullable=true)
     */
    private $valorMercado = 0.00;

    /**
     * @ORM\Column(type="decimal", precision=15, scale=2, nullable=true)
     */
    private $valorAvaliacao = 0.00;

    /**
     * @ORM\Column(type="decimal", precision=15, scale=2, nullable=true)
     */
    private $valorMinimo = 0.00;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $vendaDireta;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dataLimitePropostas;

    /**
     * @ORM\Column(type="string", length=255, nullable=true, options={"default": "Indefinida"})
     */
    private $finalidade = 'Indefinida';

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $bemExtra;

    /**
     * @ORM\OneToMany(targetEntity="Lote", mappedBy="bem")
     */
    private $lotes;

    public function __construct()
    {
        $this->lotes = new ArrayCollection();
    }


}
