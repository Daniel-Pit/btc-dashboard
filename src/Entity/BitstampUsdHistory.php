<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(indexes={@ORM\Index(name="bitstamp_usd_idx", columns={"insert_time"})})
 * @ORM\Entity(repositoryClass="App\Repository\BitstampUsdHistoryRepository")
 */
class BitstampUsdHistory
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="bigint", options={"unsigned"=true})
     */
    private $id;

    /**
     * @ORM\Column(type="float")
     */
    private $spot;

    /**
     * @ORM\Column(type="integer")
     */
    private $insert_time;

    public function getId()
    {
        return $this->id;
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
