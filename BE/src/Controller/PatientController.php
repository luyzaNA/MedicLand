<?php

namespace App\Controller;

use App\Services\PatientService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;

class PatientController extends AbstractController
{

    public function __construct(private PatientService $patientService, private SerializerInterface $serializerInterface)
    {
    }

    #[Route('/patients', name: 'get_all_patients', methods: ['GET'])]
    public function getAllPatients(): JsonResponse
    {
        $patients = $this->patientService->getAllPatients();

        $patientsJson = $this->patientService->serializePatients($patients);

        return new JsonResponse($patientsJson, 200, [], true);
    }

    #[Route('/patients/{cnp}', name: 'get_patient_by_cnp', methods: ['GET'])]
    public function getPatientByCnp(string $cnp): JsonResponse
    {
        $patient = $this->patientService->getPatientByCnp($cnp);
        if (!$patient) {
            return $this->json(['message' => 'Patient not found'], 404);
        }

        // Serialize the single patient
        $patientJson = $this->patientService->serializePatient($patient);

        return new JsonResponse($patientJson, 200, [], true);
    }

    #[Route('/patients', name: 'add_patient', methods: ['POST'])]
    public function addPatient(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $patient = $this->patientService->addPatient($data);

        $patientJson = $this->patientService->serializePatient($patient);

        return new JsonResponse($patientJson, 200, [], true);
    }

    #[Route('/patients/{cnp}', name: 'update_patient', methods: ['PUT'])]
    public function updatePatient(string $cnp, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $patient = $this->patientService->updatePatient($cnp, $data);
        if (!$patient) {
            return $this->json(['message' => 'Patient not found'], 404);
        }
        $patientJson = $this->patientService->serializePatient($patient);

        return new JsonResponse($patientJson, 200, [], true);
    }

    #[Route('/patients/{cnp}', name: 'delete_patient', methods: ['DELETE'])]
    public function deletePatient(string $cnp): JsonResponse
    {
        $deleted = $this->patientService->deletePatient($cnp);
        if (!$deleted) {
            return $this->json(['message' => 'Patient not found'], 404);
        }
        return $this->json(['message' => 'Patient deleted']);
    }

}
