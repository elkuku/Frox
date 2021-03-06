<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\WaypointRepository")
 * @ApiResource
 */
class Waypoint
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $name = '';

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $description = '';

    /**
     * @ORM\Column(type="decimal", precision=10, scale=6)
     */
    private float $lat = 0;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=6)
     */
    private float $lon = 0;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Category", inversedBy="waypoints")
     */
    private ?Category $category = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Province", inversedBy="waypoints")
     */
    private ?Province $province = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $city = '';

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private ?string $guid = '';

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getLat(): ?float
    {
        return $this->lat;
    }

    public function setLat(float $lat): self
    {
        $this->lat = $lat;

        return $this;
    }

    public function getLon(): ?float
    {
        return $this->lon;
    }

    public function setLon(float $lon): self
    {
        $this->lon = $lon;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getProvince(): ?Province
    {
        return $this->province;
    }

    public function setProvince(?Province $province): self
    {
        $this->province = $province;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getGpxRaw(): void
    {
    }

    public function __toString()
    {
        return (string)$this->getName();
    }

    public function getGuid(): ?string
    {
        return $this->guid;
    }

    public function setGuid(string $guid): self
    {
        $this->guid = $guid;

        return $this;
    }
}
