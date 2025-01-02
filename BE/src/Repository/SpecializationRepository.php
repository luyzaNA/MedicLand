<?php

namespace App\Repository;

use App\Entity\Specialization;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @extends ServiceEntityRepository<Specialization>
 */
class SpecializationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, Specialization::class);
    }

    public function save(Specialization $specialization): void
    {
        $this->entityManager->persist($specialization);
        $this->entityManager->flush();
    }
    public function remove(Specialization $specialization): void
    {
        $this->entityManager->remove($specialization);
        $this->entityManager->flush();
    }
    public function findSpecialization($name): ?Specialization
    {
        return $this->find($name);
    }
}
