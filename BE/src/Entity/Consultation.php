<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity]
#[ORM\Table(name: "consultation")]
class Consultation
{
    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Patient::class)]
    #[ORM\JoinColumn(name: "patient_cnp", referencedColumnName: "cnp", nullable: false)]
    private ?Patient $patient = null;

    #[ORM\ManyToOne(targetEntity: User::class)] 
    #[ORM\JoinColumn(name: "doctor_email", referencedColumnName: "email", nullable: false)]
    private ?User $doctor = null;

    #[ORM\Column(type: "datetime")]
    #[Assert\Date]
    #[Assert\EqualTo(
        value: "today",
        message: "The birth date must be exactly today."
    )]
    private \DateTimeInterface $date;

    #[ORM\ManyToMany(targetEntity: Disease::class)]
    #[ORM\JoinTable(
        name: "consultation_disease",
        joinColumns: [new ORM\JoinColumn(name: "consultation_id", referencedColumnName: "id")],
        inverseJoinColumns: [new ORM\JoinColumn(name: "disease_name", referencedColumnName: "name")]
    )]
    private Collection $diagnostic;

    #[ORM\Column(type: "simple_array", nullable: true)]
    private array $medication = [];

    public function __construct()
    {
        $this->diagnostic = new ArrayCollection();
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;
        return $this;
    }

    public function getPatient(): ?Patient
    {
        return $this->patient;
    }

    public function setPatient(Patient $patient): static
    {
        $this->patient = $patient;
        return $this;
    }

    public function getDoctor(): ?User
    {
        return $this->doctor;
    }

    public function setDoctor(User $doctor): static
    {
        $this->doctor = $doctor;
        return $this;
    }

    public function getDiagnostic(): Collection
    {
        return $this->diagnostic;
    }

    public function addDiagnostic(Disease $disease): static
    {
        if (!$this->diagnostic->contains($disease)) {
            $this->diagnostic[] = $disease;
        }

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;
        return $this;
    }

    public function getMedication(): array
    {
        return $this->medication;
    }

    public function setMedication(array $medication): static
    {
        $this->medication = $medication;
        return $this;
    }
}
