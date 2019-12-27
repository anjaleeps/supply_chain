<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\StoreRepository")
 */
class Store
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $city;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Route", mappedBy="store")
     */
    private $routes;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Truck", mappedBy="store")
     */
    private $trucks;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Driver", mappedBy="store")
     */
    private $drivers;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\DriverAssistant", mappedBy="store")
     */
    private $driverAssistants;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\StoreManager", mappedBy="store")
     */
    private $storeManagers;

    public function __construct()
    {
        $this->routes = new ArrayCollection();
        $this->trucks = new ArrayCollection();
        $this->drivers = new ArrayCollection();
        $this->driverAssistants = new ArrayCollection();
        $this->storeManagers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * @return Collection|Route[]
     */
    public function getRoutes(): Collection
    {
        return $this->routes;
    }

    public function addRoute(Route $route): self
    {
        if (!$this->routes->contains($route)) {
            $this->routes[] = $route;
            $route->setStore($this);
        }

        return $this;
    }

    public function removeRoute(Route $route): self
    {
        if ($this->routes->contains($route)) {
            $this->routes->removeElement($route);
            // set the owning side to null (unless already changed)
            if ($route->getStore() === $this) {
                $route->setStore(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Truck[]
     */
    public function getTrucks(): Collection
    {
        return $this->trucks;
    }

    public function addTruck(Truck $truck): self
    {
        if (!$this->trucks->contains($truck)) {
            $this->trucks[] = $truck;
            $truck->setStore($this);
        }

        return $this;
    }

    public function removeTruck(Truck $truck): self
    {
        if ($this->trucks->contains($truck)) {
            $this->trucks->removeElement($truck);
            // set the owning side to null (unless already changed)
            if ($truck->getStore() === $this) {
                $truck->setStore(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Driver[]
     */
    public function getDrivers(): Collection
    {
        return $this->drivers;
    }

    public function addDriver(Driver $driver): self
    {
        if (!$this->drivers->contains($driver)) {
            $this->drivers[] = $driver;
            $driver->setStore($this);
        }

        return $this;
    }

    public function removeDriver(Driver $driver): self
    {
        if ($this->drivers->contains($driver)) {
            $this->drivers->removeElement($driver);
            // set the owning side to null (unless already changed)
            if ($driver->getStore() === $this) {
                $driver->setStore(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|DriverAssistant[]
     */
    public function getDriverAssistants(): Collection
    {
        return $this->driverAssistants;
    }

    public function addDriverAssistant(DriverAssistant $driverAssistant): self
    {
        if (!$this->driverAssistants->contains($driverAssistant)) {
            $this->driverAssistants[] = $driverAssistant;
            $driverAssistant->setStore($this);
        }

        return $this;
    }

    public function removeDriverAssistant(DriverAssistant $driverAssistant): self
    {
        if ($this->driverAssistants->contains($driverAssistant)) {
            $this->driverAssistants->removeElement($driverAssistant);
            // set the owning side to null (unless already changed)
            if ($driverAssistant->getStore() === $this) {
                $driverAssistant->setStore(null);
            }
        }

        return $this;
    }
    public function __toString()
    {
        return $this-> city;

    }

    /**
     * @return Collection|StoreManager[]
     */
    public function getStoreManagers(): Collection
    {
        return $this->storeManagers;
    }

    public function addStoreManager(StoreManager $storeManager): self
    {
        if (!$this->storeManagers->contains($storeManager)) {
            $this->storeManagers[] = $storeManager;
            $storeManager->setStore($this);
        }

        return $this;
    }

    public function removeStoreManager(StoreManager $storeManager): self
    {
        if ($this->storeManagers->contains($storeManager)) {
            $this->storeManagers->removeElement($storeManager);
            // set the owning side to null (unless already changed)
            if ($storeManager->getStore() === $this) {
                $storeManager->setStore(null);
            }
        }

        return $this;
    }
}
