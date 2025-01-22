<?php

namespace App\Services;

use App\Entity\Specialization;
use App\Entity\User;
use App\Repository\PatientRepository;
use App\Repository\SpecializationRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class RegisterService
{
    public function __construct(
        private UserRepository $userRepository,
        private SpecializationRepository $specializationRepository,
        private  EntityManagerInterface $entityManager,
        private  JWTTokenManagerInterface $jwtManager,
        private  UserPasswordHasherInterface $passwordHasher,
        private Security $security,
        private PatientRepository $patientRepository
    ) {}

    public function login(array $credentials): ?string
    {
        $email = $credentials['email'] ?? null;
        $password = $credentials['password'] ?? null;

        if (!$email || !$password) {
            return null;
        }

        $user = $this->userRepository->findByEmail($email);


        if (!$user || !$this->passwordHasher->isPasswordValid($user, $password)) {
            return null;
        }

        return $this->jwtManager->create($user);
    }

    public function register(string $email, ?string $cnp = null, string $password, ?string $firstName = null, ?string $lastName = null, ?Specialization $specialization = null, array $roles): array
    {
        $user = new User();
        $user->setEmail($email);
        $user->setCnp($cnp);
        $user->setFirstName($firstName);
        $user->setLastName($lastName);
        $user->setSpecialization(specialization: $specialization);
        $user->setRoles($roles); 
        $hashedPassword = $this->passwordHasher->hashPassword($user, $password);
        $user->setPassword($hashedPassword);

        $this->userRepository->save($user);

        return [
            'status' => 'Account created successfully.',
        ];        
    }

    public function registerPatient(string $email, string $password): array
    {
        $user = new User();
        $user->setEmail($email);
        $user->setRoles(['patient']);

        $hashedPassword = $this->passwordHasher->hashPassword($user, $password);
        $user->setPassword($hashedPassword);

        $this->userRepository->save($user);

        $token = $this->jwtManager->create($user);

        return [
            'status' => 'User registered successfully',
            'token' => $token,
        ];
    }
}
