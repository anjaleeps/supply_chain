<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TrainScheduleRepository")
 */
class TrainSchedule
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $destination;

    /**
     * @ORM\Column(type="decimal", precision=6, scale=3)
     */
    private $capacity;

    /**
     * @ORM\Column(type="time")
     */
    private $start_time;

    /**
     * @ORM\Column(type="time")
     */
    private $journey_time;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $day;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Transports", mappedBy="train_schedule")
     */
    private $scheduledTransports;

    public function __construct()
    {
        $this->scheduledTransports = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDestination(): ?string
    {
        return $this->destination;
    }

    public function setDestination(string $destination): self
    {
        $this->destination = $destination;

        return $this;
    }

    public function getCapacity(): ?string
    {
        return $this->capacity;
    }

    public function setCapacity(string $capacity): self
    {
        $this->capacity = $capacity;

        return $this;
    }

    public function getStartTime(): ?\DateTimeInterface
    {
        return $this->start_time;
    }

    public function setStartTime(\DateTimeInterface $start_time): self
    {
        $this->start_time = $start_time;

        return $this;
    }

    public function getJourneyTime(): ?\DateTimeInterface
    {
        return $this->journey_time;
    }

    public function setJourneyTime(\DateTimeInterface $journey_time): self
    {
        $this->journey_time = $journey_time;

        return $this;
    }

    public function getDay(): ?string
    {
        return $this->day;
    }

    public function setDay(string $day): self
    {
        $this->day = $day;

        return $this;
    }

    /**
     * @return Collection|Transports[]
     */
    public function getScheduledTransports(): Collection
    {
        return $this->scheduledTransports;
    }

    public function addScheduledTransport(Transports $scheduledTransport): self
    {
        if (!$this->scheduledTransports->contains($scheduledTransport)) {
            $this->scheduledTransports[] = $scheduledTransport;
            $scheduledTransport->setTrainSchedule($this);
        }

        return $this;
    }

    public function removeScheduledTransport(Transports $scheduledTransport): self
    {
        if ($this->scheduledTransports->contains($scheduledTransport)) {
            $this->scheduledTransports->removeElement($scheduledTransport);
            // set the owning side to null (unless already changed)
            if ($scheduledTransport->getTrainSchedule() === $this) {
                $scheduledTransport->setTrainSchedule(null);
            }
        }

        return $this;
    }
}
