<?php

namespace SL\WebsiteBundle\Entity;

use SL\WebsiteBundle\Repository\PropostaRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(indexes={
 *     @ORM\Index(name="data", columns={"data"}),
 *     @ORM\Index(name="bem_id", columns={"bem_id"}),
 *     @ORM\Index(name="lote_id", columns={"lote_id"}),
 * })
 * @ORM\Entity(repositoryClass=PropostaRepository::class)
 */
class Proposta extends ApiSync
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\NotBlank(message="Informe seu nome")
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $nome;

    /**
     * @Assert\NotBlank(message="Informe seu e-mail")
     * @Assert\Email(message="Digite un e-mail válido")
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @Assert\NotBlank(message="Informe seu telefone")
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $telefone;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $assunto;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $mensagem;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $data;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $ip;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $bemId;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $loteId;

    /**
     * @ORM\Column(type="decimal", precision=15, scale=2, nullable=true)
     */
    private $valor;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $parcelado = false;

    /**
     * @ORM\Column(type="decimal", precision=15, scale=2, nullable=true)
     */
    private $valorEntrada;

    /**
     * @ORM\Column(type="decimal", precision=4, scale=2, nullable=true)
     */
    private $porcentagemEntrada;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $quantidadeParcelas;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $indiceCorrecao;

    public function __construct()
    {
        $this->data = new \DateTime();
        $this->setAcreatedAt(new \DateTime());
        $this->setActive(true);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNome(): ?string
    {
        return $this->nome;
    }

    public function setNome(?string $nome): self
    {
        $this->nome = $nome;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getTelefone(): ?string
    {
        return $this->telefone;
    }

    public function setTelefone(?string $telefone): self
    {
        $this->telefone = $telefone;

        return $this;
    }

    public function getAssunto(): ?string
    {
        return $this->assunto;
    }

    public function setAssunto(?string $assunto): self
    {
        $this->assunto = $assunto;

        return $this;
    }

    public function getMensagem(): ?string
    {
        return $this->mensagem;
    }

    public function setMensagem(?string $mensagem): self
    {
        $this->mensagem = $mensagem;

        return $this;
    }

    public function getData(): ?\DateTimeInterface
    {
        return $this->data;
    }

    public function setData(?\DateTimeInterface $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function getIp(): ?string
    {
        return $this->ip;
    }

    public function setIp(?string $ip): self
    {
        $this->ip = $ip;

        return $this;
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
    public function getLoteId()
    {
        return $this->loteId;
    }

    /**
     * @param mixed $loteId
     */
    public function setLoteId($loteId): void
    {
        $this->loteId = $loteId;
    }

    /**
     * @return mixed
     */
    public function getValor()
    {
        return $this->valor;
    }

    /**
     * @param mixed $valor
     */
    public function setValor($valor): void
    {
        $this->valor = $valor;
    }

    /**
     * @return bool
     */
    public function isParcelado(): bool
    {
        return $this->parcelado;
    }

    /**
     * @param bool $parcelado
     */
    public function setParcelado(bool $parcelado): void
    {
        $this->parcelado = $parcelado;
    }

    /**
     * @return mixed
     */
    public function getPorcentagemEntrada()
    {
        return $this->porcentagemEntrada;
    }

    /**
     * @param mixed $porcentagemEntrada
     */
    public function setPorcentagemEntrada($porcentagemEntrada): void
    {
        $this->porcentagemEntrada = $porcentagemEntrada;
    }

    /**
     * @return mixed
     */
    public function getQuantidadeParcelas()
    {
        return $this->quantidadeParcelas;
    }

    /**
     * @param mixed $quantidadeParcelas
     */
    public function setQuantidadeParcelas($quantidadeParcelas): void
    {
        $this->quantidadeParcelas = $quantidadeParcelas;
    }

    /**
     * @return mixed
     */
    public function getIndiceCorrecao()
    {
        return $this->indiceCorrecao;
    }

    /**
     * @param mixed $indiceCorrecao
     */
    public function setIndiceCorrecao($indiceCorrecao): void
    {
        $this->indiceCorrecao = $indiceCorrecao;
    }

    /**
     * @return mixed
     */
    public function getValorEntrada()
    {
        return $this->valorEntrada;
    }

    /**
     * @param mixed $valorEntrada
     */
    public function setValorEntrada($valorEntrada): void
    {
        $this->valorEntrada = $valorEntrada;
    }

    public function __serialize()
    {
        return [
            'id' => $this->id,
            'nome' => $this->nome,
            'telefone' => $this->telefone,
            'email' => $this->email,
            'assunto' => $this->assunto,
            'mensagem' => $this->mensagem,
            'data' => $this->data,
            'ip' => $this->ip,
            'bemId' => $this->bemId,
            'loteId' => $this->loteId,
            'valor' => $this->valor,
            'valorEntrada' => $this->valorEntrada,
            'parcelado' => $this->parcelado,
            'porcentagemEntrada' => $this->porcentagemEntrada,
            'parcelas' => $this->quantidadeParcelas,
            'indice' => $this->indiceCorrecao
        ];
    }

}
