<?php

namespace App\Entity;

use App\Repository\PatientRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PatientRepository::class)]
#[ORM\Table(name: "patient")]
class Patient
{
    #[ORM\Id]
    #[ORM\Column(length: 50)]
    private string $cnp ;

    #[ORM\Column(length: 50, nullable: false)]
    private string $firstName;

    #[ORM\Column(length: 255, nullable: false)]
    private string $lastName;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: false)]
    private \DateTimeInterface $birthDate;

    #[ORM\Column(nullable: false)]
    private int $age;

    #[ORM\Column(length: 255, nullable: false)]
    private string $address;

    #[ORM\Column(length: 255, nullable: false)]
    private string $email;

    #[ORM\Column(length: 255, nullable: false)]
    private string $phone;

    public function getCnp(): ?string
    {
        return $this->cnp;
    }

    public function setCnp(string $cnp): static
    {
        $this->cnp = $cnp;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getBirthDate(): ?\DateTimeInterface
    {
        return $this->birthDate;
    }

    public function setBirthDate(\DateTimeInterface $birthDate): static
    {
        $this->birthDate = $birthDate;

        return $this;
    }

    public function getAge(): ?int
    {
        return $this->age;
    }

    public function setAge(int $age): static
    {
        $this->age = $age;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }
}
