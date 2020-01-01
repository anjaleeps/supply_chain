<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CustomerRepository")
 */
class Customer implements UserInterface
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
     * @ORM\Column(type="string", length=50)
     */
    private $customer_type;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $place_no;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $street;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $city;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Orders", mappedBy="customer")
     */
    private $orders;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PhoneNumber", mappedBy="customer")
     */
    private $phoneNumbers;

    /**
     * @Assert\NotBlank()
     */
    private $phoneNumber;

    public function __construct()
    {
        $this->orders = new ArrayCollection();
        $this->phoneNumbers = new ArrayCollection();
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

    public function getFullName(): ?string
    {
        return $this->first_name.' '.$this->last_name;
    }

    public function setLastName(string $last_name): self
    {
        $this->last_name = $last_name;

        return $this;
    }

    public function getCustomerType(): ?string
    {
        return $this->customer_type;
    }

    public function setCustomerType(string $customer_type): self
    {
        $this->customer_type = $customer_type;

        return $this;
    }

    public function getPlaceNo(): ?string
    {
        return $this->place_no;
    }

    public function setPlaceNo(string $place_no): self
    {
        $this->place_no = $place_no;

        return $this;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(string $street): self
    {
        $this->street = $street;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->getPlaceNo().', '.$this->getStreet().', '.$this->getCity();
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }
    public function getPhoneNumbersAsString(): ?string{

        $phoneNumberList=array();
        foreach ($this->getPhoneNumbers() as $phoneNumber){
            $phoneNumberList[] = $phoneNumber->getPhoneNumber();
        }

        $phoneNumberAsString= implode( ' / ', $phoneNumberList);
        return $phoneNumberAsString;
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
            $order->setCustomer($this);
        }

        return $this;
    }

    public function removeOrder(Orders $order): self
    {
        if ($this->orders->contains($order)) {
            $this->orders->removeElement($order);
            // set the owning side to null (unless already changed)
            if ($order->getCustomer() === $this) {
                $order->setCustomer(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|PhoneNumber[]
     */
    public function getPhoneNumbers(): Collection
    {
        return $this->phoneNumbers;
    }

    public function addPhoneNumber(PhoneNumber $phoneNumber): self
    {
        if (!$this->phoneNumbers->contains($phoneNumber)) {
            $this->phoneNumbers[] = $phoneNumber;
            $phoneNumber->setCustomer($this);
        }

        return $this;
    }

    public function removePhoneNumber(PhoneNumber $phoneNumber): self
    {
        if ($this->phoneNumbers->contains($phoneNumber)) {
            $this->phoneNumbers->removeElement($phoneNumber);
            // set the owning side to null (unless already changed)
            if ($phoneNumber->getCustomer() === $this) {
                $phoneNumber->setCustomer(null);
            }
        }

        return $this;
    }

    public function setPhoneNumber(string $phoneNumber){
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getPhoneNumber( ){
        return $this->phoneNumber;
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata){

        $metadata->addPropertyConstraint('email', new Assert\Email([
            'message' => 'The email "{{ value }}" is not a valid email.',
        ]));

        $metadata->addPropertyConstraint('first_name', new Assert\NotBlank());
        $metadata->addPropertyConstraint('last_name',  new Assert\NotBlank());
        $metadata->addPropertyConstraint('place_no',  new Assert\NotBlank());
        $metadata->addPropertyConstraint('street',  new Assert\NotBlank());
        
        $metadata->addPropertyConstraint('customer_type', new Assert\Choice([
            'choices' => ['Wholesaler', 'Retailer', 'End Customer'],
            'message' => 'Choose a valid customer type.',
        ]));

        $metadata->addPropertyConstraint('city', new Assert\Choice([
            'choices' => ['Colombo', 'Negombo', 'Galle', 'Jaffna', 'Matara', 'Trincomalee'],
            'message' => 'Choose a valid city.',
        ]));

        $metadata->addPropertyConstraint('plainPassword', new Assert\Length([
            'min' => 8,
            'max' => 20,    
            'minMessage' => 'Password should be at least 8 characters long',
            'maxMessage' => 'Password cannot be longer than 20 characters'
        ]));
        
        $metadata->addPropertyConstraint('phoneNumber', new Assert\Length([
            'min' => 10,
            'max' => 10,    
            'exactMessage' => 'Incorrect format'
        ]));

    }
    public function __toString()
    {
        return $this->first_name;
        return $this->last_name;
        return $this->email;
        return $this->city;
        return $this->street;

    }

    public function currentLoggedInUser()
    {



    }
}
