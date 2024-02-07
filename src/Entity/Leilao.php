<?php

namespace SL\WebsiteBundle\Entity;

use SL\WebsiteBundle\Repository\LeilaoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(indexes={
 *     @ORM\Index(name="status", columns={"status"}),
 *     @ORM\Index(name="status_tipo", columns={"status_tipo"}),
 *     @ORM\Index(name="data_proximo_leilao", columns={"data_proximo_leilao"}),
 *     @ORM\Index(name="tipo", columns={"tipo"}),
 *     @ORM\Index(name="judicial", columns={"judicial"})
 * })
 * @ORM\Entity(repositoryClass=LeilaoRepository::class)
 */
class Leilao extends ApiSync
{

    const LEILAO_ONLINE = 1;
    const LEILAO_PRESENCIAL = 2;
    const LEILAO_ONLINE_PRESENCIAL = 3;

    const STATUS_RASCUNHO = 0;
    const STATUS_EM_BREVE = 1;
    const STATUS_EM_LOTEAMENTO = 2;
    const STATUS_ABERTO_PARA_LANCES = 3;
    const STATUS_EM_LEILAO = 4;
    const STATUS_CANCELADO = 96;
    const STATUS_ADIADO = 97;
    const STATUS_SUSPENSO = 98;
    const STATUS_ENCERRADO = 99;

    const STATUS_TIPO_ABERTO = 1;
    const STATUS_TIPO_EM_LEILAO = 2;
    const STATUS_TIPO_ENCERRADO = 100;


    public function getTipoString()
    {
        $status = $this->getTipo();
        if ($status === self::LEILAO_ONLINE) {
            return 'Online';
        } elseif ($status === self::LEILAO_PRESENCIAL) {
            return 'Presencial';
        } elseif ($status === self::LEILAO_ONLINE_PRESENCIAL) {
            return 'Online e Presencial';
        }
        return '?';
    }

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $codigo;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $numero;

    /**
     * @ORM\Column(type="integer", length=255, nullable=true)
     */
    private $ano;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $slug;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $data1;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $data2;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $data3;

    /**
     * Data que o leilão estará aberto para lances
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dataAbertura1;

    /**
     * Data que o segundo leilão estará aberto para lances
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dataAbertura2;

    /**
     * Data que o terceiro leilão estará aberto para lances
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dataAbertura3;

    /**
     * Em qual leilão está. Isso serve em caso de leilões de mais de uma data.
     * @ORM\Column(type="smallint", options={"default": 1})
     */
    private $praca = 1;

    /**
     * Quantidade de datas para este leilão. Pode acontecer de ser 3 instâncias (3 leilões), mas
     * somente a primeira e segunda data já está marcada, então não confie 100% no data1, data2 e data3
     * como referência para quantidade de leilões
     * @ORM\Column(type="smallint", options={"default": 1})
     */
    private $instancia = 1;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $titulo;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $descricao;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $tipo;

    /**
     * // Atualizado automaticamente para ordenação das datas
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dataProximoLeilao;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $judicial;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $vendaDireta;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dataLimitePropostas;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $totalLotes;

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
     * @ORM\Column(type="smallint", options={"default": 1})
     */
    private $statusTipo = self::STATUS_TIPO_ABERTO;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $leiloeiro;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $leiloeiroLogo;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $leiloeiroUrl;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $leiloeiroMatricula;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $leiloeiroUf;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $local;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $localLat;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $localLng;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $localGoogleMaps;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $infoVisitacao;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $infoRetirada;

    /**
     * @ORM\Column(type="string", length=500, nullable=true)
     */
    private $infoPagamento;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $observacoes;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $arquivos = [];

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $comitentes = [];

    /**
     * @ORM\Column(type="object", nullable=true)
     */
    private $extra;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $destaque = false;

    /**
     * @ORM\Column(type="object", nullable=true)
     */
    private $image;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $cep;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $endereco;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $enderecoNumero;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $bairro;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $cidade;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $uf;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $enderecoReferencia;


    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $editalHtml;

    /**
     * @ORM\Column(type="string", length=50, nullable=true, options={"default": "America/Sao_Paulo"})
     */
    private $timezone = 'America/Sao_Paulo';

