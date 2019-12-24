<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TruckScheduleRepository")
 */
class TruckSchedule
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Truck", inversedBy="truckSchedules")
     * @ORM\JoinColumn(nullable=false)
     */
    private $truck;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Driver", inversedBy="truckSchedules")
     * @ORM\JoinColumn(nullable=false)
     */
    private $driver;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\DriverAssistant", inversedBy="truckSchedules")
     * @ORM\JoinColumn(nullable=false)
     */
    private $driver_assistant;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Route", inversedBy="truckSchedules")
     * @ORM\JoinColumn(nullable=false)
     */
    private $route;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $start_time;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $end_time;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $status;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\TruckOrder", mappedBy="truck_schedule")
     */
    private $truckOrders;

    public function __construct()
    {
        $this->truckOrders = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTruck(): ?Truck
    {
        return $this->truck;
    }

    public function setTruck(?Truck $truck): self
    {
        $this->truck = $truck;

        return $this;
    }

    public function getDriver(): ?Driver
    {
        return $this->driver;
    }

    public function setDriver(?Driver $driver): self
    {
        $this->driver = $driver;

        return $this;
    }

    public function getDriverAssistant(): ?DriverAssistant
    {
        return $this->driver_assistant;
    }

    public function setDriverAssistant(?DriverAssistant $driver_assistant): self
    {
        $this->driver_assistant = $driver_assistant;

        return $this;
    }

    public function getRoute(): ?Route
    {
        return $this->route;
    }

    public function setRoute(?Route $route): self
    {
        $this->route = $route;

        return $this;
    }

    public function getStartTime(): ?\DateTimeInterface
    {
        return $this->start_time;
    }

    public function setStartTime(?\DateTimeInterface $start_time): self
    {
        $this->start_time = $start_time;

        return $this;
    }

    public function getEndTime(): ?\DateTimeInterface
    {
        return $this->end_time;
    }

    public function setEndTime(?\DateTimeInterface $end_time): self
    {
        $this->end_time = $end_time;

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

    /**
     * @return Collection|TruckOrder[]
     */
    public function getTruckOrders(): Collection
    {
        return $this->truckOrders;
    }

    public function addTruckOrder(TruckOrder $truckOrder): self
    {
        if (!$this->truckOrders->contains($truckOrder)) {
            $this->truckOrders[] = $truckOrder;
            $truckOrder->setTruckSchedule($this);
        }

        return $this;
    }

    public function removeTruckOrder(TruckOrder $truckOrder): self
    {
        if ($this->truckOrders->contains($truckOrder)) {
            $this->truckOrders->removeElement($truckOrder);
            // set the owning side to null (unless already changed)
            if ($truckOrder->getTruckSchedule() === $this) {
                $truckOrder->setTruckSchedule(null);
            }
        }

        return $this;
    }
}
