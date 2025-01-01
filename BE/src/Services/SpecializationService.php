<?php

namespace App\Services;

use App\Entity\Specialization;
use App\Repository\SpecializationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;

class SpecializationService
{

    public function __construct(
        private SpecializationRepository $specializationRepository,
        private  EntityManagerInterface $entityManager,
        private SerializerInterface $serializer
    ) {}

    public function addSpecialization(string $name): Specialization
    {
        $specialization = new Specialization();
        $specialization->setName($name);

        $this->specializationRepository->save($specialization);

        return $specialization;
    }

    public function deleteSpecialization(string $name): void
    {
        $specialization = $this->specializationRepository->findSpecialization($name);
        if ($specialization) {
            $this->specializationRepository->remove($specialization);
        }
    }
    
    public function getSpecialization($name): ?Specialization
    {
        return $this->specializationRepository->findSpecialization($name);
    }
}
