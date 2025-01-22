<?php

namespace App\Controller;

use App\Entity\Consultation;
use App\Entity\User;
use App\Services\ConsultationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
class ConsultationController extends AbstractController
{

    public function __construct(private ConsultationService $consultationService, 
                                private Security $security,
                                private ValidatorInterface $validator
    ){}

    #[Route('/api/consultation', name: 'add_consultationcon', methods: ['POST'])]
    public function addConsultation(Request $request): JsonResponse
    {

        $doctor = $this->getUser();

        if(!$doctor  instanceof User)     
        {
            return new JsonResponse (['error' => 'Missing patient'], 400);

        }          

        $role = $doctor->getRoles();

        if (!in_array('doctor', $role)) {
            return new JsonResponse(['error' => 'Unauthorized - Only doctors can add consultation'], 403);
        }

        $data = json_decode( $request->getContent(), true);

        $constraints = new Assert\Collection([
            'patientCnp' => [
                new Assert\NotBlank()
            ], 
            'diseases' => [
                new Assert\Optional()
            ],
            'medication' => [
                new Assert\Optional(),
               
            ],
            'symptoms' => [
                new Assert\NotBlank()
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

        $patientCnp = $data['patientCnp'];
        $diseases = $data['diseases'] ?? [];
        $medication = $data['medication'] ?? '';
        $symptoms = $data['symptoms'] ??'';
        
        try {

            $consultation = $this->consultationService->addConsultation($patientCnp, $doctor, $diseases, $medication, $symptoms);


            return new JsonResponse($this->consultationService->serializeConsultation($consultation), 201);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }

    #[Route('/api/consultation/{id}', name: 'update_consultation', methods: ['PUT'])]
    #[Route('/api/consultation/{id}', name: 'update_consultation', methods: ['PUT'])]
    public function updateConsultation(int $id, Request $request): JsonResponse
    {
        $doctor = $this->getUser();
    
        if (!$doctor instanceof User) {
            return new JsonResponse(['error' => 'Missing doctor'], 400);
        }
    
        $role = $doctor->getRoles();
    
        if (!in_array('doctor', $role)) {
            return new JsonResponse(['error' => 'Unauthorized - Only doctors can update consultation'], 403);
        }
    
        $consultation = $this->consultationService->getConsultation($id);
    
        if (!$consultation) {
            return new JsonResponse(['error' => 'Consultation not found'], 404);
        }
    
        if ($consultation->getDoctor() !== $doctor) {
            return new JsonResponse(['error' => 'Unauthorized - You cannot update this consultation'], 403);
        }
    
        $data = json_decode($request->getContent(), true);
    
        $constraints = new Assert\Collection([
            'medication' => [new Assert\Optional()],
            'symptoms' => [new Assert\Optional()],
            'diseases' => [new Assert\Optional()],
        ]);
    
        $violations = $this->validator->validate($data, $constraints);
    
        if (count($violations) > 0) {
            $errors = [];
            foreach ($violations as $violation) {
                $errors[] = $violation->getPropertyPath() . ': ' . $violation->getMessage();
            }
            return new JsonResponse(['errors' => $errors], 400);
        }
    
        $medication = $data['medication'] ?? null;
        $symptoms = $data['symptoms'] ?? null;
        $diseases = $data['diseases'] ?? [];
    
        try {
            $updatedConsultation = $this->consultationService->updateConsultation($id, $medication, $symptoms, $diseases);
    
            return new JsonResponse($this->consultationService->serializeConsultation($updatedConsultation), 200);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }
    

#[Route('/api/consultation/{id}', name: 'get_consultation', methods: ['GET'])]
public function getConsultation(int $id): JsonResponse
{
    $doctor = $this->getUser();

    if (!$doctor instanceof User) {
        return new JsonResponse(['error' => 'Missing doctor'], 400);
    }

    $role = $doctor->getRoles();

    if (!in_array('doctor', $role)) {
        return new JsonResponse(['error' => 'Unauthorized - Only doctors can update consultation'], 403);
    }

    $consultation = $this->consultationService->getConsultation($id);
    if ($consultation->getDoctor() !== $doctor) {
        return new JsonResponse(['error' => 'Unauthorized - You cannot update this consultation'], 403);
    }
    if (!$consultation) {
        return new JsonResponse(['error' => 'Consultation not found'], 404);
    }

    return new JsonResponse($this->consultationService->serializeConsultation($consultation), 200);
}

#[Route('/api/consultation/{id}', name: 'delete_consultation', methods: ['DELETE'])]
public function deleteConsultation(int $id): JsonResponse
{
    $doctor = $this->getUser();

    if (!$doctor instanceof User) {
        return new JsonResponse(['error' => 'Missing doctor'], 400);
    }

    $role = $doctor->getRoles();

    if (!in_array('doctor', $role)) {
        return new JsonResponse(['error' => 'Unauthorized - Only doctors can delete consultation'], 403);
    }

    $consultation = $this->consultationService->getConsultation($id);

    if (!$consultation) {
        return new JsonResponse(['error' => 'Consultation not found'], 404);
    }

    if ($consultation->getDoctor() !== $doctor) {
        return new JsonResponse(['error' => 'Unauthorized - You cannot delete this consultation'], 403);
    }

    try {
        $this->consultationService->removeConsultation($id);
        return new JsonResponse(['success' => 'Consultation deleted successfully'], 200);
    } catch (\Exception $e) {
        return new JsonResponse(['error' => $e->getMessage()], 400);
    }
}


#[Route('/api/consultation', name: 'get_all_consultations', methods: ['GET'])]
public function getAllSpecializations(): JsonResponse
{
    $doctor = $this->getUser();

    if (!$doctor instanceof User) {
        return new JsonResponse(['error' => 'Missing doctor'], 400);
    }

    $role = $doctor->getRoles();

    if (!in_array('doctor', $role)) {
        return new JsonResponse(['error' => 'Unauthorized - Only doctors can view consultations'], 403);
    }

    try {
        $consultations = $this->consultationService->getAllConsultations();
        return new JsonResponse(
            $this->consultationService->serializeConsultations($consultations),
            200,
            ['Content-Type' => 'application/json'],
            true 
        );
    } catch (\Exception $e) {
        return new JsonResponse(['error' => $e->getMessage()], 400);
    }
}

#[Route('/api/consultations/patient/{patientCnp}', name: 'get_consultations_by_patient', methods: ['GET'])]
public function getConsultationsByPatient(string $patientCnp): JsonResponse
{
    $doctor = $this->getUser();

    if (!$doctor instanceof User) {
        return new JsonResponse(['error' => 'Missing doctor'], 400);
    }

    $role = $doctor->getRoles();

    if (!in_array('doctor', $role)) {
        return new JsonResponse(['error' => 'Unauthorized - Only doctors can view consultations'], 403);
    }

    try {
        $consultations = $this->consultationService->getConsultationByPatient($patientCnp);
        return new JsonResponse(
            $this->consultationService->serializeConsultations($consultations),
            200,
            ['Content-Type' => 'application/json'],
            true
        );
    } catch (\Exception $e) {
        return new JsonResponse(['error' => $e->getMessage()], 400);
    }
}

}