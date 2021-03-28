<?php

namespace SL\WebsiteBundle\Entity;

use SL\WebsiteBundle\Repository\MenuRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MenuRepository::class)
 */
class Menu extends ApiSync
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $link;

    /**
     * @ORM\Column(type="smallint")
     */
    private $section;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(string $link): self
    {
        $this->link = $link;

        return $this;
    }

    public function getSection(): ?int
    {
        return $this->section;
    }

    public function setSection(int $section): self
    {
        $this->section = $section;

        return $this;
    }

    /*static function loadApiRepresentation(ApiRepresentationMetadataInterface $representation)
    {
        parent::loadApiRepresentation($representation);

        $public = [
            'id',
            'title',
            'link',
            'section'
        ];

        $representation
            ->setGroup('public')
            ->addProperties($public)
        ;
    }*/
}
