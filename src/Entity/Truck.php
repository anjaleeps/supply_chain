<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TruckRepository")
 */
class Truck
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $truck_no;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Store", inversedBy="trucks")
     * @ORM\JoinColumn(nullable=false)
     */
    private $store;

    /**
     * @ORM\Column(type="time")
     */
    private $used_hours;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $status;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTruckNo(): ?string
    {
        return $this->truck_no;
    }

    public function setTruckNo(string $truck_no): self
    {
        $this->truck_no = $truck_no;

        return $this;
    }

    public function getStore(): ?Store
    {
        return $this->store;
    }

    public function setStore(?Store $store): self
    {
        $this->store = $store;

        return $this;
    }

    public function getUsedHours(): ?\DateTimeInterface
    {
        return $this->used_hours;
    }

    public function setUsedHours(\DateTimeInterface $used_hours): self
    {
        $this->used_hours = $used_hours;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }
}


