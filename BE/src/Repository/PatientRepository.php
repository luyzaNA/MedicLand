<?php

namespace App\Repository;

use App\Entity\Patient;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @extends ServiceEntityRepository<Patient>
 */
class PatientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, Patient::class);
    }
    /**
     * Add a new patient.
     */
    public function add(Patient $patient): void
    {
        $this->entityManager->persist($patient);
        $this->entityManager->flush();
    }

    /**
     * Delete a patient by their entity.
     */
    public function delete(Patient $patient): void
    {
        $this->entityManager->remove($patient);
        $this->entityManager->flush();
    }

    /**
     * Get a patient by their CNP.
     */
    public function findByCnp(string $cnp): ?Patient
    {
        return $this->findOneBy(['cnp' => $cnp]);
    }

    /**
     * Get all patients.
     */
    public function findAllPatients(): array
    {
        return $this->findAll();
    }

    /**
     * Update a patient (just flush in this case as EntityManager handles the changes).
     */
    public function update(): void
    {
        $this->entityManager->flush();
    }
}
