<?php

namespace App\Services;

use App\Entity\Consultation;
use App\Entity\DiseaseCategory;
use App\Entity\Disease;
use App\Entity\User;
use App\Repository\ConsultationRepository;
use App\Repository\DiseaseRepository;
use App\Repository\PatientRepository;
use App\Repository\SpecializationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;

class ConsultationService
{
    public function __construct(
        private SpecializationRepository $specializationRepository,
        private  EntityManagerInterface $entityManager,
        private SerializerInterface $serializer,
        private ConsultationRepository $consultationRepository,
        private PatientRepository $patientRepository,
        private DiseaseRepository $diseaseRepository,
) {}

public function serializeConsultations(array $consultations): string
{
    $formattedConsultations = array_map(function ($consultation) {
        return $this->serializeConsultation($consultation);
    }, $consultations);

    return json_encode($formattedConsultations, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
}


    public function serializeConsultation(Consultation $consultation): array
    {
        return [
            "id" => $consultation->getId(),
            "doctor" => [
                "firstName" => $consultation->getDoctor()->getFirstName(),
                "lastName" => $consultation->getDoctor()->getLastName(),
                "email" => $consultation->getDoctor()->getEmail(),
                "specialization" => [
                    "name" => $consultation->getDoctor()->getSpecialization()->getName()
                ]
            ],
            "patient" => [
                "cnp" => $consultation->getPatient()->getCnp(),
                "firstName" => $consultation->getPatient()->getFirstName(),
                "lastName" => $consultation->getPatient()->getLastName(),
                "birthDate" => $consultation->getPatient()->getBirthDate()->format('Y-m-d\TH:i:sP'),
                "age" => $consultation->getPatient()->getAge(),
                "address" => $consultation->getPatient()->getAddress(),
                "email" => $consultation->getPatient()->getEmail(),
                "phone" => $consultation->getPatient()->getPhone(),
                "locality" => $consultation->getPatient()->getLocality(),
                "bloodGroup" => $consultation->getPatient()->getBloodGroup() ? $consultation->getPatient()->getBloodGroup()->value : null,
                "rh" => $consultation->getPatient()->getRh() ? $consultation->getPatient()->getRh()->value : null,
                "weight" => $consultation->getPatient()->getWeight(),
                "height" => $consultation->getPatient()->getHeight(),
                "allergies" => $consultation->getPatient()->getAllergies(),
                "occupation" => $consultation->getPatient()->getOccupation(),
                "recordDate" => $consultation->getPatient()->getRecordDate() ? $consultation->getPatient()->getRecordDate()->format('Y-m-d\TH:i:sP') : null,
                "sex" => $consultation->getPatient()->getSex(),
                "diseases" => array_map(function($disease) {
                    return [
                        'name' => $disease->getName(),
                        'description' => $disease->getDescription(),
                        'category' => $disease->getCategory()->value,
                    ];
                }, $consultation->getPatient()->getMedicalHistory()->toArray())
            ],                
            "date" => $consultation->getDate()->format('Y-m-d\TH:i:sP'),
            "medication" => $consultation->getMedication(),
            "symptoms" => $consultation->getSymptoms(),
            "diagnostic" => array_map(function($disease) {
                return [
                    'name' => $disease->getName(),
                    'description' => $disease->getDescription(),
                    'category' => $disease->getCategory()->value, 
                ];
            }, $consultation->getDiagnostic()->toArray())
                    ];
    }

    public function addConsultation(string $patientCnp, User $doctor, array $diseases = [], ?string $medication = null, ?string $symptoms = null): Consultation
    {
        $patient = $this->patientRepository->findOneBy(['cnp' => $patientCnp]);
    
    $consultation = new Consultation();

    $consultation->setPatient($patient)
                 ->setDoctor($doctor)
                 ->setDate(new \DateTime()) 
                 ->setMedication($medication)
                 ->setSymptoms($symptoms);

    if (!empty($diseases)) {
    foreach ($diseases as $diseaseData) {
        $disease = $this->diseaseRepository->findOneBy(['name' => $diseaseData['name']]);

        if (!$disease) {
            $disease = new Disease();
            $disease->setName($diseaseData['name']);
            $disease->setDescription($diseaseData['description'] ?? '');

            $category = DiseaseCategory::tryFrom($diseaseData['category']);
            if (!$category || !in_array($category, DiseaseCategory::cases())) {
                $category = DiseaseCategory::OTHER; 
            }
            $disease->setCategory($category);
            $consultation->addDiagnostic($disease);
            $this->diseaseRepository->save($disease);
        }

        $patient->addMedicalHistory($disease);
    }
}

            
        $this->consultationRepository->add($consultation);

    return $consultation;
}
public function updateConsultation(int $consultationId, ?string $medication = null, ?string $symptoms = null, array $diseases = []): ?Consultation
{
    $consultation = $this->consultationRepository->findConsultation($consultationId);

    if (!$consultation) {
        return null; 
    }
    $patient = $this->consultationRepository->findPatientByConsultationId($consultationId);

    $existingMedication = $consultation->getMedication();
    $newMedication = $medication;

    if ($newMedication) {
        $existingMedications = $existingMedication ? explode(',', $existingMedication) : [];
        $newMedications = explode(',', $newMedication);

        $uniqueMedication = array_unique(array_merge($existingMedications, $newMedications));
        $consultation->setMedication(implode(',', $uniqueMedication));
    }

    $existingSymptoms = $consultation->getSymptoms();
    $newSymptoms = $symptoms;

    if ($newSymptoms) {
        $existingSymptomsArray = $existingSymptoms ? explode(',', $existingSymptoms) : [];
        $newSymptomsArray = explode(',', $newSymptoms);

        $uniqueSymptoms = array_unique(array_merge($existingSymptomsArray, $newSymptomsArray));
        $consultation->setSymptoms(implode(',', $uniqueSymptoms));
    }

    foreach ($diseases as $diseaseData) {
        $disease = $this->diseaseRepository->findOneBy(['name' => $diseaseData['name']]);

        if (!$disease) {
            $disease = new Disease();
            $disease->setName($diseaseData['name']);
            $disease->setDescription($diseaseData['description'] ?? '');

            $category = DiseaseCategory::tryFrom($diseaseData['category']);
            if (!$category || !in_array($category, DiseaseCategory::cases())) {
                $category = DiseaseCategory::OTHER; 
            }
            $disease->setCategory($category);

            $this->diseaseRepository->save($disease);
        }
        $consultation->addDiagnostic($disease);
        $patient->addMedicalHistory($disease);
    }

    $this->consultationRepository->add($consultation);

    return $consultation;
}

public function getConsultation(int $consultationId): ?Consultation
{
    return $this->consultationRepository->findConsultation($consultationId);
}


public function removeConsultation(int $consultationId): void
{
    $consultationToBeRemove= $this->consultationRepository->findConsultation($consultationId);

     $this->consultationRepository->remove($consultationToBeRemove);
}

public function getAllConsultations(): array
{
    return $this->consultationRepository->findAllConsultations();
}

public function getConsultationByPatient(string $patientCnp): array
{
    return $this->consultationRepository->findConsultationsByPatientCnp($patientCnp);
}



}