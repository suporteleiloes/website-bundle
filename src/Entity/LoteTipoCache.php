<?php

namespace SL\WebsiteBundle\Entity;

use SL\WebsiteBundle\Repository\LoteTipoCacheRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=LoteTipoCacheRepository::class)
 */
class LoteTipoCache
{

    /**
     * @ORM\Id()
     * @ORM\Column(type="integer")
     */
    private $tipoId;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $tipo;

    /**
     * @ORM\Column(type="integer")
     */
    private $total;

    public function getId(): ?int
    {
        return $this->tipoId;
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
}
