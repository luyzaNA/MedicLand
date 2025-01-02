<?php

namespace App\Services;

use App\Entity\Patient;
use App\Repository\PatientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;

class PatientService
{

    public function __construct(private PatientRepository $patientRepository, 
                                private  EntityManagerInterface $entityManager,
                                private SerializerInterface $serializer)
    {}

    public function serializePatient(Patient $patient): string
    {
        return $this->serializer->serialize($patient, 'json');
    }

    public function serializePatients(array $patients): string
    {
        return $this->serializer->serialize($patients, 'json');
    }
    
    public function addPatient(array $data): Patient
    {
        $patient = new Patient();
        $patient->setCnp($data['cnp']);
        $patient->setFirstName($data['firstName']);
        $patient->setLastName($data['lastName']);
        $patient->setBirthDate(new \DateTime($data['birthDate']));
        $patient->setAge($data['age']);
        $patient->setAddress($data['address']);
        $patient->setEmail($data['email']);
        $patient->setPhone($data['phone']);

        $this->patientRepository->add($patient);

        return $patient;
    }

    public function updatePatient(string $cnp, array $data): ?Patient
    {
        $patient = $this->patientRepository->findByCnp($cnp);
        if (!$patient) {
            return null;
        }

        $patient->setFirstName($data['firstName'] ?? $patient->getFirstName());
        $patient->setLastName($data['lastName'] ?? $patient->getLastName());
        $patient->setBirthDate(isset($data['birthDate']) ? new \DateTime($data['birthDate']) : $patient->getBirthDate());
        $patient->setAge($data['age'] ?? $patient->getAge());
        $patient->setAddress($data['address'] ?? $patient->getAddress());
        $patient->setEmail($data['email'] ?? $patient->getEmail());
        $patient->setPhone($data['phone'] ?? $patient->getPhone());

        $this->patientRepository->update();

        return $patient;
    }

    public function deletePatient(string $cnp): bool
    {
        $patient = $this->patientRepository->findByCnp($cnp);
        if (!$patient) {
            return false;
        }

        $this->patientRepository->delete($patient);

        return true;
    }

    public function getPatientByCnp(string $cnp): ?Patient
    {
        return $this->patientRepository->findByCnp($cnp);
    }

    public function getAllPatients(): array
    {
        return $this->patientRepository->findAllPatients();
    }
}
