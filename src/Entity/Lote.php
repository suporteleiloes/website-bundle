<?php

namespace SL\WebsiteBundle\Entity;

use SL\WebsiteBundle\Repository\LoteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(indexes={
 *     @ORM\Index(name="bem_id", columns={"bem_id"}),
 *     @ORM\Index(name="aid", columns={"aid"}),
 *     @ORM\Index(name="status", columns={"status"}),
 *     @ORM\Index(name="numero", columns={"numero"}),
 *     @ORM\Index(name="tipo_id", columns={"tipo_id"}),
 *     @ORM\Index(name="tipo", columns={"tipo"}),
 *     @ORM\Index(name="tipo_slug", columns={"tipo_slug"}),
 *     @ORM\Index(name="tipo_pai_id", columns={"tipo_pai_id"}),
 *     @ORM\Index(name="tipo_pai", columns={"tipo_pai"}),
 *     @ORM\Index(name="tipo_pai_slug", columns={"tipo_pai_slug"}),
 *     @ORM\Index(name="cidade", columns={"cidade"}),
 *     @ORM\Index(name="uf", columns={"uf"}),
 *     @ORM\Index(name="bairro", columns={"bairro"}),
 *     @ORM\Index(name="marca_id", columns={"marca_id"}),
 *     @ORM\Index(name="marca", columns={"marca"}),
 *     @ORM\Index(name="modelo_id", columns={"modelo_id"}),
 *     @ORM\Index(name="modelo", columns={"modelo"}),
 *     @ORM\Index(name="valorMinimo", columns={"valor_minimo"}),
 *     @ORM\Index(name="comitente", columns={"comitente"}),
 *     @ORM\Index(name="comitenteId", columns={"comitente_id"}),
 *     @ORM\Index(name="comitenteSlug", columns={"comitente_slug"}),
 *     @ORM\Index(name="ocupado", columns={"ocupado"}),
 *     @ORM\Index(name="visitas", columns={"visitas"}),
 *     @ORM\Index(name="venda_direta", columns={"venda_direta"}),
 *     @ORM\Index(name="processo", columns={"processo"}),
 *     @ORM\Index(name="valor_inicial", columns={"valor_inicial"}),
 *     @ORM\Index(name="titulo", columns={"titulo"}),
 *     @ORM\Index(name="finalidade", columns={"finalidade"}),
 *     @ORM\Index(name="position", columns={"position"}),
 * })
 * @ORM\Entity(repositoryClass=LoteRepository::class)
 */
