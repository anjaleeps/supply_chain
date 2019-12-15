<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TruckOrderRepository")
 */
class TruckOrder
{

    /**
     * @ORM\Id()
     * @ORM\OneToOne(targetEntity="App\Entity\orders", inversedBy="truck_schedule", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $orders;

    /**
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity="App\Entity\truckSchedule", inversedBy="truckOrders")
     * @ORM\JoinColumn(nullable=false)
     */
    private $truck_schedule;

   
    public function getOrders(): ?orders
    {
        return $this->orders;
    }

    public function setOrders(orders $orders): self
    {
        $this->orders = $orders;

        return $this;
    }

    public function getTruckSchedule(): ?truckSchedule
    {
        return $this->truck_schedule;
    }

    public function setTruckSchedule(?truckSchedule $truck_schedule): self
    {
        $this->truck_schedule = $truck_schedule;

        return $this;
    }
}
