<?php

namespace SL\WebsiteBundle\Entity;

use SL\WebsiteBundle\Repository\VeiculoRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(indexes={
 *     @ORM\Index(name="marca_id", columns={"marca_id"}),
 *     @ORM\Index(name="marca", columns={"marca"}),
 *     @ORM\Index(name="modelo_id", columns={"modelo_id"}),
 *     @ORM\Index(name="modelo", columns={"modelo"}),
 * })
 * @ORM\Entity(repositoryClass=VeiculoRepository::class)
 */
class Veiculo
{

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\OneToOne(targetEntity="Bem", inversedBy="veiculo")
     */
    private $bem;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $marca;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $marcaId;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $modeloId;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $modelo;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $ano;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $placa;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $chassi;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $km;

    /**
     * @ORM\Column(type="string", length=40, nullable=true)
     */
    private $combustivel;

}
