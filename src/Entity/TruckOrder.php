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
     * @ORM\OneToOne(targetEntity="App\Entity\Orders", inversedBy="truck_schedule", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $orders; //defines a single order related to a certain schedule id

    /**
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity="App\Entity\TruckSchedule", inversedBy="truckOrders")
     * @ORM\JoinColumn(nullable=false)
     */
    private $truck_schedule;

   
    public function getOrders(): ?Orders
    {
        return $this->orders;
    }

    public function setOrders(orders $orders): self
    {
        $this->orders = $orders;

        return $this;
    }

    public function getTruckSchedule(): ?TruckSchedule
    {
        return $this->truck_schedule;
    }

    public function setTruckSchedule(?TruckSchedule $truck_schedule): self
    {
        $this->truck_schedule = $truck_schedule;

        return $this;
    }
//    public function __toString()
//    {
//        return $this-> city;
//
//    }
}
