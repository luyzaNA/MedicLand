<?php

namespace App\Entity;

use App\Repository\PatientRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use phpDocumentor\Reflection\Types\Nullable;

#[ORM\Entity]
#[ORM\Table(name: "patient", uniqueConstraints: [
    new ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_PATIENT_EMAIL', columns: ['email']),
    new ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_PATIENT_CNP', columns: ['cnp'])
])]
class Patient
{
    #[ORM\Column(length: 100, nullable: true, unique: true)]
    private string $email;

    #[ORM\Id]
    #[ORM\Column(length: 13, nullable: false, unique: true)]
    private string $cnp;

    #[ORM\GeneratedValue]
    private int $nr;

    #[ORM\Column(length: 50, nullable: false)]
    private string $firstName;

    #[ORM\Column(length: 255, nullable: false)]
    private string $lastName;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: false)]
    private \DateTimeInterface $birthDate;

    #[ORM\Column(nullable: false)]
    private int $age;


    #[ORM\Column(length: 50, nullable: false)]
    private string $locality;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $address = null;  

    #[ORM\Column(length: 20, nullable: false)]
    private string $phone;

    #[ORM\Column(enumType: BloodGroup::class, nullable: true)]
    private ?BloodGroup $bloodGroup = null;

    #[ORM\Column(enumType: RhFactor::class, nullable: true)]
    private ?RhFactor $rh = null;

    #[ORM\Column(type: "float", nullable: false)]
    private ?float $weight = null;

    #[ORM\Column(type:  "float", nullable: false)]
    private ?float $height = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $allergies = null;

    #[ORM\Column(length: 100, nullable: false)]
    private ?string $occupation = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: false)]
    private \DateTimeInterface $recordDate;

    #[ORM\Column(length: 1, nullable: false)]
    private string $sex;


     #[ORM\ManyToMany(targetEntity: Disease::class, cascade: ["persist", "remove"])]
    #[ORM\JoinTable(
        name: "patient_disease",
        joinColumns: [new ORM\JoinColumn(name: "patient_cnp", referencedColumnName: "cnp")],
        inverseJoinColumns: [new ORM\JoinColumn(name: "disease_name", referencedColumnName: "name")]
    )]
    private Collection $medicalHistory;

    public function __construct()
    {
        $this->medicalHistory = new ArrayCollection();
    }

    public function getEmail(): string
    {
        return $this->email;
    }


  public function getMedicalHistory(): Collection
    {
        return $this->medicalHistory;
    }

    public function addMedicalHistory(Disease $disease): static
{
    if ($this->medicalHistory === null) {
        $this->medicalHistory = new ArrayCollection();
    }

    if (!$this->medicalHistory->contains($disease)) {
        $this->medicalHistory->add($disease);
    }

    return $this;
}

public function removeMedicalHistory(Disease $disease): static
{
    if ($this->medicalHistory->contains($disease)) {
        $this->medicalHistory->removeElement($disease);
    }

    return $this;
}


    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getCnp(): string
    {
        return $this->cnp;
    }

    public function setCnp(string $cnp): static
    {
        $this->cnp = $cnp;
        return $this;
    }

    public function getNr(): int
    {
        return $this->nr;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;
        return $this;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;
        return $this;
    }

    public function getBirthDate(): \DateTimeInterface
    {
        return $this->birthDate;
    }

    public function setBirthDate(\DateTimeInterface $birthDate): static
    {
        $this->birthDate = $birthDate;
        return $this;
    }

    public function getAge(): int
    {
        return $this->age;
    }

    public function setAge(int $age): static
    {
        $this->age = $age;
        return $this;
    }


    public function getLocality(): string
    {
        return $this->locality;
    }

    public function setLocality(string $locality): static
    {
        $this->locality = $locality;
        return $this;
    }

   
    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): static
    {
        $this->address = $address;
        return $this;
    }


    public function getPhone(): string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): static
    {
        $this->phone = $phone;
        return $this;
    }

    public function getBloodGroup(): ?BloodGroup
    {
        return $this->bloodGroup;
    }

    public function setBloodGroup(?BloodGroup $bloodGroup): static
    {
        $this->bloodGroup = $bloodGroup;
        return $this;
    }

    public function getRh(): ?RhFactor
    {
        return $this->rh;
    }

    public function setRh(?RhFactor $rh): static
    {
        $this->rh = $rh;
        return $this;
    }

    public function getWeight(): ?float
    {
        return $this->weight;
    }

    public function setWeight(?float $weight): static
    {
        $this->weight = $weight;
        return $this;
    }

    public function getHeight(): ?float
    {
        return $this->height;
    }

    public function setHeight(?float $height): static
    {
        $this->height = $height;
        return $this;
    }

    public function getAllergies(): ?string
    {
        return $this->allergies;
    }

    public function setAllergies(?string $allergies): static
    {
        $this->allergies = $allergies;
        return $this;
    }

    public function getOccupation(): ?string
    {
        return $this->occupation;
    }

    public function setOccupation(?string $occupation): static
    {
        $this->occupation = $occupation;
        return $this;
    }

     public function getRecordDate(): ?\DateTimeInterface
       {
           return $this->recordDate;
       }

       public function setRecordDate(\DateTimeInterface $recordDate): self
       {
           $this->recordDate = $recordDate;
           return $this;
       }


    public function getSex(): string
    {
        return $this->sex;
    }

    public function setSex(string $sex): static
    {
        $this->sex = $sex;
        return $this;
    }
}

enum BloodGroup: string
{
    case A = 'A';
    case B = 'B';
    case O = 'O';
    case AB = 'AB';
}

enum RhFactor: string
{
    case NEGATIVE = 'NEGATIV';
    case POSITIVE = 'POZITIV';
}
