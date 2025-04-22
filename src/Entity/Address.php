<?php

namespace App\Entity;

use App\Repository\AddressRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AddressRepository::class)]
class Address
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $Country = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $State = null;

    #[ORM\Column(length: 255)]
    private ?string $FirstName = null;

    #[ORM\Column(length: 255)]
    private ?string $LastName = null;

    #[ORM\Column(length: 255)]
    private ?string $Postal = null;

    #[ORM\Column(length: 255)]
    private ?string $AddressLine1 = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $AddressLine2 = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $AddressLine3 = null;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $AdditionalInformation = null;

    #[ORM\Column(length: 255)]
    private ?string $City = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCountry(): ?string
    {
        return $this->Country;
    }

    public function setCountry(string $Country): static
    {
        $this->Country = $Country;

        return $this;
    }

    public function getState(): ?string
    {
        return $this->State;
    }

    public function setState(?string $State): static
    {
        $this->State = $State;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->FirstName;
    }

    public function setFirstName(string $FirstName): static
    {
        $this->FirstName = $FirstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->LastName;
    }

    public function setLastName(string $LastName): static
    {
        $this->LastName = $LastName;

        return $this;
    }

    public function getPostal(): ?string
    {
        return $this->Postal;
    }

    public function setPostal(string $Postal): static
    {
        $this->Postal = $Postal;

        return $this;
    }

    public function getAddressLine1(): ?string
    {
        return $this->AddressLine1;
    }

    public function setAddressLine1(string $AddressLine1): static
    {
        $this->AddressLine1 = $AddressLine1;

        return $this;
    }

    public function getAddressLine2(): ?string
    {
        return $this->AddressLine2;
    }

    public function setAddressLine2(string $AddressLine2): static
    {
        $this->AddressLine2 = $AddressLine2;

        return $this;
    }

    public function getAddressLine3(): ?string
    {
        return $this->AddressLine3;
    }

    public function setAddressLine3(string $AddressLine3): static
    {
        $this->AddressLine3 = $AddressLine3;

        return $this;
    }

    public function getAdditionalInformation(): ?string
    {
        return $this->AdditionalInformation;
    }

    public function setAdditionalInformation(?string $AdditionalInformation): static
    {
        $this->AdditionalInformation = $AdditionalInformation;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->City;
    }

    public function setCity(string $City): static
    {
        $this->City = $City;

        return $this;
    }
}
