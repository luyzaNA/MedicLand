<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Services\RegisterService;
use App\Services\SpecializationService;
use Symfony\Component\HttpFoundation\Request;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use App\Entity\User;

class RegistrationController extends AbstractController
{

    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
        private RegisterService $registerService,
        private SpecializationService $specializationService,
        private JWTTokenManagerInterface $jwtManager,
        private Security $security       
) {}

    #[Route('/api/register', name: 'api_register', methods: ['POST'])]
    public function register(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        try {

            $specialization = null;
            if (isset($data['specialization'])) {
                $specialization = $this->specializationService->getSpecialization($data['specialization']);
                if (!$specialization) {
                    throw new \Exception('Specialization not found.');
                }
            }
            $response = $this->registerService->register(
                $data['email'],
                $data['cnp'] ?? null,
                $data['password'],
                $data['firstName'] ?? null,
                $data['lastName'] ?? null,
                $specialization ?? null,
                $data['role']
            );


            return new JsonResponse($response, 201);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }

    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function login(Request $request): JsonResponse
    {

        $data = json_decode($request->getContent(), true);

        $token = $this->registerService->login([
            'email' => $data['email'],
            'password' => $data['password']
        ]);


        if (!$token) {
            return new JsonResponse(['error' => 'Invalid credentials'], 401);
        }

        return new JsonResponse(['token' => $token], 200);
    }

    #[Route('/api/auth/user', name: 'api_get_auth_user', methods: ['GET'])]
    public function getAuthUserDetails(): JsonResponse
    {

        $user = $this->getUser();        
        if (!$user instanceof User) {
            return new JsonResponse(['error' => 'Unauthorized - No authenticated user'], 401);
        }

        return new JsonResponse([
            'id' => $user->getEmail(),
            'email' => $user->getEmail(),
            'roles' => $user->getRoles(),
            'firstName' => $user->getFirstName(),
            'lastName' => $user->getLastName(),

            'specialization' => $user->getSpecialization()->getName(),  
        ]);
    }

    #[Route(path: '/api/logout', name: 'api_logout', methods: ['POST'])]
    public function logout(): JsonResponse
    {
        return new JsonResponse(['status' => 'Logged out successfully'], 200);
    }
}
