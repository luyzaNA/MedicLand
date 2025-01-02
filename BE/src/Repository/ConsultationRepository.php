<?php
namespace App\Repository;

use App\Entity\Consultation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

class ConsultationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, Consultation::class);
    }

    public function add(Consultation $consultation): void
    {
        $this->entityManager->persist(object: $consultation);
        $this->entityManager->flush();
    } 

    public function remove(Consultation $consultation): void
    {
        $this->entityManager->remove(object: $consultation);
        $this->entityManager->flush();
    }

     
    public function findConsultation($name): ?Consultation
    {
        return $this->find($name);
    }

}
