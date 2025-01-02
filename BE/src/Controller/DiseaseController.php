<?php

namespace App\Controller;

use App\Services\DiseaseService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;

class DiseaseController extends AbstractController
{

    public function __construct(private DiseaseService $diseaseService, private SerializerInterface $serializerInterface)
    {
    }

    #[Route('/disease', name: 'add_diseasec', methods: ['POST'])]
    public function addDisease(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (isset($data['name']) && !empty($data['name'])) {
            $name = $data['name'];
            $description = $data['description'] ?? null;

            try {
                $disease = $this->diseaseService->addDdisease($name, $description);
                return new JsonResponse('Disease added successfully: ' . $disease->getName(), 201);
            } catch (\Exception $e) {
                return new JsonResponse('Error: ' . $e->getMessage(), 400);
            }
        }

        return new JsonResponse('Invalid data provided', 400);
    }


    #[Route(path: '/disease/{name}', name: 'delete_diseasec', methods: ['DELETE'])]
    public function deleteDisease(string $name): JsonResponse
    {
        try {
            $this->diseaseService->deleteDdisease($name);
                return new JsonResponse(['status' => 'Disease deleted successfully'], 200);

        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }
}