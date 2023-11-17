<?php

namespace SL\WebsiteBundle\Entity;

use SL\WebsiteBundle\Repository\LoteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=LoteRepository::class)
 */
class LeilaoExtra
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    private $leilaoId;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $data;

    /**
     * @return mixed
     */
    public function getLeilaoId()
    {
        return $this->leilaoId;
    }

    /**
     * @param mixed $leilaoId
     */
    public function setLeilaoId($leilaoId): void
    {
        $this->leilaoId = $leilaoId;
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
