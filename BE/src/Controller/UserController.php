<?php

namespace App\Controller;

use App\Services\SpecializationService;
use App\Services\UserService;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserController extends AbstractController
{
    public function __construct(
        private Security $security,
        private SpecializationService $specializationService,
        private ValidatorInterface $validator,
        private UserService $userService
) {}

    #[Route('/users/specialization/{name}', name: 'get_names_by_specialization', methods: ['GET'])]
    public function getNamesBySpecialization(string $name): JsonResponse
    {
        $names = $this->userService->getDoctorNamesBySpecialization($name);

        return new JsonResponse($names, 200);
    }

    #[Route('/api/users', name: 'get_users', methods: ['GET'])]
    public function getUsers(): JsonResponse
    {
        $user = $this->getUser();
    
        if (!$user instanceof User) {
            return new JsonResponse(['error' => 'Missing user'], 400);
        }
    
        $role = $user->getRoles();
    
        if (!in_array('admin', $role)) {
            return new JsonResponse(['error' => 'Unauthorized - Only admins can create account'], 403);
        }
    
        $users = $this->userService->getAllUsers();
    
        $filteredUsers = array_map(function ($user) {
            return [
                'email' => $user->getEmail(),
                'roles' => $user->getRoles(),
                'firstName' => $user->getFirstName() ?? $user->getPatient()?->getFirstName(),
                'lastName' =>$user->getLastName() ??  $user->getPatient()?->getLastName(),
                'specialization' => $user->getSpecialization()?->getName(),
            ];
        }, $users);
        $usersJson = $this->specializationService->serializeSpecializations($filteredUsers);

        return new JsonResponse($usersJson, 200, [], true);
    }

    #[Route('/api/users/{email}', name: 'update_user', methods: ['PUT'])]
    public function updateUser(Request $request, string $email): JsonResponse
    {
        $user = $this->getUser();
    
        if (!$user) {
            return new JsonResponse(['error' => 'Missing user'], 400);
        }
    
        if (!in_array('admin', $user->getRoles())) {
            return new JsonResponse(['error' => 'Unauthorized - Only admins can update user data'], 403);
        }
        
        $data = json_decode($request->getContent(), true);

        $constraints = new Assert\Collection([
            'firstName' => [new Assert\NotBlank(), new Assert\Length(['min' => 2])],
            'role' => [new Assert\All([new Assert\Choice(['choices' => ['doctor', 'patient', 'admin', 'director']])])],
        ]);

        $violations = $this->validator->validate($data, $constraints);

        if (count($violations) > 0) {
            $errors = [];
            foreach ($violations as $violation) {
                $errors[] = $violation->getPropertyPath() . ': ' . $violation->getMessage();
            }
            return new JsonResponse(['errors' => $errors], 400);
        }

        $firstName = $data['firstName'] ?? null;
        $roles = $data['role'] ?? [];

        try {
            $this->userService->updateUser($email, $firstName, $roles);
            return new JsonResponse(['success' => 'User updated successfully'], 200);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }

    #[Route('/api/users/{email}', name: 'delete_user', methods: ['DELETE'])]
    public function deleteUser(string $email): JsonResponse
    {
        $user = $this->getUser();
    
        if (!$user instanceof User) {
            return new JsonResponse(['error' => 'Missing user'], 400);
        }
    
        $role = $user->getRoles();
    
        if (!in_array('admin', $role)) {
            return new JsonResponse(['error' => 'Unauthorized - Only admins can delete users'], 403);
        }

          if ($user->getEmail() === $email) {
        return new JsonResponse(['error' => 'You cannot delete your own account'], 400);
    }

        try {
            $this->userService->deleteUser($email);
            return new JsonResponse(['success' => 'User deleted successfully'], 200);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }


    #[Route('/api/doctors', name: 'get_doctors', methods: ['GET'])]
    public function getDoctors(): JsonResponse
    {
        $user = $this->getUser();
    
        if (!$user instanceof User) {
            return new JsonResponse(['error' => 'Missing user'], 400);
        }
    
        $role = $user->getRoles();
    
        if (!in_array('admin', $role)) {
            return new JsonResponse(['error' => 'Unauthorized - Only admins can create account'], 403);
        }
    
$users = $this->userService->getUsersByRole();
        $filteredUsers = array_map(function ($user) {
            return [
                'email' => $user->getEmail(),
                'roles' => $user->getRoles(),
                'firstName' => $user->getFirstName(),
            ];
        }, $users);
        $usersJson = $this->userService->serializeUsers($filteredUsers);

        return new JsonResponse($filteredUsers);
    }
    

}