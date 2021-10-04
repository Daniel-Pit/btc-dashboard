<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BtcMarketDataRepository")
 */
class BtcMarketData
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $currency;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $market;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $ago_24h_change;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $ago_24h_high;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $ago_24h_low;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $ago_1w_change;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $ago_1w_high;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $ago_1w_low;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $ago_1m_change;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $ago_1m_high;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $ago_1m_low;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $ago_3m_change;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $ago_3m_high;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $ago_3m_low;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $ago_6m_change;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $ago_6m_high;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $ago_6m_low;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $ago_1y_change;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $ago_1y_high;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $ago_1y_low;

    public function getId()
    {
        return $this->id;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): self
    {
        $this->currency = $currency;

        return $this;
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

    public function getAgo24hChange(): ?float
    {
        return $this->ago_24h_change;
    }

    public function setAgo24hChange(?float $ago_24h_change): self
    {
        $this->ago_24h_change = $ago_24h_change;

        return $this;
    }

    public function getAgo24hHigh(): ?float
    {
        return $this->ago_24h_high;
    }

    public function setAgo24hHigh(?float $ago_24h_high): self
    {
        $this->ago_24h_high = $ago_24h_high;

        return $this;
    }

    public function getAgo24hLow(): ?float
    {
        return $this->ago_24h_low;
    }

    public function setAgo24hLow(?float $ago_24h_low): self
    {
        $this->ago_24h_low = $ago_24h_low;

        return $this;
    }

    public function getAgo1wChange(): ?float
    {
        return $this->ago_1w_change;
    }

    public function setAgo1wChange(?float $ago_1w_change): self
    {
        $this->ago_1w_change = $ago_1w_change;

        return $this;
    }

    public function getAgo1wHigh(): ?float
    {
        return $this->ago_1w_high;
    }

    public function setAgo1wHigh(float $ago_1w_high): self
    {
        $this->ago_1w_high = $ago_1w_high;

        return $this;
    }

    public function getAgo1wLow(): ?float
    {
        return $this->ago_1w_low;
    }

    public function setAgo1wLow(?float $ago_1w_low): self
    {
        $this->ago_1w_low = $ago_1w_low;

        return $this;
    }

    public function getAgo1mChange(): ?float
    {
        return $this->ago_1m_change;
    }

    public function setAgo1mChange(?float $ago_1m_change): self
    {
        $this->ago_1m_change = $ago_1m_change;

        return $this;
    }

    public function getAgo1mHigh(): ?float
    {
        return $this->ago_1m_high;
    }

    public function setAgo1mHigh(?float $ago_1m_high): self
    {
        $this->ago_1m_high = $ago_1m_high;

        return $this;
    }

    public function getAgo1mLow(): ?float
    {
        return $this->ago_1m_low;
    }

    public function setAgo1mLow(?float $ago_1m_low): self
    {
        $this->ago_1m_low = $ago_1m_low;

        return $this;
    }

    public function getAgo3mChange(): ?float
    {
        return $this->ago_3m_change;
    }

    public function setAgo3mChange(?float $ago_3m_change): self
    {
        $this->ago_3m_change = $ago_3m_change;

        return $this;
    }

    public function getAgo3mHigh(): ?float
    {
        return $this->ago_3m_high;
    }

    public function setAgo3mHigh(?float $ago_3m_high): self
    {
        $this->ago_3m_high = $ago_3m_high;

        return $this;
    }

    public function getAgo3mLow(): ?float
    {
        return $this->ago_3m_low;
    }

    public function setAgo3mLow(?float $ago_3m_low): self
    {
        $this->ago_3m_low = $ago_3m_low;

        return $this;
    }

    public function getAgo6mChange(): ?float
    {
        return $this->ago_6m_change;
    }

    public function setAgo6mChange(?float $ago_6m_change): self
    {
        $this->ago_6m_change = $ago_6m_change;

        return $this;
    }

    public function getAgo6mHigh(): ?float
    {
        return $this->ago_6m_high;
    }

    public function setAgo6mHigh(?float $ago_6m_high): self
    {
        $this->ago_6m_high = $ago_6m_high;

        return $this;
    }

    public function getAgo6mLow(): ?float
    {
        return $this->ago_6m_low;
    }

    public function setAgo6mLow(?float $ago_6m_low): self
    {
        $this->ago_6m_low = $ago_6m_low;

        return $this;
    }

    public function getAgo1yChange(): ?float
    {
        return $this->ago_1y_change;
    }

    public function setAgo1yChange(?float $ago_1y_change): self
    {
        $this->ago_1y_change = $ago_1y_change;

        return $this;
    }

    public function getAgo1yHigh(): ?float
    {
        return $this->ago_1y_high;
    }

    public function setAgo1yHigh(?float $ago_1y_high): self
    {
        $this->ago_1y_high = $ago_1y_high;

        return $this;
    }

    public function getAgo1yLow(): ?float
    {
        return $this->ago_1y_low;
    }

    public function setAgo1yLow(?float $ago_1y_low): self
    {
        $this->ago_1y_low = $ago_1y_low;

        return $this;
    }
}
