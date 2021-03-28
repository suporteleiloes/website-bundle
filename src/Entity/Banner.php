<?php

namespace SL\WebsiteBundle\Entity;

use SL\WebsiteBundle\Repository\BannerRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=BannerRepository::class)
 */
class Banner extends ApiSync
{
    const TYPE_BANNER = 1;
    const TYPE_POPUP = 2;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="smallint")
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="smallint")
     */
    private $position = 0;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateStartExhibition;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateEndExhibition;

    /**
     * @ORM\Column(type="boolean")
     */
    private $hasVideo = false;

    /**
     * @ORM\Column(type="object", nullable=true)
     */
    private $image;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $link;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(int $type): self
    {
        $this->type = $type;

        return $this;
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

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getDateStartExhibition(): ?\DateTimeInterface
    {
        return $this->dateStartExhibition;
    }

    public function setDateStartExhibition(\DateTimeInterface $dateStartExhibition): self
    {
        $this->dateStartExhibition = $dateStartExhibition;

        return $this;
    }

    public function getDateEndExhibition(): ?\DateTimeInterface
    {
        return $this->dateEndExhibition;
    }

    public function setDateEndExhibition(\DateTimeInterface $dateEndExhibition): self
    {
        $this->dateEndExhibition = $dateEndExhibition;

        return $this;
    }

    public function getHasVideo(): ?bool
    {
        return $this->hasVideo;
    }

    public function setHasVideo(bool $hasVideo): self
    {
        $this->hasVideo = $hasVideo;

        return $this;
    }

    public function getImage()
    {
        return $this->image;
    }

    public function setImage($image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(?string $link): self
    {
        $this->link = $link;

        return $this;
    }

    /*static function loadApiRepresentation(ApiRepresentationMetadataInterface $representation)
    {
        parent::loadApiRepresentation($representation);

        $public = [
            'id',
            'type',
            'title',
            'image',
            'position',
            'dateStartExhibition',
            'dateEndExhibition',
            'hasVideo',
            'link'
        ];

        $representation
            ->setGroup('public')
            ->addProperties($public)
        ;
    }*/
}
