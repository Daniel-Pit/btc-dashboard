<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UsdHistoryRepository")
 */
class UsdHistory
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="bigint", options={"unsigned"=true})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $market;

    /**
     * @ORM\Column(type="float")
     */
    private $spot;

    /**
     * @ORM\Column(type="float")
     */
    private $price;

    /**
     * @ORM\Column(type="integer")
     */
    private $insert_time;

    public function getId()
    {
        return $this->id;
    }

    public function getMarket(): ?string
    {
        return $this->market;
    }

    public function setMarket(string $market): self
    {
        $this->market = $market;

        return $this;
    }

    public function getSpot(): ?float
    {
        return $this->spot;
    }

    public function setSpot(float $spot): self
    {
        $this->spot = $spot;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getInsertTime(): ?int
    {
        return $this->insert_time;
    }

    public function setInsertTime(int $insert_time): self
    {
        $this->insert_time = $insert_time;

        return $this;
    }
}
