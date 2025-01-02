<?php

namespace App\Controller;

use App\Services\PatientService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PatientController extends AbstractController
{

    public function __construct(private PatientService $patientService,
                                private ValidatorInterface $validator
    ){}

    #[Route('/api/patients', name: 'get_all_patients', methods: ['GET'])]
    public function getAllPatients(): JsonResponse
    {
        $patients = $this->patientService->getAllPatients();

        $patientsJson = $this->patientService->serializePatients($patients);

        return new JsonResponse($patientsJson, 200, [], true);
    }

    #[Route('/api/patients/{cnp}', name: 'get_patient_by_cnp', methods: ['GET'])]
    public function getPatientByCnp(string $cnp): JsonResponse
    {
        $patient = $this->patientService->getPatientByCnp($cnp);
        if (!$patient) {
            return $this->json(['message' => 'Patient not found'], 404);
        }
        $patientJson = $this->patientService->serializePatient($patient);

        return new JsonResponse($patientJson, 200, [], true);
    }

    #[Route('/api/patients', name: 'add_patient', methods: ['POST'])]
    public function addPatient(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $constraints = new Assert\Collection([
            'cnp' => [
                new Assert\NotBlank(),
                new Assert\Length(['min' => 13, 'max' => 13]),
                new Assert\Regex('/^\d{13}$/'),
            ],
            'birthDate' => [
                new Assert\NotBlank(),
                new Assert\Date(),
                new Assert\LessThanOrEqual('today'),
            ],
            'email' => [
                new Assert\NotBlank(),
                new Assert\Email(),
            ],
            'phone' => [
                new Assert\NotBlank(),
                new Assert\Regex('/^\d{10}$/'),
            ],
            'age' => [
                new Assert\Optional(),
                new Assert\Range(['min' => 0, 'max' => 120]),
            ],
            'firstName' => [
                new Assert\NotBlank(),
                new Assert\Length(['min' => 1, 'max' => 50]),
                new Assert\Regex('/^[a-zA-Z\s]+$/'), 
            ],
            'lastName' => [
                new Assert\NotBlank(),
                new Assert\Length(['min' => 1, 'max' => 50]),
                new Assert\Regex('/^[a-zA-Z\s]+$/'), 
            ],
            'address' => [
                new Assert\NotBlank(),
                new Assert\Length(['min' => 1, 'max' => 255]),
            ],
        ]);

        $violations = $this->validator->validate($data, $constraints);

        if (count($violations) > 0) {
            $errors = [];
            foreach ($violations as $violation) {
                $errors[] = $violation->getPropertyPath() . ': ' . $violation->getMessage();
            }
            return new JsonResponse(['errors' => $errors], 400);
        }

        $birthDate = \DateTime::createFromFormat('Y-m-d', $data['birthDate']);
        $currentDate = new \DateTime();
        $calculatedAge = $currentDate->diff($birthDate)->y;

        if (isset($data['age']) && $data['age'] !== $calculatedAge) {
            return new JsonResponse(['error' => 'The provided age does not match the birthdate.'], 400);
        }

        try {
            $patient = $this->patientService->addPatient($data);
            $patientJson = $this->patientService->serializePatient($patient);

            return new JsonResponse($patientJson, 200, [], true);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'An error occurred: ' . $e->getMessage()], 400);
        }
    }


    #[Route('/api/patients/{cnp}', name: 'update_patient', methods: ['PUT'])]
    public function updatePatient(string $cnp, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $constraints = new Assert\Collection([
            'cnp' => [
                new Assert\NotBlank(),
                new Assert\Length(['min' => 13, 'max' => 13]),
                new Assert\Regex('/^\d{13}$/'),
            ],
            'birthDate' => [
                new Assert\NotBlank(),
                new Assert\Date(),
                new Assert\LessThanOrEqual('today'),
            ],
            'email' => [
                new Assert\NotBlank(),
                new Assert\Email(),
            ],
            'phone' => [
                new Assert\NotBlank(),
                new Assert\Regex('/^\d{10}$/'),
            ],
            'age' => [
                new Assert\Optional(),
                new Assert\Range(['min' => 0, 'max' => 120]),
            ],
            'firstName' => [
                new Assert\NotBlank(),
                new Assert\Length(['min' => 1, 'max' => 50]),
                new Assert\Regex('/^[a-zA-Z\s]+$/'),
            ],
            'lastName' => [
                new Assert\NotBlank(),
                new Assert\Length(['min' => 1, 'max' => 50]),
                new Assert\Regex('/^[a-zA-Z\s]+$/'), 
            ],
            'address' => [
                new Assert\NotBlank(),
                new Assert\Length(['min' => 1, 'max' => 255]),
            ],
        ]);

        $violations = $this->validator->validate($data, $constraints);

        if (count($violations) > 0) {
            $errors = [];
            foreach ($violations as $violation) {
                $errors[] = $violation->getPropertyPath() . ': ' . $violation->getMessage();
            }
            return new JsonResponse(['errors' => $errors], 400);
        }

        $birthDate = \DateTime::createFromFormat('Y-m-d', $data['birthDate']);
        $currentDate = new \DateTime();
        $calculatedAge = $currentDate->diff($birthDate)->y;

        if (isset($data['age']) && $data['age'] !== $calculatedAge) {
            return new JsonResponse(['error' => 'The provided age does not match the birthdate.'], 400);
        }
        try {
            $patient = $this->patientService->updatePatient($cnp,  $data);
            $patientJson = $this->patientService->serializePatient($patient);
            
            return new JsonResponse($patientJson, 200, [], true);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'An error occurred: ' . $e->getMessage()], 400);
        }
    }

    #[Route('/api/patients/{cnp}', name: 'delete_patient', methods: ['DELETE'])]
    public function deletePatient(string $cnp): JsonResponse
    {
        $deleted = $this->patientService->deletePatient($cnp);
        if (!$deleted) {
            return $this->json(['message' => 'Patient not found'], 404);
        }
        return $this->json(['message' => 'Patient deleted']);
    }

}
