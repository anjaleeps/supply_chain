<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;


/**
 * @ORM\Entity(repositoryClass="App\Repository\DriverAssistantRepository")
 */
class DriverAssistant implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @Assert\NotBlank()
     * @Assert\Length(max=4096)
     */
    private $plainPassword;

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $first_name;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $last_name;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $work_hours;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Store", inversedBy="driverAssistants")
     * @ORM\JoinColumn(nullable=false)
     */
    private $store;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\TruckSchedule", mappedBy="driver_assistant")
     */
    private $truckSchedules;

    public function __construct()
    {
        $this->truckSchedules = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    public function setPlainPassword($password)
    {
        $this->plainPassword = $password;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstName(): ?string
    {
        return $this->first_name;
    }

    public function setFirstName(string $first_name): self
    {
        $this->first_name = $first_name;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->last_name;
    }

    public function setLastName(string $last_name): self
    {
        $this->last_name = $last_name;

        return $this;
    }

    public function getWorkHours(): ?\DateTimeInterface
    {
        return $this->work_hours;
    }

    public function setWorkHours(?\DateTimeInterface $work_hours): self
    {
        $this->work_hours = $work_hours;

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

    public function getStore(): ?store
    {
        return $this->store;
    }

    public function setStore(?store $store): self
    {
        $this->store = $store;

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
            $truckSchedule->setDriverAssistant($this);
        }

        return $this;
    }

    public function removeTruckSchedule(TruckSchedule $truckSchedule): self
    {
        if ($this->truckSchedules->contains($truckSchedule)) {
            $this->truckSchedules->removeElement($truckSchedule);
            // set the owning side to null (unless already changed)
            if ($truckSchedule->getDriverAssistant() === $this) {
                $truckSchedule->setDriverAssistant(null);
            }
        }

        return $this;
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata){

        $metadata->addPropertyConstraint('email', new Assert\Email([
            'message' => 'The email "{{ value }}" is not a valid email.',
        ]));

        $metadata->addPropertyConstraint('first_name', new Assert\NotBlank());
        $metadata->addPropertyConstraint('last_name',  new Assert\NotBlank());


        $metadata->addPropertyConstraint('plainPassword', new Assert\Length([
            'min' => 8,
            'max' => 20,    
            'minMessage' => 'Password should be at least 8 characters long',
            'maxMessage' => 'Password cannot be longer than 20 characters'
        ]));

    }

}
