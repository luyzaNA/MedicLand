<?php

namespace App\Repository;

use App\Entity\Patient;
use App\Entity\Consultation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;


class PatientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, Patient::class);
    }
   
    public function add(Patient $patient): void
    {
        $this->entityManager->persist($patient);
        $this->entityManager->flush();
    }

    public function delete(Patient $patient): void
    {
        $this->entityManager->remove($patient);
        $this->entityManager->flush();
    }

  
    public function findByCnp(string $cnp): ?Patient
    {
        return $this->findOneBy(['cnp' => $cnp]);
    }

   
    public function findAllPatients(): array
    {
        return $this->findAll();
    }

    public function update(Patient $patient): void
    {
        $this->entityManager->persist($patient);
        $this->entityManager->flush();
    }
    


}

    