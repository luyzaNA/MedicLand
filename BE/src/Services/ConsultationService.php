<?php

namespace App\Services;

use App\Entity\Consultation;
use App\Entity\User;
use App\Repository\ConsultationRepository;
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
        private PatientRepository $patientRepository
    ) {}

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
                "phone" => $consultation->getPatient()->getPhone()
            ],
            "date" => $consultation->getDate()->format('Y-m-d\TH:i:sP'),
            "medication" => $consultation->getMedication(),
        ];
    }

    

    public function addConsultation(string $patient_cnp, User $doctor, \DateTimeInterface $date, array $diseases = [], ?string $medication = null): Consultation
    {

        $patient = $this->patientRepository->findByCnp($patient_cnp);
    
        $consultation = new Consultation();
        $consultation->setPatient($patient)
                    ->setDoctor($doctor)
                    ->setDate($date)
                    ->setMedication($medication);

        foreach ($diseases as $disease) {
            $consultation->addDiagnostic($disease);
        }

        $this->consultationRepository->add($consultation);

        return $consultation;
    }
}