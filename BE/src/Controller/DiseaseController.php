<?php

namespace App\Controller;

use App\Services\DiseaseService;
use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
class DiseaseController extends AbstractController
{

    public function __construct(private DiseaseService $diseaseService, 
                                private ValidatorInterface $validator,
                                private Security $security,
    ){}

    #[Route('/api/disease', name: 'add_diseasec', methods: ['POST'])]
    public function addDisease(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $constraints = new Assert\Collection([
            'name' => [new Assert\NotBlank(), new Assert\Length(['min' => 3])],
            'description' => [new Assert\Optional([new Assert\Length(['min' => 5])])],
            'category' => [new Assert\NotBlank()]

        ]);
        $violations = $this->validator->validate($data, $constraints);

        
        if (count($violations) > 0) {
            $errors = [];
            foreach ($violations as $violation) {
                $errors[] = $violation->getPropertyPath() . ': ' . $violation->getMessage();
            }
            return new JsonResponse(['errors' => $errors], 400);
        }

        $name = $data['name'];
        $description = $data['description'] ?? null;
        $category = $data['category'] ?? null;


            try {
                $disease = $this->diseaseService->addDisease($name,  $category,$description);

                return new JsonResponse('Disease added successfully: ' . $disease->getName(), 201);
            } catch (\Exception $e) {
                return new JsonResponse('Error: ' . $e->getMessage(), 400);
            }
    }

    #[Route(path: '/api/disease/{name}', name: 'delete_diseasec', methods: ['DELETE'])]
    public function deleteDisease(string $name): JsonResponse
    {
        try {
            $this->diseaseService->deleteDdisease($name);

            return new JsonResponse(['status' => 'Disease deleted successfully'], 200);

        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }

    #[Route('/api/disease/{name}', name: 'find_disease', methods: ['GET'])]
    public function findDisease(string $name): JsonResponse
    {
        $doctor = $this->getUser();

        if (!$doctor instanceof User) {
            return new JsonResponse(['error' => 'Missing doctor'], 400);
        }

        $role = $doctor->getRoles();

        if (!in_array('doctor', $role)) {
            return new JsonResponse(['error' => 'Unauthorized - Only doctors can search disease'], 403);
        }
        try {
            $disease = $this->diseaseService->findDisease($name);

            if (!$disease) {
                return new JsonResponse(['error' => 'Disease not found'], 404);
            }

            $serializedDisease = $this->diseaseService->serializePatient($disease);

            return new JsonResponse(json_decode($serializedDisease), 200);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }

}