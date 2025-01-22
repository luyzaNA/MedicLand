<?php

namespace App\Services;

use App\Entity\User;
use App\Entity\Specialization;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;

class UserService
{

    public function __construct(
        private UserRepository $userRepository,
        private  EntityManagerInterface $entityManager,
        private SerializerInterface $serializer
    ) {}

    public function serializeUsers(array $users): string
    {
        return $this->serializer->serialize($users, 'json');
    }
    public function getDoctorNamesBySpecialization(string $specializationName): array
    {
        return $this->userRepository->findDoctorNamesBySpecialization($specializationName);
    }

    public function getAllUsers(): array
    {
        return $this->userRepository->findAllUsers();
    }

    public function getUsersByRole(): array
    {
        return $this->userRepository->findByDoctor();
    }

    public function updateUser(string $email, ?string $firstName, array $role): void
    {
        $user = $this->userRepository->findByEmail($email);

        if ($firstName !== null) {
            $user->setFirstName($firstName);
        }    
        $user->setRoles($role);
 
        $this->userRepository->save($user);
    }

    public function deleteUser(string $email): void
    {
        $user = $this->userRepository->findByEmail($email);

        if (!$user) {
            throw new \Exception('User not found');
        }
        
        $this->userRepository->remove($user);
    }
}