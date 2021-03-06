<?php

namespace SL\WebsiteBundle\Entity;

use SL\WebsiteBundle\Repository\LoteTipoCacheRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(indexes={
 *     @ORM\Index(name="tipo_id", columns={"tipo_id"}),
 *     @ORM\Index(name="tipo_pai_id", columns={"tipo_pai_id"}),
 * })
 * @ORM\Entity(repositoryClass=LoteTipoCacheRepository::class)
 */
class LoteTipoCache
{

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $tipoId;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $tipoPaiId;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $tipo;

    /**
     * @ORM\Column(type="integer")
     */
    private $total;

    /**
     * @ORM\Column(type="boolean", options={"default": 0})
     */
    private $subtipo = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTipo(): ?string
    {
        return $this->tipo;
    }

    public function setTipo(string $tipo): self
    {
        $this->tipo = $tipo;

        return $this;
    }

    public function getTipoId(): ?int
    {
        return $this->tipoId;
    }

    public function setTipoId(?int $tipo): self
    {
        $this->tipoId = $tipo;

        return $this;
    }

    public function getTotal(): ?int
    {
        return $this->total;
    }

    public function setTotal(int $total): self
    {
        $this->total = $total;

        return $this;
    }

    /**
     * @return bool
     */
    public function isSubtipo(): bool
    {
        return $this->subtipo;
    }

    /**
     * @param bool $subtipo
     */
    public function setSubtipo(bool $subtipo): void
    {
        $this->subtipo = $subtipo;
    }

    /**
     * @return mixed
     */
    public function getTipoPaiId()
    {
        return $this->tipoPaiId;
    }

    /**
     * @param mixed $tipoPaiId
     */
    public function setTipoPaiId($tipoPaiId): void
    {
        $this->tipoPaiId = $tipoPaiId;
    }
}
