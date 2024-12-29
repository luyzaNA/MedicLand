<?php

namespace App\Entity;

use App\Repository\DoctorRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: DoctorRepository::class)]
#[ORM\Table(name: "doctor", uniqueConstraints: [
    new ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_USER_EMAIL', columns: ['email']),
    new ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_USER_CNP', columns: ['cnp'])
])]
class Doctor implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\Column(length: 50)]
    private string $cnp;

    #[ORM\Column(length: 50)]
    private string $firstName;

    #[ORM\Column(length: 255)]
    private string $lastName;

    #[ORM\Column(length: 180)]
    private string $email;

    #[ORM\Column(length: 255)]
    private string $specialization;

    #[ORM\Column(length: 50)]
    private string $role;

    #[ORM\Column]
    private string $password;
    
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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }
  
    public function getSpecialization(): ?string
    {
        return $this->specialization;
    }

    public function setSpecialization(string $specialization): static
    {
        $this->specialization = $specialization;
        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): static
    {
        $this->role = $role;
        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }
    
    public function getUserIdentifier(): string
    {
        return $this->email; 
    }
    public function getRoles(): array
    {
        return [$this->role];
    }
    public function eraseCredentials(): void
    {
    }

}
