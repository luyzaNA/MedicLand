<?php

namespace App\Services;

use App\Entity\Patient;
use App\Entity\Disease;
use App\Entity\DiseaseCategory;
use App\Repository\ConsultationRepository;
use App\Repository\DiseaseRepository;
use App\Repository\PatientRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;

class PatientService
{

    public function __construct(private PatientRepository $patientRepository, 
                                private DiseaseRepository $diseaseRepository, 
                                private DiseaseService $diseaseService,
                                private UserRepository $userRepository,
                                private EntityManagerInterface $entityManager,
                                private SerializerInterface $serializer,
                                private ConsultationRepository $consultationRepository)
    {}

    public function serializePatient(Patient $patient): string
    {
        return $this->serializer->serialize($patient, 'json');
    }

    public function serializePatients(array $patients): string
    {
        return $this->serializer->serialize($patients, 'json');
    }

    public function addPatient(Patient $patient): array
    {
        $cnp = $patient->getCnp();
    
        if (!$this->validateCnp($cnp)) {
            throw new \Exception('Invalid CNP');
        }
    
        $birthDate = $this->extractBirthDateFromCnp($cnp);
        $age = $this->calculateAge($birthDate);
        $sex = $this->extractSexFromCnp($cnp);
    
        $patient->setBirthDate($birthDate);
        $patient->setAge($age);
        $patient->setSex($sex);
    
        $existingPatient = $this->patientRepository->findByCnp($cnp);
        if ($existingPatient) {
            throw new \Exception('Patient with this CNP already exists');
        }
    
        $this->patientRepository->add($patient);
        return [
            'status' => 'Account created successfully.',
        ];
    }
  
    private function validateCnp(string $cnp): bool
    {
        if (strlen($cnp) !== 13 || !ctype_digit($cnp)) {
            return false;
        }
    
        $weights = [2, 7, 9, 1, 4, 6, 3, 5, 8, 2, 7, 9];
        $controlDigit = (int)substr($cnp, -1);
    
        $sum = 0;
        for ($i = 0; $i < 12; $i++) {
            $sum += (int)$cnp[$i] * $weights[$i];
        }
    
        $calculatedControl = $sum % 11;
        if ($calculatedControl === 10) {
            $calculatedControl = 1;
        }
    
        return $controlDigit === $calculatedControl;
    }
    

    private function extractBirthDateFromCnp(string $cnp): \DateTime
    {
        $yearPrefix = match ($cnp[0]) {
            '1', '2' => '19',
            '3', '4' => '18',
            '5', '6' => '20',
            default => throw new \Exception('Invalid CNP'),
        };
    
        $year = (int)($yearPrefix . substr($cnp, 1, 2));
        $month = (int)substr($cnp, 3, 2);
        $day = (int)substr($cnp, 5, 2);
    
        return new \DateTime("$year-$month-$day");
    }

    private function calculateAge(\DateTime $birthDate): int
    {
        $today = new \DateTime();
        $age = $today->diff($birthDate)->y;
        return $age;
    }

    private function extractSexFromCnp(string $cnp): string
    {
        return in_array($cnp[0], ['1', '3', '5', '7']) ? 'M' : 'F';
    }
    
    public function updatePatient(string $cnp, array $data): ?Patient
    {
        $patient = $this->patientRepository->findByCnp($cnp);
        if (!$patient) {
            return null;
        }
    
        $patient->setFirstName($data['firstName'] ?? $patient->getFirstName());
        $patient->setEmail($data['email'] ?? $patient->getEmail());
        $patient->setAge($data['age'] ?? $patient->getAge());
        $patient->setPhone($data['phone'] ?? $patient->getPhone());
        $patient->setLocality($data['locality'] ?? $patient->getLocality());
        $patient->setAddress($data['address'] ?? $patient->getAddress());
        $patient->setWeight($data['weight'] ?? $patient->getWeight());
        $patient->setHeight($data['height'] ?? $patient->getHeight());
    
        $existingAllergies = $patient->getAllergies();
        $newAllergy = $data['allergies'] ?? null;
    
        if ($newAllergy && !in_array($newAllergy, explode(',', $existingAllergies))) {
            $existingAllergies .= ($newAllergy ? ',' . $newAllergy : '');
        }
    
        $uniqueAllergies = array_unique(explode(',', $existingAllergies));
        $patient->setAllergies(implode(',', $uniqueAllergies));
    
        $patient->setOccupation($data['occupation'] ?? $patient->getOccupation());
    
        if (isset($data['diseases']) && is_array($data['diseases'])) {
            foreach ($data['diseases'] as $diseaseData) {
                $disease = $this->diseaseRepository->findDisease($diseaseData['name']);
    
                if (!$disease) {
                    $disease = new Disease();
                    $disease->setName($diseaseData['name'] ?? '');
                    $disease->setDescription($diseaseData['description'] ?? '');
    
                    $category = DiseaseCategory::tryFrom($diseaseData['category']);
                    if (!$category || !in_array($category, DiseaseCategory::cases())) {
                        $category = DiseaseCategory::OTHER; 
                    }
                    $disease->setCategory($category);
    
                    $this->diseaseRepository->save($disease);
                }
    
                $patient->addMedicalHistory($disease);
            }
        }
    
        $this->patientRepository->add($patient);
        return $patient;
    }
    
    public function deletePatient(string $cnp): void
    {
        $patient = $this->patientRepository->findByCnp($cnp);

        if (!$patient) {
            throw new \Exception('Patient not found');
        }

        foreach ($patient->getMedicalHistory() as $disease) {
            $patient->removeMedicalHistory($disease); 
        }
        $this->patientRepository->delete($patient);
    }

    public function getPatientByCnp(string $cnp): ?Patient
    {
        return $this->patientRepository->findByCnp($cnp);
    }

    public function getAllPatients(): array
    {
        return $this->patientRepository->findAllPatients();
    }

    public function getPatientsByDoctorEmail(string $doctorEmail): array
    {
        return $this->consultationRepository->getPatientsFromDoctor($doctorEmail);
    }

    public function findPatientsByDoctorAndDisease(string $doctorEmail, string $diseaseName): array
    {
        return $this->consultationRepository->findPatientsByDoctorAndDisease($doctorEmail, $diseaseName);
    }

     public function countPatientsBySpecialization(): array
    {
        return $this->consultationRepository->countPatientsBySpecialization();
    }

    public function countPatientsByChronicDisease(): array
    {
        return $this->consultationRepository->countPatientsByChronicDisease();
    }
}
