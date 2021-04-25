<?php

namespace App\Entity;

use App\Repository\StoredImageRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=StoredImageRepository::class)
 */
class StoredImage
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", auto)
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $storedLocation;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStoredLocation(): ?string
    {
        return $this->storedLocation;
    }

    public function setStoredLocation(string $storedLocation): self
    {
        $this->storedLocation = $storedLocation;

        return $this;
    }

    public function __toString(): string
    {
        return $this->storedLocation;
    }
}
