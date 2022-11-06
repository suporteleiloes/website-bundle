<?php

namespace SL\WebsiteBundle\Entity;

use SL\WebsiteBundle\Repository\LanceRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=LanceRepository::class)
 * @ORM\Table(indexes={@ORM\Index(name="valor", columns={"valor"})})
 */
class Lance extends ApiSync
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $data;

    /**
     * @ORM\Column(type="integer", length=255, nullable=true)
     */
    private $arrematanteId;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $apelido;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nome;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $cidade;

    /**
     * @ORM\Column(type="string", length=2, nullable=true)
     */
    private $uf;

    /**
     * @ORM\Column(type="decimal", precision=15, scale=2)
     */
    private $valor;

    /**
     * @ORM\ManyToOne(targetEntity="SL\WebsiteBundle\Entity\Lote", inversedBy="lances")
     * @ORM\JoinColumn(nullable=false)
     */
    private $lote;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getData(): ?\DateTimeInterface
    {
        return $this->data;
    }

    public function setData(\DateTimeInterface $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function getApelido(): ?string
    {
        return $this->apelido;
    }

    public function setApelido(string $apelido): self
    {
        $this->apelido = $apelido;

        return $this;
    }

    public function getNome(): ?string
    {
        return $this->nome;
    }

    public function setNome(string $nome): self
    {
        $this->nome = $nome;

        return $this;
    }

    public function getValor(): ?string
    {
        return $this->valor;
    }

    public function setValor(string $valor): self
    {
        $this->valor = $valor;

        return $this;
    }

    public function getLote(): ?Lote
    {
        return $this->lote;
    }

    public function setLote(?Lote $lote): self
    {
        $this->lote = $lote;

        return $this;
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
    public function getArrematanteId()
    {
        return $this->arrematanteId;
    }

    /**
     * @param mixed $arrematanteId
     */
    public function setArrematanteId($arrematanteId): void
    {
        $this->arrematanteId = $arrematanteId;
    }

    public function __serialize(): array
    {
        return [
            'id' => $this->id,
            'aid' => $this->getAid(),
            'data' => $this->data,
            'arrematanteId' => $this->apelido,
            'apelido' => $this->apelido,
            'nome' => $this->nome,
            'cidade' => $this->cidade,
            'uf' => $this->uf,
            'valor' => $this->valor,
            'lote' => [
                'id' => $this->getLote()->getId(),
                'numero' => $this->getLote()->getNumero(),
            ],
        ];
    }


}
