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


    public function serializeSpecializations(array $specializations): string
    {
        return $this->serializer->serialize($specializations, 'json');
    }
    public function addSpecialization(string $name): Specialization
    {
        $existingSpecialization = $this->specializationRepository->findSpecialization($name);
        if ($existingSpecialization) {
            throw new \Exception('Specialization with this name already exists');
        }

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

    public function getAllSpecializations(): array
    {
        return $this->specializationRepository->findAllSpecializations();
    }
}
