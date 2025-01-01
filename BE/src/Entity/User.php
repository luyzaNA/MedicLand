<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity]
#[ORM\Table(name: "users", uniqueConstraints: [
    new ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_USER_EMAIL', columns: ['email']),
    new ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_USER_CNP', columns: ['cnp'])
])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\Column(length: 255, nullable: false, unique: true)]
    private string $email;

    #[ORM\Column(length: 255, nullable: false)]
    private string $password;

    #[ORM\Column(length: 50, nullable: false)]
    private string $role;

    #[ORM\Column(length: 50, nullable: true)] 
    private ?string $cnp = null;

    #[ORM\Column(length: 50, nullable: true)] 
    private ?string $firstName = null;

    #[ORM\Column(length: 50, nullable: true)] 
    private ?string $lastName = null;

    #[ORM\ManyToOne(targetEntity: Specialization::class)]
    #[ORM\JoinColumn(name: "specialization_name", referencedColumnName: "name", nullable: true)] 
    private ?Specialization $specialization = null;

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;
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

    public function getCnp(): ?string
    {
        return $this->cnp;
    }

    public function setCnp(?string $cnp): static
    {
        $this->cnp = $cnp;
        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): static
    {
        $this->firstName = $firstName;
        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): static
    {
        $this->lastName = $lastName;
        return $this;
    }

    public function getSpecialization(): ?Specialization
    {
        return $this->specialization;
    }

    public function setSpecialization(?Specialization $specialization): static
    {
        $this->specialization = $specialization;
        return $this;
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function eraseCredentials(): void
    {
    }

    public function getRoles(): array
    {
        return [$this->role]; 
    }
}
