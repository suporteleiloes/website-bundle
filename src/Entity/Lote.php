<?php

namespace SL\WebsiteBundle\Entity;

use SL\WebsiteBundle\Repository\LoteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(indexes={@ORM\Index(name="aid", columns={"aid"}), @ORM\Index(name="status", columns={"status"}), @ORM\Index(name="numero", columns={"numero"}), @ORM\Index(name="tipo_id", columns={"tipo_id"}), @ORM\Index(name="tipo", columns={"tipo"}), @ORM\Index(name="tipo_pai_id", columns={"tipo_pai_id"}), @ORM\Index(name="tipo_pai", columns={"tipo_pai"})})
 * @ORM\Entity(repositoryClass=LoteRepository::class)
 */
class Lote extends ApiSync
{
    const STATUS_RASCUNHO = 0;
    const STATUS_ABERTO_PARA_LANCES = 1;
    const STATUS_EM_PREGAO = 2;
    const STATUS_HOMOLOGANDO = 5;
    const STATUS_VENDIDO = 100;
    const STATUS_CONDICIONAL = 7;
    const STATUS_SEM_LICITANTES = 8;
    const STATUS_BAIXA_OFERTA = 9;
    const STATUS_RETIRADO = 10;
    const STATUS_CANCELADO = 11;

    const LIST_STATUS_PERMITIDO_LANCE = [
        self::STATUS_ABERTO_PARA_LANCES,
        self::STATUS_EM_PREGAO,
    ];

    const LIST_STATUS_VENDA = [
        self::STATUS_CONDICIONAL,
        self::STATUS_VENDIDO,
    ];

    const LIST_STATUS_PERMITIDO_ARREMATE = [
        self::STATUS_HOMOLOGANDO,
        self::STATUS_VENDIDO,
        self::STATUS_CONDICIONAL,
        self::STATUS_BAIXA_OFERTA,
        self::STATUS_RETIRADO,
        self::STATUS_CANCELADO,
    ];

    public static $statusTitles = array(
        self::STATUS_RASCUNHO => 'Rascunho',
        self::STATUS_ABERTO_PARA_LANCES => 'Aberto para Lances',
        self::STATUS_EM_PREGAO => 'Em leilão',
        self::STATUS_HOMOLOGANDO => 'Homologando',
        self::STATUS_VENDIDO => 'Vendido',
        self::STATUS_CONDICIONAL => 'Condicional',
        self::STATUS_SEM_LICITANTES => 'Sem Licitantes',
        self::STATUS_BAIXA_OFERTA => 'Baixa Oferta',
        self::STATUS_RETIRADO => 'Retirado',
        self::STATUS_CANCELADO => 'Cancelado',
    );

    public function getStatusMessage($code = null)
    {
        if ($code === null) {
            $code = $this->status;
        }
        $message = isset(self::$statusTitles[$code])
            ? self::$statusTitles[$code]
            : 'Unknown status code :(';
        return $message;
    }

    public function permitidoLance()
    {
        return in_array($this->status, self::LIST_STATUS_PERMITIDO_LANCE);
    }

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $slug;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $numero;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $titulo;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $descricao;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    private $valorInicial;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    private $valorInicial2;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    private $valorIncremento = 200.00;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    private $valorMercado = 0.00;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    private $valorAvaliacao = 0.00;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    private $valorMinimo = 0.00;

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
     * @ORM\Column(type="smallint")
     */
    private $status;

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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $cidade;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $uf;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $tipoId;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $tipo;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $tipoPaiId;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $tipoPai;

    /**
     * @ORM\Column(type="object", nullable=true)
     */
    private $extra;

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
    private $exequente;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
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
     * @ORM\Column(type="array", nullable=true)
     */
    private $camposExtras;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $textoTaxas;

    /**
     * @ORM\OneToMany(targetEntity="SL\WebsiteBundle\Entity\Lance", mappedBy="lote", orphanRemoval=true, cascade={"persist", "remove"})
     * @ORM\OrderBy({"valor" = "DESC", "data" = "ASC"})
     */
    private $lances;

    /**
     * @ORM\ManyToOne(targetEntity="SL\WebsiteBundle\Entity\Leilao", inversedBy="lotes")
     * @ORM\JoinColumn(nullable=false)
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

    public function getValorInicial(): ?string
    {
        return $this->valorInicial;
    }

    public function setValorInicial(?string $valorInicial): self
    {
        $this->valorInicial = $valorInicial;

        return $this;
    }

    public function getValorInicial2(): ?string
    {
        return $this->valorInicial2;
    }

    public function setValorInicial2(?string $valorInicial2): self
    {
        $this->valorInicial2 = $valorInicial2;

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
    public function setLeilao(Leilao $leilao): void
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
                'resolution' => $foto['full']['resolution']['width'] . 'x' . $foto['full']['resolution']['height'],
                'versions' => $foto,
                'nome' => null
            ]);
        }
        foreach ($this->documentos as $arquivo) {
            if (strtolower($arquivo['tipo']['nome']) === 'foto site' && $arquivo['site']) {
                $fotos->add($arquivo);
            }
        }
        return $fotos;
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
        if($this->lances->count() > 0){
            return $this->lances->first()->getValor();
        }
        return $this->getLeilao()->getPraca() === 2 && bccomp($this->valorInicial2, 0, 2) === 1 ? $this->valorInicial2 : $this->valorInicial;
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

    public function isRetirado () {
        return $this->getStatus() === self::STATUS_RETIRADO || $this->getStatus() === self::STATUS_CANCELADO;
    }

    public function getDadosParaJsonSite()
    {
        return [
            "id" => $this->getId(),
            "numero" => $this->getNumero(),
            "slug" => $this->getSlug(),
            "titulo" => $this->getTitulo(),
            "descricao" => $this->getDescricao(),
            "observacao" => $this->getObservacao(),
            "destaque" => $this->isDestaque(),
            "status" => $this->getStatus(),
            "valorIncremento" => $this->getValorIncremento(),
            "valorInicial" => $this->getValorInicial(),
            "valorInicial2" => $this->getValorInicial2(),
        ];
    }

}
