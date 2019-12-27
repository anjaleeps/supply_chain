<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity(repositoryClass="App\Repository\TransportsRepository")
 */
class Transports
{

    /**
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity="App\Entity\TrainSchedule", inversedBy="scheduledTransports")
     * @ORM\JoinColumn(nullable=false)
     */
    private $train_schedule;

    /**
     * @ORM\Column(type="date")
     */
    private $date;

    /**
     * @ORM\Column(type="string")
     */
    private $status;
    /**
     * @ORM\Id()
     * @ORM\OneToOne(targetEntity="App\Entity\Orders", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $orders;


    public function getTrainSchedule(): ?TrainSchedule
    {
        return $this->train_schedule;
    }

    public function setTrainSchedule(?TrainSchedule $train_schedule): self
    {
        $this->train_schedule = $train_schedule;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus(string $status)
    {
        $this->status = $status;

        return $this;
    }

    public function getOrders(): ?Orders
    {
        return $this->orders;
    }

    public function setOrders(Orders $orders): self
    {
        $this->orders = $orders;

        return $this;
    }
}
