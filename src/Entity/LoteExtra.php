<?php

namespace SL\WebsiteBundle\Entity;

use SL\WebsiteBundle\Repository\LoteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(indexes={
 *     @ORM\Index(name="lote_id", columns={"bem_id"}),
 * })
 * @ORM\Entity(repositoryClass=LoteRepository::class)
 */
class LoteExtra
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    private $loteId;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $data;

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