    /**
     * 0 = Desativar, 1 = Permitir e obrigatório, 2 = Permitir e não obrigatório
     * @ORM\Column(type="smallint", nullable=true, options={"default": 1})
     */
    private $habilitacao = 1;
    /**
     * @ORM\Column(type="integer", nullable=true, options={"default": 0})
     */
    private $habilitados = 0;

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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $video;

    /**
     * Se for nulo, carregar template padrão de regras
     * @ORM\Column(type="text", nullable=true)
     */
    private $regras;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $textoPropostas;

    /**
     * @ORM\Column(type="json", length=255, nullable=true)
     */
    private $sistemaTaxa;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $classificacaoId;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $classificacao;

    /**
     * @ORM\Column(type="integer", nullable=true, options={"default": 0})
     */
    private $visitas;

    /**
     * @ORM\Column(type="integer", nullable=true, options={"default": 0})
     */
    private $lances;

    /**
     * @ORM\OneToMany(targetEntity="SL\WebsiteBundle\Entity\Lote", mappedBy="leilao", cascade={"persist"})
     * @ORM\OrderBy({"numero" = "ASC", "id" = "ASC"})
     */
    private $lotes;

    /**
     * @ORM\OneToOne(targetEntity="SL\WebsiteBundle\Entity\LeilaoCache")
     */
    private $cache;

