<?php

namespace App\Controller;

use App\Services\SpecializationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;

class SpecializationController extends AbstractController
{
    public function __construct(
        private Security $security,
        private SerializerInterface $serializerInterface,
        private SpecializationService $specializationService
    ) {}


    #[Route('/api/specialization', name: 'api_add_specialization', methods: ['POST'])]
    public function addSpecialization(Request $request): JsonResponse
    {
        $authUser = $this->security->getUser();

        $roles = $authUser->getRoles();

        if (!in_array('admin', $roles)) {
            return new JsonResponse(['error' => 'Unauthorized - Only Admins can add specializations'], 403);
        }

        $data = json_decode($request->getContent(), true);

        try {
            $name = $data['name'] ?? null;

            $specialization = $this->specializationService->addSpecialization($name);
            return new JsonResponse([
                'name' => $specialization->getName()
            ], 201);        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }

    #[Route('/api/specialization/{name}', name: 'api_delete_specialization', methods: ['DELETE'])]
    public function deleteSpecialization(string $name): JsonResponse
    {
        $authUser = $this->security->getUser();

        $roles = $authUser->getRoles();

        if (!in_array('admin', $roles)) {
            return new JsonResponse(['error' => 'Unauthorized - Only Admins can add specializations'], 403);
        }

        try {
            $this->specializationService->deleteSpecialization($name);

            return new JsonResponse(['status' => 'Specialization deleted successfully'], 200);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }

}
