<?php

namespace SL\WebsiteBundle\Entity;

use SL\WebsiteBundle\Repository\ContentRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ContentRepository::class)
 */
class Content extends ApiSync
{

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $pageName;

    /**
     * @ORM\Column(type="text")
     */
    private $pageDescription;

    /**
     * @ORM\Column(type="text")
     */
    private $template;

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

    public function getPageName(): ?string
    {
        return $this->pageName;
    }

    public function setPageName(string $pageName): self
    {
        $this->pageName = $pageName;

        return $this;
    }

    public function getPageDescription(): ?string
    {
        return $this->pageDescription;
    }

    public function setPageDescription(string $pageDescription): self
    {
        $this->pageDescription = $pageDescription;

        return $this;
    }

    public function getTemplate(): ?string
    {
        return $this->template;
    }

    public function setTemplate(string $template): self
    {
        $this->template = $template;

        return $this;
    }

    /*static function loadApiRepresentation(ApiRepresentationMetadataInterface $representation)
    {
        parent::loadApiRepresentation($representation);

        $public = [
            'id',
            'title',
            'pageName',
            'pageDescription',
            'template'
        ];

        $representation
            ->setGroup('public')
            ->addProperties($public)
        ;
    }*/
}
