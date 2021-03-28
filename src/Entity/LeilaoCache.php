<?php

namespace SL\WebsiteBundle\Entity;

use SL\WebsiteBundle\Repository\LeilaoCacheRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=LeilaoCacheRepository::class)
 */
class LeilaoCache
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $filtros = [];

    /**
     * @ORM\OneToOne(targetEntity="SL\WebsiteBundle\Entity\Leilao", inversedBy="cache")
     * @ORM\JoinColumn(name="leilao_id", referencedColumnName="id")
     */
    private $leilao;

    public function getId()
    {
        return $this->id;
    }

    public function getFiltros()
    {
        return $this->filtros;
    }

    public function setFiltros($filtros)
    {
        $this->filtros = $filtros;

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
     * @param mixed $leilao
     */
    public function setLeilao(Leilao $leilao)
    {
        $this->leilao = $leilao;
    }
}
