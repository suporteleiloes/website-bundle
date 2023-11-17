<?php

namespace SL\WebsiteBundle\Entity;

use SL\WebsiteBundle\Repository\LeilaoExtraRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=LeilaoExtraRepository::class)
 */
class LeilaoExtra
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    private $leilao;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $data;

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

}
