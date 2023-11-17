<?php

namespace SL\WebsiteBundle\Entity;

use SL\WebsiteBundle\Repository\LoteExtraRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=LoteExtraRepository::class)
 */
class LoteExtra
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    private $lote;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $data;

    /**
     * @return mixed
     */
    public function getLote()
    {
        return $this->lote;
    }

    /**
     * @param mixed $lote
     */
    public function setLote($lote): void
    {
        $this->lote = $lote;
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
