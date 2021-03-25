<?php

namespace App\Entity;

use App\Repository\LeilaoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
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

    const STATUS_INTERNO_EM_PREPARACAO = 0;
    const STATUS_INTERNO_PREPARADO = 1;
    const STATUS_INTERNO_EM_LEILAO = 2;
    const STATUS_INTERNO_EM_RECEBIMENTO = 3;
    const STATUS_INTERNO_ENCERRADO = 100;

    const LIST_STATUS_PERMITIDO_LANCE = [3, 4];

    public static $statusTitles = array(
        self::STATUS_RASCUNHO => 'Rascunho',
        self::STATUS_EM_BREVE => 'Em breve',
        self::STATUS_EM_LOTEAMENTO => 'Em loteamento',
        self::STATUS_ABERTO_PARA_LANCES => 'Aberto para lances',
        self::STATUS_EM_LEILAO => 'Em leilão',
        self::STATUS_CANCELADO => 'Cancelado',
        self::STATUS_ADIADO => 'Adiado',
        self::STATUS_SUSPENSO => 'Suspenso',
        self::STATUS_ENCERRADO => 'Encerrado'
    );

    public static $statusInternoTitles = array(
        self::STATUS_INTERNO_EM_PREPARACAO => 'Em preparação',
        self::STATUS_INTERNO_PREPARADO => 'Preparado',
        self::STATUS_INTERNO_EM_LEILAO => 'Em leilão',
        self::STATUS_INTERNO_EM_RECEBIMENTO => 'Em recebimento',
        self::STATUS_INTERNO_ENCERRADO => 'Fechado',
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

    public function getJudicialString()
    {
        return $this->judicial ? 'Judicial' : 'Extrajudicial';
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
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dataPraca1;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dataFimPraca1;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dataPraca2;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dataFimPraca2;

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
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $totalLotes;

    /**
     * @ORM\Column(type="smallint")
     */
    private $status;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $praca = 1;

    /**
     * @ORM\Column(type="smallint", nullable=true, options={"default": 1})
     */
    private $instancia = 1;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $leiloeiro;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $leiloeiroLogo;

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
     * @ORM\Column(type="boolean")
     */
    private $deleted = false;

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
    private $enderecoReferencia;

    /**
     * @ORM\Column(type="string", length=50, nullable=true, options={"default": "America/Sao_Paulo"})
     */
    private $timezone = 'America/Sao_Paulo';

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $vendaDireta;

    /**
     * 0 = Desativar, 1 = Permitir e obrigatório, 2 = Permitir e não obrigatório
     * @ORM\Column(type="smallint", nullable=true, options={"default": 1})
     */
    private $habilitacao = 1;

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
     * @ORM\OneToMany(targetEntity="App\Entity\Lote", mappedBy="leilao", orphanRemoval=true)
     * @ORM\OrderBy({"numero" = "ASC", "id" = "ASC"})
     */
    private $lotes;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\LeilaoCache", mappedBy="leilao")
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

    public function getDataPraca1(): ?\DateTimeInterface
    {
        return $this->dataPraca1;
    }

    public function setDataPraca1(?\DateTimeInterface $dataPraca1): self
    {
        $this->dataPraca1 = $dataPraca1;

        return $this;
    }

    public function getDataPraca2(): ?\DateTimeInterface
    {
        return $this->dataPraca2;
    }

    public function setDataPraca2(?\DateTimeInterface $dataPraca2): self
    {
        $this->dataPraca2 = $dataPraca2;

        return $this;
    }

    public function getDataAbertura(): ?\DateTimeInterface
    {
        if ($this->praca === 2) {
            return $this->getDataPraca2();
        }
        return $this->getDataPraca1();
    }

    public function getDataEncerramento(): ?\DateTimeInterface
    {
        if ($this->praca === 2) {
            return $this->getDataFimPraca2();
        }
        return $this->getDataFimPraca1();
    }

    public function getDataAberturaOuEncerramento(): ?\DateTimeInterface
    {
        $now = new \DateTime();
        if ($this->praca === 2) {
            if ($this->getDataFimPraca2()) {
                if ($this->getDataPraca2() < $now) {
                    return $this->getDataFimPraca2();
                }
            }
            return $this->getDataPraca2();
        }
        if ($this->getDataFimPraca1()) {
            if ($this->getDataPraca1() < $now) {
                return $this->getDataFimPraca1();
            }
        }
        return $this->getDataPraca1();
    }

    public function getJudicial(): ?bool
    {
        return $this->judicial;
    }

    /**
     * @return mixed
     */
    public function getDataFimPraca1()
    {
        return $this->dataFimPraca1;
    }

    /**
     * @param mixed $dataFimPraca1
     */
    public function setDataFimPraca1($dataFimPraca1): void
    {
        $this->dataFimPraca1 = $dataFimPraca1;
    }

    /**
     * @return mixed
     */
    public function getDataFimPraca2()
    {
        return $this->dataFimPraca2;
    }

    /**
     * @param mixed $dataFimPraca2
     */
    public function setDataFimPraca2($dataFimPraca2): void
    {
        $this->dataFimPraca2 = $dataFimPraca2;
    }

    public function setJudicial(?bool $judicial): self
    {
        $this->judicial = $judicial;

        return $this;
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
        return $this->praca ? $this->praca : 1;
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
        return (!$this->getDataPraca2() && $this->status > self::STATUS_EM_LEILAO) || ($this->getDataPraca2() && $this->status > self::STATUS_EM_LEILAO && $this->praca === 2);
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
     * @return bool
     */
    public function isDeleted(): bool
    {
        return $this->deleted;
    }

    /**
     * @param bool $deleted
     */
    public function setDeleted(bool $deleted): void
    {
        $this->deleted = $deleted;
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
        return $this->status === self::STATUS_EM_LEILAO;
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
     * @return int
     */
    public function getInstancia()
    {
        return $this->instancia;
    }

    /**
     * @param int $instancia
     */
    public function setInstancia($instancia)
    {
        $this->instancia = $instancia;
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

    public function __serialize(): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'titulo' => $this->titulo,
            'descricao' => $this->descricao,
            'tipo' => $this->tipo,
            'dataPraca1' => $this->dataPraca1,
            'dataFimPraca1' => $this->dataFimPraca1,
            'dataPraca2' => $this->dataPraca2,
            'dataFimPraca2' => $this->dataFimPraca2,
            'judicial' => $this->judicial,
            'totalLotes' => $this->totalLotes,
            'status' => $this->status,
            'leiloeiro' => $this->leiloeiro,
            'leiloeiroLogo' => $this->leiloeiroLogo,
        ];
    }

}
