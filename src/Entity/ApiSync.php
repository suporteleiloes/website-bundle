<?php

namespace SL\WebsiteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperclass()
 * * @ORM\Table(indexes={
 *     @ORM\Index(name="aid", columns={"aid"}),
 *     @ORM\Index(name="active", columns={"active"}),
 *     @ORM\Index(name="order", columns={"ordering"}),
 *     @ORM\Index(name="deleted", columns={"deleted"})
 * })
 */
class ApiSync
{

    /**
     * @ORM\Column(type="integer")
     */
    private $aid = 0;

    /**
     * @ORM\Column(type="datetime")
     */
    private $acreatedAt = '1999-01-01 00:00:00';

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $alastUpdate;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $version;

    /**
     * @ORM\Column(type="boolean")
     */
    private $deleted = false;

    /**
     * @ORM\Column(type="boolean", options={"unsigned":true, "default":1})
     */
    private $active = true;

    /**
     * @ORM\Column(type="smallint", name="ordering", options={"default":99})
     */
    private $order = 99;

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

    /**
     * @return mixed
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @param mixed $version
     */
    public function setVersion($version): void
    {
        $this->version = $version;
    }

    /**
     * @return mixed
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * @param mixed $active
     */
    public function setActive($active): void
    {
        $this->active = $active;
    }

    /**
     * @return bool
     */
    public function isDeleted(): bool
    {
        return $this->deleted;
    }

    /**
     * @param bool $deleted
     */
    public function setDeleted(bool $deleted): void
    {
        $this->deleted = $deleted;
    }

    /**
     * @return int
     */
    public function getOrder(): int
    {
        return $this->order;
    }

    /**
     * @param int $order
     */
    public function setOrder(?int $order): void
    {
        $this->order = $order;
    }
}
