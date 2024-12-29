<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Services\RegisterService;
use Symfony\Component\HttpFoundation\Request;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

class RegistrationController extends AbstractController
{

    public function __construct(private UserPasswordHasherInterface $passwordHasher,
                                private RegisterService $registerService,
                                private JWTTokenManagerInterface $jwtManager
                                )               
    {
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

    #[Route('/api/register', name: 'api_register', methods: ['POST'])]
    public function register(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);  
        try {
            $response = $this->registerService->register(
                $data['email'],
                $data['cnp'],
                $data['password'],
                $data['firstName'],
                $data['lastName'],
                $data['specialization']
            );

            return new JsonResponse($response, status: 201);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }


    #[Route('/api/me', name: 'api_me', methods: ['GET'])]
    public function getMe(): JsonResponse
    {
        $doctor = $this->registerService->getAuthenticatedDoctor();

        if (!$doctor) {
            return new JsonResponse(['error' => 'Unauthorized'], 401);
        }

        return new JsonResponse([
            'email' => $doctor->getEmail(),
            'cnp' => $doctor->getCnp(),
            'firstName' => $doctor->getFirstName(),
            'lastName' => $doctor->getLastName(),
            'specialization' => $doctor->getSpecialization(),
            'role' => $doctor->getRole(),
        ]);
    }
}