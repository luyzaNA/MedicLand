<?php

namespace App\Repository;

use App\Entity\Disease;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

class DiseaseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, Disease::class);
    }

    public function save(Disease $disease): void
    {
        $this->entityManager->persist($disease);
        $this->entityManager->flush();
    }
    public function remove(Disease $disease): void
    {
        $this->entityManager->remove($disease);
        $this->entityManager->flush();
    }
    public function findDisease($name): ?Disease
    {
        return $this->find($name);
    }
}