    /**
     * Leilao constructor.
     */
    public function __construct()
    {
        $this->lotes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * @return mixed
     */
    public function getDescricao()
    {
        return $this->descricao;
    }

    /**
     * @param mixed $descricao
     */
    public function setDescricao($descricao): void
    {
        $this->descricao = $descricao;
    }

    public function getTipo(): ?int
    {
        return $this->tipo;
    }

    public function setTipo(?int $tipo): self
    {
        $this->tipo = $tipo;

        return $this;
    }

    public function getJudicial(): ?bool
    {
        return $this->judicial;
    }

    public function setJudicial($j)
    {
        return $this->judicial = $j;
    }

    public function getTotalLotes(): ?int
    {
        return $this->totalLotes;
    }

    public function setTotalLotes(?int $lotes): self
    {
        $this->totalLotes = $lotes;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus($status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getPraca(): ?int
    {
        return $this->praca ?: 1;
    }

    public function setPraca(?int $praca): self
    {
        $this->praca = $praca;

        return $this;
    }

    public function getLeiloeiro(): ?string
    {
        return $this->leiloeiro;
    }

    public function setLeiloeiro(?string $leiloeiro): self
    {
        $this->leiloeiro = $leiloeiro;

        return $this;
    }

    public function getLeiloeiroLogo(): ?string
    {
        return $this->leiloeiroLogo;
    }

    public function setLeiloeiroLogo(?string $leiloeiroLogo): self
    {
        $this->leiloeiroLogo = $leiloeiroLogo;

        return $this;
    }

    public function getLocal()
    {
        return $this->local;
    }

    public function setLocal($local): self
    {
        $this->local = $local;

        return $this;
    }

    public function getLocalLat(): ?string
    {
        return $this->localLat;
    }

    public function setLocalLat(?string $localLat): self
    {
        $this->localLat = $localLat;

        return $this;
    }

    public function getLocalLng(): ?string
    {
        return $this->localLng;
    }

    public function setLocalLng(?string $localLng): self
    {
        $this->localLng = $localLng;

        return $this;
    }

    public function getLocalGoogleMaps(): ?string
    {
        return $this->localGoogleMaps;
    }

    public function setLocalGoogleMaps(?string $localGoogleMaps): self
    {
        $this->localGoogleMaps = $localGoogleMaps;

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

    public function getInfoRetirada(): ?string
    {
        return $this->infoRetirada;
    }

    public function setInfoRetirada(?string $infoRetirada): self
    {
        $this->infoRetirada = $infoRetirada;

        return $this;
    }

    public function getObservacoes(): ?string
    {
        return $this->observacoes;
    }

    public function setObservacoes(?string $observacoes): self
    {
        $this->observacoes = $observacoes;

        return $this;
    }

    public function getArquivos(): ?array
    {
        return $this->arquivos;
    }

    public function getArquivosSemEdital()
    {
        if (!is_array($this->arquivos)) {
            return [];
        }
        $edital = $this->getEdital();
        $arquivos = new ArrayCollection();

        foreach ($this->arquivos as $arquivo) {
            if ($arquivo === $edital) continue;
            $arquivos->add($arquivo);
        }

        return $arquivos;
    }

    public function setArquivos(?array $arquivos): self
    {
        $this->arquivos = $arquivos;

        return $this;
    }

    public function getComitentes(): ?array
    {
        return $this->comitentes;
    }

    public function setComitentes(?array $comitentes): self
    {
        $this->comitentes = $comitentes;

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

    public function isEncerrado()
    {
        return $this->statusTipo === self::STATUS_TIPO_ENCERRADO;
    }

    /**
     * @return Collection|Lote[]
     */
    public function getLotes(): Collection
    {
        return $this->lotes;
    }

    public function setLotesManual($lotes)
    {
        $this->lotes = $lotes;
    }

    public function addLote(Lote $lote): self
    {
        if (!$this->lotes->contains($lote)) {
            $this->lotes[] = $lote;
            $lote->setLeilao($this);
        }

        return $this;
    }

    public function removeLote(Lote $lote): self
    {
        if ($this->lotes->contains($lote)) {
            $this->lotes->removeElement($lote);
            // set the owning side to null (unless already changed)
            if ($lote->getLeilao() === $this) {
                $lote->setLeilao(null);
            }
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param mixed $slug
     */
    public function setSlug($slug): void
    {
        $this->slug = $slug;
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
     * @return mixed
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param mixed $image
     */
    public function setImage($image): void
    {
        $this->image = $image;
    }

    // TODO: Verificar se existe PDF do edital. Se não existir, tentar carregar o edital baseado no template da API
    public function getEdital()
    {
        if (empty($this->arquivos)) {
            return null;
        }
        foreach ($this->arquivos as $arquivo) {
            if ($arquivo['tipo']['id'] === 1) {
                return $arquivo;
            }
        }
        return null;
    }

    /**
     * @return LeilaoCache
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * @param mixed $cache
     */
    public function setCache(LeilaoCache $cache)
    {
        $this->cache = $cache;
    }

    public function emLeilao()
    {
        return $this->status === self::STATUS_TIPO_EM_LEILAO;
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
    public function getEnderecoNumero()
    {
        return $this->enderecoNumero;
    }

    /**
     * @param mixed $enderecoNumero
     */
    public function setEnderecoNumero($enderecoNumero): void
    {
        $this->enderecoNumero = $enderecoNumero;
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
    public function getEnderecoReferencia()
    {
        return $this->enderecoReferencia;
    }

    /**
     * @param mixed $enderecoReferencia
     */
    public function setEnderecoReferencia($enderecoReferencia): void
    {
        $this->enderecoReferencia = $enderecoReferencia;
    }

    /**
     * @return string
     */
    public function getTimezone(): string
    {
        return $this->timezone;
    }

    /**
     * @param string $timezone
     */
    public function setTimezone(string $timezone): void
    {
        $this->timezone = $timezone;
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
     * @return int
     */
    public function getHabilitacao(): int
    {
        return $this->habilitacao;
    }

    /**
     * @param int $habilitacao
     */
    public function setHabilitacao(int $habilitacao): void
    {
        $this->habilitacao = $habilitacao;
    }

    /**
     * @return int
     */
    public function getHabilitados(): int
    {
        return $this->habilitados;
    }

    /**
     * @param int $habilitados
     */
    public function setHabilitados(int $habilitados): void
    {
        $this->habilitados = $habilitados;
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
    public function getVideo()
    {
        return $this->video;
    }

    /**
     * @param mixed $video
     */
    public function setVideo($video): void
    {
        $this->video = $video;
    }

    /**
     * @return mixed
     */
    public function getRegras()
    {
        return $this->regras;
    }

    /**
     * @param mixed $regras
     */
    public function setRegras($regras): void
    {
        $this->regras = $regras;
    }

    /**
     * @return mixed
     */
    public function getTextoPropostas()
    {
        return $this->textoPropostas;
    }

    /**
     * @param mixed $textoPropostas
     */
    public function setTextoPropostas($textoPropostas): void
    {
        $this->textoPropostas = $textoPropostas;
    }

    /**
     * @return mixed
     */
    public function getDataProximoLeilao()
    {
        return $this->dataProximoLeilao;
    }

    /**
     * @param mixed $dataProximoLeilao
     */
    public function setDataProximoLeilao($dataProximoLeilao): void
    {
        $this->dataProximoLeilao = $dataProximoLeilao;
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
     * @return int
     */
    public function getStatusTipo(): int
    {
        return $this->statusTipo;
    }

    /**
     * @param int $statusTipo
     */
    public function setStatusTipo(int $statusTipo): void
    {
        $this->statusTipo = $statusTipo;
    }

    /**
     * @return mixed
     */
    public function getCidade()
    {
        return $this->cidade;
    }

    /**
     * @param mixed $cidade
     */
    public function setCidade($cidade): void
    {
        $this->cidade = $cidade;
    }

    /**
     * @return mixed
     */
    public function getUf()
    {
        return $this->uf;
    }

    /**
     * @param mixed $uf
     */
    public function setUf($uf): void
    {
        $this->uf = $uf;
    }

    /**
     * @return mixed
     */
    public function getEditalHtml()
    {
        return $this->editalHtml;
    }

    /**
     * @param mixed $editalHtml
     */
    public function setEditalHtml($editalHtml): void
    {
        $this->editalHtml = $editalHtml;
    }

    /**
     * @return mixed
     */
    public function getData1()
    {
        return $this->data1;
    }

    /**
     * @param mixed $data1
     */
    public function setData1($data1): void
    {
        $this->data1 = $data1;
    }

    /**
     * @return mixed
     */
    public function getData2()
    {
        return $this->data2;
    }

    /**
     * @param mixed $data2
     */
    public function setData2($data2): void
    {
        $this->data2 = $data2;
    }

    /**
     * @return mixed
     */
    public function getData3()
    {
        return $this->data3;
    }

    /**
     * @param mixed $data3
     */
    public function setData3($data3): void
    {
        $this->data3 = $data3;
    }

    /**
     * @return mixed
     */
    public function getDataAbertura1()
    {
        return $this->dataAbertura1;
    }

    /**
     * @param mixed $dataAbertura1
     */
    public function setDataAbertura1($dataAbertura1): void
    {
        $this->dataAbertura1 = $dataAbertura1;
    }

    /**
     * @return mixed
     */
    public function getDataAbertura2()
    {
        return $this->dataAbertura2;
    }

    /**
     * @param mixed $dataAbertura2
     */
    public function setDataAbertura2($dataAbertura2): void
    {
        $this->dataAbertura2 = $dataAbertura2;
    }

    /**
     * @return mixed
     */
    public function getDataAbertura3()
    {
        return $this->dataAbertura3;
    }

    /**
     * @param mixed $dataAbertura3
     */
    public function setDataAbertura3($dataAbertura3): void
    {
        $this->dataAbertura3 = $dataAbertura3;
    }

    /**
     * @return int
     */
    public function getInstancia(): int
    {
        return $this->instancia;
    }

    /**
     * @param int $instancia
     */
    public function setInstancia(int $instancia): void
    {
        $this->instancia = $instancia;
    }

    /**
     * @return mixed
     */
    public function getSistemaTaxa()
    {
        return $this->sistemaTaxa;
    }

    /**
     * @param mixed $sistemaTaxa
     */
    public function setSistemaTaxa($sistemaTaxa): void
    {
        $this->sistemaTaxa = $sistemaTaxa;
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
    public function getLeiloeiroUrl()
    {
        return $this->leiloeiroUrl;
    }

    /**
     * @param mixed $leiloeiroUrl
     */
    public function setLeiloeiroUrl($leiloeiroUrl): void
    {
        $this->leiloeiroUrl = $leiloeiroUrl;
    }

    /**
     * @return mixed
     */
    public function getLeiloeiroMatricula()
    {
        return $this->leiloeiroMatricula;
    }

    /**
     * @param mixed $leiloeiroMatricula
     */
    public function setLeiloeiroMatricula($leiloeiroMatricula): void
    {
        $this->leiloeiroMatricula = $leiloeiroMatricula;
    }

    /**
     * @return mixed
     */
    public function getLeiloeiroUf()
    {
        return $this->leiloeiroUf;
    }

    /**
     * @param mixed $leiloeiroUf
     */
    public function setLeiloeiroUf($leiloeiroUf): void
    {
        $this->leiloeiroUf = $leiloeiroUf;
    }

    /**
     * @return mixed
     */
    public function getClassificacaoId()
    {
        return $this->classificacaoId;
    }

    /**
     * @param mixed $classificacaoId
     */
    public function setClassificacaoId($classificacaoId): void
    {
        $this->classificacaoId = $classificacaoId;
    }

    /**
     * @return mixed
     */
    public function getClassificacao()
    {
        return $this->classificacao;
    }

    /**
     * @param mixed $classificacao
     */
    public function setClassificacao($classificacao): void
    {
        $this->classificacao = $classificacao;
    }

    /**
     * @return mixed
     */
    public function getVisitas()
    {
        return $this->visitas;
    }

    /**
     * @param mixed $visitas
     */
    public function setVisitas($visitas): void
    {
        $this->visitas = $visitas;
    }

    /**
     * @return mixed
     */
    public function getLances()
    {
        return $this->lances;
    }

    /**
     * @param mixed $lances
     */
    public function setLances($lances): void
    {
        $this->lances = $lances;
    }

    /**
     * @return mixed
     */
    public function getCodigo()
    {
        return $this->codigo;
    }

    /**
     * @param mixed $codigo
     */
    public function setCodigo($codigo): void
    {
        $this->codigo = $codigo;
    }

    /**
     * @return mixed
     */
    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * @param mixed $numero
     */
    public function setNumero($numero): void
    {
        $this->numero = $numero;
    }

    /**
     * @return mixed
     */
    public function getAno()
    {
        return $this->ano;
    }

    /**
     * @param mixed $ano
     */
    public function setAno($ano): void
    {
        $this->ano = $ano;
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
    public function getInfoPagamento()
    {
        return $this->infoPagamento;
    }

    /**
     * @param mixed $infoPagamento
     */
    public function setInfoPagamento($infoPagamento): void
    {
        $this->infoPagamento = $infoPagamento;
    }

    public function __serialize(): array
    {
        return [
            'id' => $this->id,
            'aid' => $this->getAid(),
            'slug' => $this->slug,
            'codigo',
            'numero',
            'ano',
            'data1',
            'data2',
            'data3',
            'dataAbertura1',
            'dataAbertura2',
            'dataAbertura3',
            'titulo' => $this->titulo,
            'descricao' => $this->descricao,
            'tipo' => $this->tipo,
            'dataProximoLeilao' => $this->dataProximoLeilao,
            'judicial' => $this->judicial,
            'praca' => $this->praca,
            'totalLotes' => $this->totalLotes,
            'status' => $this->status,
            'statusString' => $this->statusString,
            'leiloeiro' => $this->leiloeiro,
            'leiloeiroLogo' => $this->leiloeiroLogo,
            'leiloeiroMatricula' => $this->leiloeiroMatricula,
            'leiloeiroUf' => $this->leiloeiroUf,
            'leiloeiroUrl' => $this->leiloeiroUrl,
            'classificacaoId' => $this->classificacaoId,
            'classificacao' => $this->classificacao,
            'sistemaTaxa' => $this->sistemaTaxa,
            'habilitacao' => $this->habilitacao,
            'habilitados' => $this->habilitados,
            'visitas' => $this->visitas,
            'lances' => $this->lances,
            'permitirParcelamento' => $this->permitirParcelamento,
            'parcelamentoMinimoEntrada' => $this->parcelamentoMinimoEntrada,
            'parcelamentoQtdParcelas' => $this->parcelamentoQtdParcelas,
            'parcelamentoIndices' => $this->parcelamentoIndices,
            'vendaDireta' => $this->vendaDireta,
            'dataLimitePropostas' => $this->dataLimitePropostas,
        ];
    }

}
