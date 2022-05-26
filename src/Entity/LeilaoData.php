<?php

namespace SL\WebsiteBundle\Entity;

use SL\WebsiteBundle\Repository\LeilaoDataRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=LeilaoDataRepository::class)
 */
class LeilaoData
{

    /**
     * @ORM\ManyToOne(targetEntity="SL\WebsiteBundle\Entity\Leilao", inversedBy="datas")
     * @ORM\Id()
     */
    private $leilao;

    /**
     * @ORM\Id()
     * @ORM\Column(type="datetime")
     */
    private $data;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $abertura;

    /**
     * @ORM\Column(type="boolean")
     */
    private $encerrado = false;

    /**
     * @return mixed
     */
    public function getLeilao()
    {
        return $this->leilao;
    }

    /**
     * @param mixed $leilao
     */
    public function setLeilao($leilao): void
    {
        $this->leilao = $leilao;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     */
    public function setData($data): void
    {
        $this->data = $data;
    }

    /**
     * @return mixed
     */
    public function getAbertura()
    {
        return $this->abertura;
    }

    /**
     * @param mixed $abertura
     */
    public function setAbertura($abertura): void
    {
        $this->abertura = $abertura;
    }

    /**
     * @return bool
     */
    public function isEncerrado(): bool
    {
        return $this->encerrado;
    }

    /**
     * @param bool $encerrado
     */
    public function setEncerrado(bool $encerrado): void
    {
        $this->encerrado = $encerrado;
    }

}
