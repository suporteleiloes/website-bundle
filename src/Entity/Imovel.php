<?php

namespace SL\WebsiteBundle\Entity;

use SL\WebsiteBundle\Repository\ImovelRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(indexes={
 *     @ORM\Index(name="ocupado", columns={"ocupado"})
 * })
 * @ORM\Entity(repositoryClass=ImovelRepository::class)
 */
class Imovel
{

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $ocupado;

    /**
     * @ORM\OneToOne(targetEntity="Bem", inversedBy="imovel")
     */
    private $bem;


}
