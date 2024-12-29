<?php

namespace App\Services;

use App\Entity\Doctor;
use App\Repository\DoctorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
class RegisterService
{

    public function __construct(private DoctorRepository $doctorRepository, 
                                private  EntityManagerInterface $entityManager, 
                                private  JWTTokenManagerInterface $jwtManager,
                                private  UserPasswordHasherInterface $passwordHasher
                               )
    { }

    public function login(array $credentials): ?string
    {
        $email = $credentials['email'] ?? null;
        $password = $credentials['password'] ?? null;

        if (!$email || !$password) {
            return null; 
        }

        $user = $this->doctorRepository->findOneByEmail( $email);

        if (!$user || !$this->passwordHasher->isPasswordValid($user, $password)) {
            return null; 
        }
        return $this->jwtManager->create($user);
    }

    public function register(string $email, string $cnp, string $password, string $firstName, string $lastName, string $specialization): array
    {
        $existingDoctorByEmail = $this->doctorRepository->findByEmail($email);
        if ($existingDoctorByEmail) {
            throw new \Exception('Email already in use');
        }

        $existingDoctorByCnp = $this->doctorRepository->findByCnp($cnp);
        if ($existingDoctorByCnp) {
            throw new \Exception('CNP already in use');
        }

        $doctor = new Doctor();
        $doctor->setEmail($email);
        $doctor->setCnp($cnp);
        $doctor->setFirstName($firstName);
        $doctor->setLastName($lastName);
        $doctor->setSpecialization($specialization);
        $doctor->setRole('DOCTOR'); 

        $hashedPassword = $this->passwordHasher->hashPassword($doctor, $password);
        $doctor->setPassword($hashedPassword);

        $this->doctorRepository->save($doctor);

        $token = $this->jwtManager->create($doctor);

        return [
            'status' => 'User registered successfully',
            'token' => $token,
        ];
    }

}