class Lote extends ApiSync
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $bemId;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $slug;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $numero;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $numeroString;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $titulo;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $subtitulo;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $descricao;

    /**
     * @ORM\Column(type="decimal", precision=15, scale=2, nullable=true)
     */
    private $valorInicial;

    /**
     * @ORM\Column(type="decimal", precision=15, scale=2, nullable=true)
     */
    private $valorInicial2;

    /**
     * @ORM\Column(type="decimal", precision=15, scale=2, nullable=true)
     */
    private $valorInicial3;

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
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dataLimitePropostas;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $observacao;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $infoVisitacao;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $infoImportante;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $documentos = [];

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $foto = [];

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $ultimoLance = [];

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $acessorios = [];

    /**
     * @ORM\Column(type="smallint")
     */
    private $status;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $statusString;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $statusCor;

    /**
     * @ORM\Column(type="boolean", options={"default": 1})
     */
    private $permitirLance = true;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $comitenteId;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $comitente;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $comitenteSlug;

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
     * @ORM\Column(type="string", length=255, nullable=true, options={"default": "Indefinida"})
     */
    private $finalidade = 'Indefinida';

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $marcaId;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $marca;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $modeloId;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $modelo;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $ano;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $placa;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $chassi;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $km;

    /**
     * @ORM\Column(type="string", length=40, nullable=true)
     */
    private $combustivel;

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
     * @ORM\Column(type="json", nullable=true)
     */
    private $extra;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $bemExtra;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $destaque = false;

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
     * @ORM\Column(type="text", nullable=true)
     */
    private $formasPagamento;

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
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $permitirParcelamento;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $parcelamentoQtdParcelas;

    /**
     * @ORM\Column(type="smallint", nullable=true, options={"default": 0})
     */
    private $parcelamentoMinimoEntrada;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $parcelamentoIndices;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $permitirPropostas;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $videos;

    /**
     * @deprecated
     * @ORM\Column(type="array", nullable=true)
     */
    private $camposExtras;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $campos;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $tags;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $textoTaxas;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $taxas;

    /**
     * @ORM\Column(type="integer", options={"default": 0})
     */
    private $visitas = 0;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $ocupado;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $vendaDireta;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $publicado;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $impostos = [];

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $tour360;

    /**
     * @ORM\Column(type="smallint", nullable=true, options={"default": 0})
     */
    protected $totalLances = 0;

    /**
     * @ORM\Column(type="smallint", name="position", options={"default":99})
     */
    private $position = 99;

    /**
     * @ORM\OneToMany(targetEntity="SL\WebsiteBundle\Entity\Lance", mappedBy="lote", orphanRemoval=true, cascade={"persist", "remove"})
     * @ORM\OrderBy({"valor" = "DESC", "data" = "DESC"})
     */
    private $lances;

    /**
     * @ORM\ManyToOne(targetEntity="SL\WebsiteBundle\Entity\Leilao", inversedBy="lotes")
     * @ORM\JoinColumn(nullable=true)
     */
    private $leilao;

    public function __construct()
    {
        $this->lances = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumero(): ?int
    {
        return $this->numero;
    }

    public function setNumero($numero): self
    {
        $this->numero = $numero;

        return $this;
    }

    public function getTitulo(): ?string
    {
        return $this->titulo;
    }

    public function setTitulo(?string $titulo): self
    {
        $this->titulo = $titulo;

        return $this;
    }

    public function getDescricao(): ?string
    {
        return $this->descricao;
    }

    public function setDescricao(?string $descricao): self
    {
        $this->descricao = $descricao;

        return $this;
    }

    public function getValorInicial(): ?float
    {
        return $this->valorInicial;
    }

    public function setValorInicial(?float $valor): self
    {
        $this->valorInicial = $valor;

        return $this;
    }

    public function getValorInicial2(): ?float
    {
        return $this->valorInicial2;
    }

    public function setValorInicial2(?float $valor): self
    {
        $this->valorInicial2 = $valor;

        return $this;
    }

    public function getValorInicial3(): ?float
    {
        return $this->valorInicial3;
    }

    public function setValorInicial3(?float $valor): self
    {
        $this->valorInicial3 = $valor;

        return $this;
    }

    public function getObservacao(): ?string
    {
        return $this->observacao;
    }

    public function setObservacao(?string $observacao): self
    {
        $this->observacao = $observacao;

        return $this;
    }

    public function getInfoVisitacao(): ?string
    {
        return $this->infoVisitacao;
    }

    public function setInfoVisitacao(?string $infoVisitacao): self
    {
        $this->infoVisitacao = $infoVisitacao;

        return $this;
    }

    public function getInfoImportante(): ?string
    {
        return $this->infoImportante;
    }

    public function setInfoImportante(?string $infoImportante): self
    {
        $this->infoImportante = $infoImportante;

        return $this;
    }

    public function getDocumentos(): ?array
    {
        return $this->documentos;
    }

    public function getDocumentosPublicosSemFotos()
    {
        $docs = new ArrayCollection();
        foreach ($this->documentos as $arquivo) {
            if ((!$arquivo['tipo'] || strtolower($arquivo['tipo']['nome']) !== 'foto site') && empty($arquivo['permissao'])) {
                $docs->add($arquivo);
            }
        }
        return $docs;
    }

    public function setDocumentos(?array $documentos): self
    {
        $this->documentos = $documentos;

        return $this;
    }

    public function getFoto(): ?array
    {
        return $this->foto;
    }

    public function setFoto(?array $foto): self
    {
        $this->foto = $foto;

        return $this;
    }

    public function getUltimoLance(): ?array
    {
        return $this->ultimoLance;
    }

    public function setUltimoLance($ultimoLance): self
    {
        $this->ultimoLance = $ultimoLance;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getComitenteId()
    {
        return $this->comitenteId;
    }

    /**
     * @param mixed $comitenteId
     */
    public function setComitenteId($comitenteId): void
    {
        $this->comitenteId = $comitenteId;
    }

    public function getComitenteNome(): ?string
    {
        return $this->comitente;
    }

    public function getComitente(): ?string
    {
        return $this->comitente;
    }

    public function setComitente(?string $comitente): self
    {
        $this->comitente = $comitente;

        return $this;
    }

    public function getComitenteLogo()
    {
        return $this->comitenteLogo;
    }

    public function setComitenteLogo($comitenteLogo): self
    {
        $this->comitenteLogo = $comitenteLogo;

        return $this;
    }

    public function getComitenteTipoId(): ?int
    {
        return $this->comitenteTipoId;
    }

    public function setComitenteTipoId(?int $comitenteTipoId): self
    {
        $this->comitenteTipoId = $comitenteTipoId;

        return $this;
    }

    public function getComitenteTipo(): ?string
    {
        return $this->comitenteTipo;
    }

    public function setComitenteTipo(?string $comitenteTipo): self
    {
        $this->comitenteTipo = $comitenteTipo;

        return $this;
    }

    public function getMostrarComitente(): ?bool
    {
        return $this->mostrarComitente;
    }

    public function setMostrarComitente(?bool $mostrarComitente): self
    {
        $this->mostrarComitente = $mostrarComitente;

        return $this;
    }

    public function getMarcaId(): ?int
    {
        return $this->marcaId;
    }

    public function setMarcaId(?int $marcaId): self
    {
        $this->marcaId = $marcaId;

        return $this;
    }

    public function getMarca(): ?string
    {
        return $this->marca;
    }

    public function setMarca(?string $marca): self
    {
        $this->marca = $marca;

        return $this;
    }

    public function getModeloId(): ?int
    {
        return $this->modeloId;
    }

    public function setModeloId(?int $modeloId): self
    {
        $this->modeloId = $modeloId;

        return $this;
    }

    public function getModelo(): ?string
    {
        return $this->modelo;
    }

    public function setModelo(?string $modelo): self
    {
        $this->modelo = $modelo;

        return $this;
    }

    public function getAno()
    {
        return $this->ano;
    }

    public function setAno($ano): self
    {
        $this->ano = $ano;

        return $this;
    }

    public function getCidade(): ?string
    {
        return $this->cidade;
    }

    public function setCidade(?string $cidade): self
    {
        $this->cidade = $cidade;

        return $this;
    }

    public function getUf(): ?string
    {
        return $this->uf;
    }

    public function setUf(?string $uf): self
    {
        $this->uf = $uf;

        return $this;
    }

    public function getTipoId(): ?int
    {
        return $this->tipoId;
    }

    public function setTipoId(?int $tipoId): self
    {
        $this->tipoId = $tipoId;

        return $this;
    }

    public function getTipo(): ?string
    {
        return $this->tipo;
    }

    public function setTipo(?string $tipo): self
    {
        $this->tipo = $tipo;

        return $this;
    }

    public function getTipoPaiId(): ?int
    {
        return $this->tipoPaiId;
    }

    public function setTipoPaiId(?int $tipoId): self
    {
        $this->tipoPaiId = $tipoId;

        return $this;
    }

    public function getTipoPai(): ?string
    {
        return $this->tipoPai;
    }

    public function setTipoPai(?string $tipo): self
    {
        $this->tipoPai = $tipo;

        return $this;
    }

    public function getExtra()
    {
        return $this->extra;
    }

    public function setExtra($extra): self
    {
        $this->extra = $extra;

        return $this;
    }

    /**
     * @return Collection|Lance[]
     */
    public function getLances(): Collection
    {
        return $this->lances;
    }

    public function getLancesArray()
    {
        $array = [];
        foreach ($this->lances as $lance) {
            $array[] = $lance->__serialize();
        }
        return $array;
    }

    public function addLance(Lance $lance): self
    {
        if (!$this->lances->contains($lance)) {
            $this->lances[] = $lance;
            $lance->setLote($this);
        }

        return $this;
    }

    public function removeLance(Lance $lance): self
    {
        if ($this->lances->contains($lance)) {
            $this->lances->removeElement($lance);
            // set the owning side to null (unless already changed)
            if ($lance->getLote() === $this) {
                $lance->setLote(null);
            }
        }

        return $this;
    }

    /**
     * @return Leilao
     */
    public function getLeilao()
    {
        return $this->leilao;
    }

    /**
     * @param Leilao $leilao
     */
    public function setLeilao(?Leilao $leilao): void
    {
        $this->leilao = $leilao;
    }

    /**
     * @return mixed
     */
    public function getSlug()
    {
        return empty($this->slug) ? ('lote-' . $this->getNumero()) : $this->slug;
    }

    /**
     * @param mixed $slug
     */
    public function setSlug($slug): void
    {
        $this->slug = $slug;
    }

    public function getFotos()
    {
        $fotos = new ArrayCollection();
        if ($this->getFoto()) {
            $foto = $this->getFoto();
            $fotos->add([
                'url' => $foto['full']['url'],
                'resolution' => @$foto['full']['resolution']['width'] . 'x' . @$foto['full']['resolution']['height'],
                'versions' => $foto,
                'nome' => null,
                'order' => -1
            ]);
        }
        foreach ($this->documentos as $arquivo) {
            if ($arquivo['tipo'] && strtolower($arquivo['tipo']['nome']) === 'foto site' && $arquivo['site']) {
                $fotos->add($arquivo);
            }
        }
        if (count($fotos)) {
            $compareFunction = function ($obj1, $obj2) {
                $order1 = $obj1['order'];
                $order2 = $obj2['order'];

                // Realiza a comparação
                if ($order1 === $order2) {
                    return 0; // Igual, mantém a ordem atual
                } elseif ($order1 < $order2) {
                    return -1; // Menor, coloca $obj1 antes de $obj2
                } else {
                    return 1; // Maior, coloca $obj1 depois de $obj2
                }
            };

            $array = $fotos->getIterator()->getArrayCopy();
            usort($array, $compareFunction);
            return $array;

        }
        return $fotos;
    }

    public function getFotosSemPrincipal()
    {
        $fotos = new ArrayCollection();
        foreach ($this->documentos as $arquivo) {
            if ($arquivo['tipo'] && strtolower($arquivo['tipo']['nome']) === 'foto site' && $arquivo['site']) {
                $fotos->add($arquivo);
            }
        }
        if (count($fotos)) {
            $compareFunction = function ($obj1, $obj2) {
                $order1 = $obj1['order'];
                $order2 = $obj2['order'];

                // Realiza a comparação
                if ($order1 === $order2) {
                    return 0; // Igual, mantém a ordem atual
                } elseif ($order1 < $order2) {
                    return -1; // Menor, coloca $obj1 antes de $obj2
                } else {
                    return 1; // Maior, coloca $obj1 depois de $obj2
                }
            };

            $array = $fotos->getIterator()->getArrayCopy();
            usort($array, $compareFunction);
            return $array;

        }
        return $this->getFotos();
    }

    /**
     * @return bool
     */
    public function isDestaque(): ?bool
    {
        return $this->destaque;
    }

    /**
     * @param bool $destaque
     */
    public function setDestaque(?bool $destaque): void
    {
        $this->destaque = $destaque;
    }

    /**
     * @return float
     */
    public function getValorIncremento(): ?float
    {
        return $this->valorIncremento;
    }

    /**
     * @param float $valorIncremento
     */
    public function setValorIncremento($valorIncremento): void
    {
        $this->valorIncremento = $valorIncremento;
    }

    /**
     * @return mixed
     */
    public function getConservacaoId()
    {
        return $this->conservacaoId;
    }

    /**
     * @param mixed $conservacaoId
     */
    public function setConservacaoId($conservacaoId)
    {
        $this->conservacaoId = $conservacaoId;
    }

    /**
     * @return mixed
     */
    public function getConservacao()
    {
        return $this->conservacao;
    }

    /**
     * @param mixed $conservacao
     */
    public function setConservacao($conservacao)
    {
        $this->conservacao = $conservacao;
    }

    /**
     * @return mixed
     */
    public function getProcesso()
    {
        return $this->processo;
    }

    /**
     * @param mixed $processo
     */
    public function setProcesso($processo): void
    {
        $this->processo = $processo;
    }

    /**
     * @return mixed
     */
    public function getExecutado()
    {
        return $this->executado;
    }

    /**
     * @param mixed $executado
     */
    public function setExecutado($executado): void
    {
        $this->executado = $executado;
    }

    /**
     * @return mixed
     */
    public function getExequente()
    {
        return $this->exequente;
    }

    /**
     * @param mixed $exequente
     */
    public function setExequente($exequente): void
    {
        $this->exequente = $exequente;
    }

    /**
     * @return float
     */
    public function getValorMercado()
    {
        return $this->valorMercado;
    }

    /**
     * @param float $valorMercado
     */
    public function setValorMercado($valorMercado): void
    {
        $this->valorMercado = $valorMercado;
    }

    /**
     * @return float
     */
    public function getValorAvaliacao()
    {
        return $this->valorAvaliacao;
    }

    /**
     * @param float $valorAvaliacao
     */
    public function setValorAvaliacao($valorAvaliacao): void
    {
        $this->valorAvaliacao = $valorAvaliacao;
    }

    /**
     * @return float
     */
    public function getValorMinimo()
    {
        return $this->valorMinimo;
    }

    /**
     * @param float $valorMinimo
     */
    public function setValorMinimo($valorMinimo): void
    {
        $this->valorMinimo = $valorMinimo;
    }

    public function valorAtual()
    {
        if (!$this->getLeilao()) {
            return $this->valorInicial > 0 ? $this->valorInicial : $this->valorMinimo;
        }

        if ($this->lances->count() > 0) {
            return $this->lances->first()->getValor();
        }

        if (bccomp($this->valorInicial2, 0, 2) < 1 && bccomp($this->valorInicial3, 0, 2) < 1) {
            return $this->valorInicial;
        }

        switch ($this->getLeilao()->getPraca()) {
            case 2:
                return $this->valorInicial2;
            case 3:
                return $this->valorInicial3;
            default:
                return $this->valorInicial;

        }
    }

    public function diferencaAvaliacao()
    {
        if (!$this->valorAvaliacao) {
            return null;
        }
        $diff = bcsub($this->valorAvaliacao, $this->valorAtual());
        if (bccomp($diff, 0, 0) < 0) {
            return null;
        }
        return $diff;
    }

    public function diferencaAvaliacaoPorcentagem()
    {
        if (!$this->valorAtual() || !$this->diferencaAvaliacao()) {
            return 0;
        }
        $diferenca = $this->diferencaAvaliacao();
        $avaliacao = $this->getValorAvaliacao();
        $porcentagem = ($diferenca / $avaliacao) * 100;
        if ($porcentagem <= 0) {
            return 0;
        }
        return floor($porcentagem);
    }

    public function diferencaMercado()
    {
        if (!$this->valorMercado) {
            return null;
        }
        $diff = bcsub($this->valorMercado, $this->valorAtual());
        if (bccomp($diff, 0, 0) < 0) {
            return null;
        }
        return $diff;
    }

    public function diferencaMercadoPorcentagem()
    {
        if (!$this->valorAtual() || !$this->diferencaMercado()) {
            return 0;
        }
        $diferenca = $this->diferencaMercado();
        $avaliacao = $this->getValorAvaliacao();
        $porcentagem = ($diferenca / $avaliacao) * 100;
        if ($porcentagem <= 0) {
            return 0;
        }
        return floor($porcentagem);
    }

    /**
     * @return mixed
     */
    public function getFormasPagamento()
    {
        return $this->formasPagamento;
    }

    /**
     * @param mixed $formasPagamento
     */
    public function setFormasPagamento($formasPagamento): void
    {
        $this->formasPagamento = $formasPagamento;
    }

    /**
     * @return mixed
     */
    public function getLocalizacao()
    {
        return $this->localizacao;
    }

    /**
     * @param mixed $localizacao
     */
    public function setLocalizacao($localizacao): void
    {
        $this->localizacao = $localizacao;
    }

    /**
     * @return mixed
     */
    public function getLocalizacaoUrlGoogleMaps()
    {
        return $this->localizacaoUrlGoogleMaps;
    }

    /**
     * @param mixed $localizacaoUrlGoogleMaps
     */
    public function setLocalizacaoUrlGoogleMaps($localizacaoUrlGoogleMaps): void
    {
        $this->localizacaoUrlGoogleMaps = $localizacaoUrlGoogleMaps;
    }

    /**
     * @return mixed
     */
    public function getLocalizacaoUrlStreetView()
    {
        return $this->localizacaoUrlStreetView;
    }

    /**
     * @param mixed $localizacaoUrlStreetView
     */
    public function setLocalizacaoUrlStreetView($localizacaoUrlStreetView): void
    {
        $this->localizacaoUrlStreetView = $localizacaoUrlStreetView;
    }

    /**
     * @return mixed
     */
    public function getLocalizacaoMapEmbed()
    {
        return $this->localizacaoMapEmbed;
    }

    /**
     * @param mixed $localizacaoMapEmbed
     */
    public function setLocalizacaoMapEmbed($localizacaoMapEmbed): void
    {
        $this->localizacaoMapEmbed = $localizacaoMapEmbed;
    }

    /**
     * @return mixed
     */
    public function getPermitirParcelamento()
    {
        return $this->permitirParcelamento;
    }

    /**
     * @param mixed $permitirParcelamento
     */
    public function setPermitirParcelamento($permitirParcelamento): void
    {
        $this->permitirParcelamento = $permitirParcelamento;
    }

    /**
     * @return mixed
     */
    public function getParcelamentoQtdParcelas()
    {
        return $this->parcelamentoQtdParcelas;
    }

    /**
     * @param mixed $parcelamentoQtdParcelas
     */
    public function setParcelamentoQtdParcelas($parcelamentoQtdParcelas): void
    {
        $this->parcelamentoQtdParcelas = $parcelamentoQtdParcelas;
    }

    /**
     * @return mixed
     */
    public function getParcelamentoIndices()
    {
        return $this->parcelamentoIndices;
    }

    /**
     * @param mixed $parcelamentoIndices
     */
    public function setParcelamentoIndices($parcelamentoIndices): void
    {
        $this->parcelamentoIndices = $parcelamentoIndices;
    }

    /**
     * @return mixed
     */
    public function getPermitirPropostas()
    {
        return $this->permitirPropostas;
    }

    /**
     * @param mixed $permitirPropostas
     */
    public function setPermitirPropostas($permitirPropostas): void
    {
        $this->permitirPropostas = $permitirPropostas;
    }

    /**
     * @return mixed
     */
    public function getVideos()
    {
        return $this->videos;
    }

    /**
     * @param mixed $videos
     */
    public function setVideos($videos): void
    {
        $this->videos = $videos;
    }

    /**
     * @return mixed
     */
    public function getTextoTaxas()
    {
        return $this->textoTaxas;
    }

    /**
     * @param mixed $textoTaxas
     */
    public function setTextoTaxas($textoTaxas): void
    {
        $this->textoTaxas = $textoTaxas;
    }

    /**
     * @return mixed
     */
    public function getCamposExtras()
    {
        return $this->camposExtras;
    }

    /**
     * @param mixed $camposExtras
     */
    public function setCamposExtras($camposExtras): void
    {
        $this->camposExtras = $camposExtras;
    }

    /**
     * @return mixed
     */
    public function getNumeroString()
    {
        return $this->numeroString ?? $this->numero;
    }

    /**
     * @param mixed $numeroString
     */
    public function setNumeroString($numeroString): void
    {
        $this->numeroString = $numeroString;
    }

    /**
     * @return array
     */
    public function getAcessorios(): array
    {
        return $this->acessorios;
    }

    /**
     * @param array $acessorios
     */
    public function setAcessorios(array $acessorios): void
    {
        $this->acessorios = $acessorios;
    }

    /**
     * @return mixed
     */
    public function getStatusString()
    {
        return $this->statusString;
    }

    /**
     * @param mixed $statusString
     */
    public function setStatusString($statusString): void
    {
        $this->statusString = $statusString;
    }

    /**
     * @return mixed
     */
    public function getStatusCor()
    {
        return $this->statusCor;
    }

    /**
     * @param mixed $statusCor
     */
    public function setStatusCor($statusCor): void
    {
        $this->statusCor = $statusCor;
    }

    /**
     * @return bool
     */
    public function isPermitirLance(): bool
    {
        return $this->permitirLance;
    }

    /**
     * @param bool $permitirLance
     */
    public function setPermitirLance(bool $permitirLance): void
    {
        $this->permitirLance = $permitirLance;
    }

    /**
     * @return int
     */
    public function getVisitas(): int
    {
        return $this->visitas;
    }

    /**
     * @param int $visitas
     */
    public function setVisitas(int $visitas): void
    {
        $this->visitas = $visitas;
    }

    /**
     * @return mixed
     */
    public function isOcupado(): ?bool
    {
        return $this->ocupado;
    }

    /**
     * @param mixed $ocupado
     */
    public function setOcupado($ocupado): void
    {
        $this->ocupado = $ocupado;
    }

    /**
     * @return array
     */
    public function getImpostos(): ?array
    {
        return $this->impostos;
    }

    /**
     * @param array $impostos
     */
    public function setImpostos(array $impostos): void
    {
        $this->impostos = $impostos;
    }

    /**
     * @return mixed
     */
    public function getDataLimitePropostas()
    {
        return $this->dataLimitePropostas;
    }

    /**
     * @param mixed $dataLimitePropostas
     */
    public function setDataLimitePropostas($dataLimitePropostas): void
    {
        $this->dataLimitePropostas = $dataLimitePropostas;
    }

    /**
     * @return mixed
     */
    public function getBemId()
    {
        return $this->bemId;
    }

    /**
     * @param mixed $bemId
     */
    public function setBemId($bemId): void
    {
        $this->bemId = $bemId;
    }

    /**
     * @return mixed
     */
    public function getVendaDireta()
    {
        return $this->vendaDireta;
    }

    /**
     * @param mixed $vendaDireta
     */
    public function setVendaDireta($vendaDireta): void
    {
        $this->vendaDireta = $vendaDireta;
    }

    /**
     * @return mixed
     */
    public function getPublicado()
    {
        return $this->publicado;
    }

    /**
     * @param mixed $publicado
     */
    public function setPublicado($publicado): void
    {
        $this->publicado = $publicado;
    }

    /**
     * @return mixed
     */
    public function getTour360()
    {
        return $this->tour360;
    }

    /**
     * @param mixed $tour360
     */
    public function setTour360($tour360): void
    {
        $this->tour360 = $tour360;
    }

    /**
     * @return mixed
     */
    public function getBairro()
    {
        return $this->bairro;
    }

    /**
     * @param mixed $bairro
     */
    public function setBairro($bairro): void
    {
        $this->bairro = $bairro;
    }

    /**
     * @return mixed
     */
    public function getTipoSlug()
    {
        return $this->tipoSlug;
    }

    /**
     * @param mixed $tipoSlug
     */
    public function setTipoSlug($tipoSlug): void
    {
        $this->tipoSlug = $tipoSlug;
    }

    /**
     * @return mixed
     */
    public function getTipoPaiSlug()
    {
        return $this->tipoPaiSlug;
    }

    /**
     * @param mixed $tipoPaiSlug
     */
    public function setTipoPaiSlug($tipoPaiSlug): void
    {
        $this->tipoPaiSlug = $tipoPaiSlug;
    }

    /**
     * @return mixed
     */
    public function getSubtitulo()
    {
        return $this->subtitulo;
    }

    /**
     * @param mixed $subtitulo
     */
    public function setSubtitulo($subtitulo): void
    {
        $this->subtitulo = $subtitulo;
    }

    public function isRetirado()
    {
        return trim(mb_strtolower($this->statusString)) === 'retirado';
    }

    /**
     * @return mixed
     */
    public function getBemExtra()
    {
        return $this->bemExtra;
    }

    /**
     * @param mixed $bemExtra
     */
    public function setBemExtra($bemExtra): void
    {
        $this->bemExtra = $bemExtra;
    }

    /**
     * @return mixed
     */
    public function getParcelamentoMinimoEntrada()
    {
        return $this->parcelamentoMinimoEntrada;
    }

    /**
     * @param mixed $parcelamentoMinimoEntrada
     */
    public function setParcelamentoMinimoEntrada($parcelamentoMinimoEntrada): void
    {
        $this->parcelamentoMinimoEntrada = $parcelamentoMinimoEntrada;
    }

    /**
     * @return mixed
     */
    public function getTotalLances()
    {
        return $this->totalLances;
    }

    /**
     * @param mixed $totalLances
     */
    public function setTotalLances($totalLances): void
    {
        $this->totalLances = $totalLances ?? 0;
    }

    /**
     * @return mixed
     */
    public function getCampos()
    {
        return $this->campos;
    }

    /**
     * @param mixed $campos
     */
    public function setCampos($campos): void
    {
        $this->campos = $campos;
    }

    /**
     * @return mixed
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @param mixed $tags
     */
    public function setTags($tags): void
    {
        $this->tags = $tags;
    }

    /**
     * @return mixed
     */
    public function getTaxas()
    {
        return $this->taxas;
    }

    /**
     * @param mixed $taxas
     */
    public function setTaxas($taxas): void
    {
        $this->taxas = $taxas;
    }

    public function getValorImpostos()
    {
        $total = 0;
        if ($this->taxas && count($this->taxas)) {
            foreach ($this->taxas as $taxa) {
                $total = bcadd($total, $taxa['valor'], 2);
            }
        }
        return $total;
    }

    /**
     * @return mixed
     */
    public function getPais()
    {
        return $this->pais;
    }

    /**
     * @param mixed $pais
     */
    public function setPais($pais): void
    {
        $this->pais = $pais;
    }

    /**
     * @return mixed
     */
    public function getCep()
    {
        return $this->cep;
    }

    /**
     * @param mixed $cep
     */
    public function setCep($cep): void
    {
        $this->cep = $cep;
    }

    /**
     * @return mixed
     */
    public function getEndereco()
    {
        return $this->endereco;
    }

    /**
     * @param mixed $endereco
     */
    public function setEndereco($endereco): void
    {
        $this->endereco = $endereco;
    }

    /**
     * @return mixed
     */
    public function getEndNumero()
    {
        return $this->endNumero;
    }

    /**
     * @param mixed $endNumero
     */
    public function setEndNumero($endNumero): void
    {
        $this->endNumero = $endNumero;
    }

    /**
     * @return mixed
     */
    public function getEndComplemento()
    {
        return $this->endComplemento;
    }

    /**
     * @param mixed $endComplemento
     */
    public function setEndComplemento($endComplemento): void
    {
        $this->endComplemento = $endComplemento;
    }

    /**
     * @return mixed
     */
    public function getPlaca()
    {
        return $this->placa;
    }

    /**
     * @param mixed $placa
     */
    public function setPlaca($placa): void
    {
        $this->placa = $placa;
    }

    /**
     * @return mixed
     */
    public function getChassi()
    {
        return $this->chassi;
    }

    /**
     * @param mixed $chassi
     */
    public function setChassi($chassi): void
    {
        $this->chassi = $chassi;
    }

    /**
     * @return mixed
     */
    public function getKm()
    {
        return $this->km;
    }

    /**
     * @param mixed $km
     */
    public function setKm($km): void
    {
        $this->km = $km;
    }

    /**
     * @return mixed
     */
    public function getCombustivel()
    {
        return $this->combustivel;
    }

    /**
     * @param mixed $combustivel
     */
    public function setCombustivel($combustivel): void
    {
        $this->combustivel = $combustivel;
    }

    /**
     * @return mixed
     */
    public function getFinalidade()
    {
        return $this->finalidade;
    }

    /**
     * @param mixed $finalidade
     */
    public function setFinalidade($finalidade): void
    {
        $this->finalidade = $finalidade;
    }

    /**
     * @return mixed
     */
    public function getValorDebitos()
    {
        return $this->valorDebitos;
    }

    /**
     * @return mixed
     */
    public function getVara()
    {
        return $this->vara;
    }

    /**
     * @param mixed $vara
     */
    public function setVara($vara): void
    {
        $this->vara = $vara;
    }

    /**
     * @param mixed $valorDebitos
     */
    public function setValorDebitos($valorDebitos): void
    {
        $this->valorDebitos = $valorDebitos;
    }

    /**
     * @return mixed
     */
    public function getComarca()
    {
        return $this->comarca;
    }

    /**
     * @param mixed $comarca
     */
    public function setComarca($comarca): void
    {
        $this->comarca = $comarca;
    }

    /**
     * @return int
     */
    public function getPosition(): ?int
    {
        return $this->position;
    }

    /**
     * @param int $position
     */
    public function setPosition(?int $position): void
    {
        $this->position = $position;
    }

    /**
     * @return mixed
     */
    public function getComitenteSlug()
    {
        return $this->comitenteSlug;
    }

    /**
     * @param mixed $comitenteSlug
     */
    public function setComitenteSlug($comitenteSlug): void
    {
        $this->comitenteSlug = $comitenteSlug;
    }

    public function getDadosParaJsonSite()
    {
        return [
            "id" => $this->getId(),
            "aid" => $this->getAid(),
            "bemId" => $this->getId(),
            "numero" => $this->getNumero(),
            "slug" => $this->getSlug(),
            "titulo" => $this->getTitulo(),
            "descricao" => $this->getDescricao(),
            "observacao" => $this->getObservacao(),
            "destaque" => $this->isDestaque(),
            "status" => $this->getStatus(),
            "valorMinimo" => $this->getValorMinimo(),
            "valorAvaliacao" => $this->getValorAvaliacao(),
            "valorIncremento" => $this->getValorIncremento(),
            "valorAtual" => $this->valorAtual(),
            "valorDiferencaAvaliacao" => $this->diferencaAvaliacao(),
            "valorDiferencaAvaliacaoPorcentagem" => $this->diferencaAvaliacaoPorcentagem(),
            "valorInicial" => $this->getValorInicial(),
            "valorInicial2" => $this->getValorInicial2(),
            "valorInicial3" => $this->getValorInicial3(),
            'permitirParcelamento' => $this->permitirParcelamento,
            'parcelamentoQtdParcelas' => $this->parcelamentoQtdParcelas,
            'parcelamentoMinimoEntrada' => $this->parcelamentoMinimoEntrada,
            'parcelamentoIndices' => $this->parcelamentoIndices,
            'permitirPropostas' => $this->permitirPropostas,
            'totalLances' => $this->totalLances,
            'campos' => $this->campos,
            'tags' => $this->tags,
            'taxas' => $this->taxas,
        ];
    }

}
