<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RouteRepository")
 */
class Route
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=500)
     */
    private $decription;

    /**
     * @ORM\Column(type="time")
     */
    private $max_time;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Store", inversedBy="routes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $store;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Orders", mappedBy="route")
     */
    private $orders;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\TruckSchedule", mappedBy="route")
     */
    private $truckSchedules;

    public function __construct()
    {
        $this->orders = new ArrayCollection();
        $this->truckSchedules = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDecription(): ?string
    {
        return $this->decription;
    }

    public function setDecription(string $decription): self
    {
        $this->decription = $decription;

        return $this;
    }

    public function getMaxTime(): ?\DateTimeInterface
    {
        return $this->max_time;
    }

    public function setMaxTime(\DateTimeInterface $max_time): self
    {
        $this->max_time = $max_time;

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

    /**
     * @return Collection|Orders[]
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(Orders $order): self
    {
        if (!$this->orders->contains($order)) {
            $this->orders[] = $order;
            $order->setRoute($this);
        }

        return $this;
    }

    public function removeOrder(Orders $order): self
    {
        if ($this->orders->contains($order)) {
            $this->orders->removeElement($order);
            // set the owning side to null (unless already changed)
            if ($order->getRoute() === $this) {
                $order->setRoute(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|TruckSchedule[]
     */
    public function getTruckSchedules(): Collection
    {
        return $this->truckSchedules;
    }

    public function addTruckSchedule(TruckSchedule $truckSchedule): self
    {
        if (!$this->truckSchedules->contains($truckSchedule)) {
            $this->truckSchedules[] = $truckSchedule;
            $truckSchedule->setRoute($this);
        }

        return $this;
    }

    public function removeTruckSchedule(TruckSchedule $truckSchedule): self
    {
        if ($this->truckSchedules->contains($truckSchedule)) {
            $this->truckSchedules->removeElement($truckSchedule);
            // set the owning side to null (unless already changed)
            if ($truckSchedule->getRoute() === $this) {
                $truckSchedule->setRoute(null);
            }
        }

        return $this;
    }
}
