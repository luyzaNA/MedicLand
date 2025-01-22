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
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RegistrationController extends AbstractController
{

    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
        private RegisterService $registerService,
        private SpecializationService $specializationService,
        private JWTTokenManagerInterface $jwtManager,
        private Security $security,
        private ValidatorInterface $validator
       
) {}

    #[Route('/api/register', name: 'api_register', methods: ['POST'])]
    public function register(Request $request): JsonResponse
    {
        $user = $this->getUser();

        if(!$user  instanceof User)
        {
            return new JsonResponse (['error' => 'Missing user'], 400);
        }

        $role = $user->getRoles();

        if (!in_array('admin', $role)) {
            return new JsonResponse(['error' => 'Unauthorized - Only admins can create account'], 403);
        }

        $data = json_decode($request->getContent(), true);

    if (isset($data['roles']) && !(in_array('admin', $data['roles'])))   {
            $constraints = new Assert\Collection([
                'email' => [new Assert\NotBlank(), new Assert\Email()],
                'password' => [new Assert\NotBlank(), new Assert\Length(['min' => 15])],
                'roles' => [new Assert\NotBlank()],
                'cnp' => [new Assert\NotBlank(), new Assert\Length(['min' => 13, 'max' => 13]), new Assert\Regex('/^\d{13}$/')],
                'firstName' => [new Assert\NotBlank()],
                'lastName' => [new Assert\NotBlank()],
                'specialization' => [new Assert\NotBlank()]
            ]);
        }else  {
            $constraints = new Assert\Collection([
                'email' => [new Assert\NotBlank(), new Assert\Email()],
                'password' => [new Assert\NotBlank(), new Assert\Length(['min' => 8])],
                'roles' => [new Assert\NotBlank()]
            ]);
        }

        $violations = $this->validator->validate($data, $constraints);

        if (count($violations) > 0) {
            $errors = [];
            foreach ($violations as $violation) {
                $errors[] = $violation->getPropertyPath() . ': ' . $violation->getMessage();
            }
            return new JsonResponse(['errors' => $errors], 400);
        }

        try {
            $specialization = null;
            if (isset($data['specialization'])) {
                $specialization = $this->specializationService->getSpecialization($data['specialization']);
                if (!$specialization) {
                    $specialization = $this->specializationService->addSpecialization($data['specialization']);
                    if (!$specialization) {
                        throw new \Exception('Could not automatically create specialization. Please create the specialization manually first.');
                    }
                    }
            }
          $roles= $data['roles'];

            $response = $this->registerService->register(
                $data['email'],
                $data['cnp'] ?? null,
                $data['password'],
                $data['firstName'] ?? null,
                $data['lastName'] ?? null,
                $specialization ?? null,
                $roles
            );

            return new JsonResponse($response, 201);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }

    #[Route('/register/patient', name: 'register_patient', methods: ['POST'])]
    public function registerPatient(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
       
            $constraints = new Assert\Collection([
                'email' => [new Assert\NotBlank(), new Assert\Email()],
                'password' => [new Assert\NotBlank(), new Assert\Length(['min' => 8])]            ]);
        

        $violations = $this->validator->validate($data, $constraints);

        if (count($violations) > 0) {
            $errors = [];
            foreach ($violations as $violation) {
                $errors[] = $violation->getPropertyPath() . ': ' . $violation->getMessage();
            }
            return new JsonResponse(['errors' => $errors], 400);
        }

        try {
            $response = $this->registerService->registerPatient(
                $data['email'],
                $data['password']
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

        $constraints = new Assert\Collection([
            'email' => [new Assert\NotBlank(), new Assert\Email()],
            'password' => [new Assert\NotBlank()],
        ]);
    
        $violations = $this->validator->validate($data, $constraints);
    
        if (count($violations) > 0) {
            $errors = [];
            foreach ($violations as $violation) {
                $errors[] = $violation->getPropertyPath() . ': ' . $violation->getMessage();
            }
            return new JsonResponse(['errors' => $errors], 400);
        }

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

        if (in_array('doctor', $user->getRoles())) {
            return new JsonResponse([
            'id' => $user->getEmail(),
            'email' => $user->getEmail(),
            'roles' => $user->getRoles(),
            'firstName' => $user->getFirstName(),
            'lastName' => $user->getLastName(),
            'specialization' => $user->getSpecialization()->getName(),  
        ]);
    }else  {
            return new JsonResponse([
                'id' => $user->getEmail(),
                'email' => $user->getEmail(),
                'roles' => $user->getRoles(),
            ]);
        }
    }


    #[Route(path: '/api/logout', name: 'api_logout', methods: ['POST'])]
    public function logout(): JsonResponse
    {
        return new JsonResponse(['status' => 'Logged out successfully'], 200);
    }
}
