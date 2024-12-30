<?php

namespace App\Repository;

use App\Entity\Doctor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @extends ServiceEntityRepository<Doctor>
 */
class DoctorRepository extends ServiceEntityRepository
{
    public function __construct(private ManagerRegistry $registry,
                               private EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, Doctor::class);

    }

    public function findOneByEmail(string $email): ?Doctor
    {
        return $this->findOneBy(['email' => $email]);
    }

    public function findByCnp(string $cnp): ?Doctor
    {
        return $this->findOneBy(['cnp' => $cnp]);
    }

    public function save(Doctor $doctor): void
    {
        $this->entityManager->persist(object: $doctor);
        $this->entityManager->flush();
    }
}
