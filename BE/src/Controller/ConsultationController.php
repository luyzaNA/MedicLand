<?php

namespace App\Controller;

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
            'patient' => [
                new Assert\NotBlank()
            ], 
            'diseases' => [
                new Assert\NotBlank()
            ],
            'medication' => [
          new Assert\Optional(),
                new Assert\Type('array'),
                new Assert\All([
                    new Assert\Type('string') 
                ])
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

        if (!isset($data['patient'])) {
            return new JsonResponse( ['error' => 'Missing patient'], 400);
        }

        $patient = $data['patient'];

        $date = new \DateTime(); 

        $diseases = $data['diseases'] ?? [];
        $medication = $data['medication'] ?? [];
        
        try {
            $consultation = $this->consultationService->addConsultation(
                $patient,
                $doctor,
                $date,
                $diseases,
                $medication
            );

            return new JsonResponse($this->consultationService->serializeConsultation($consultation));
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }
}