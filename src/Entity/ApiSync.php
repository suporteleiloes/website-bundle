<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperclass()
 */
class ApiSync
{

    /**
     * @ORM\Column(type="integer")
     */
    private $aid;

    /**
     * @ORM\Column(type="datetime")
     */
    private $acreatedAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $alastUpdate;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAid(): ?int
    {
        return $this->aid;
    }

    public function setAid(int $aid): self
    {
        $this->aid = $aid;

        return $this;
    }

    public function getAcreatedAt(): ?\DateTimeInterface
    {
        return $this->acreatedAt;
    }

    public function setAcreatedAt(\DateTimeInterface $date): self
    {
        $this->acreatedAt = $date;

        return $this;
    }

    public function getAlastUpdate(): ?\DateTimeInterface
    {
        return $this->alastUpdate;
    }

    public function setAlastUpdate(?\DateTimeInterface $date): self
    {
        $this->alastUpdate = $date;

        return $this;
    }
}
