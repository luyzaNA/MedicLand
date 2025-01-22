<?php

namespace App\Services;

use App\Entity\Disease;
use App\Entity\DiseaseCategory;
use App\Repository\DiseaseRepository;
use App\Repository\SpecializationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;

class DiseaseService
{

    public function __construct(
        private SpecializationRepository $specializationRepository,
        private  EntityManagerInterface $entityManager,
        private SerializerInterface $serializer,
        private DiseaseRepository $diseaseRepository
    ) {}

  public function addDisease(string $name, DiseaseCategory $category, ?string $description = null): Disease | null
{
    $existingDisease = $this->diseaseRepository->findDisease($name);
    if ($existingDisease) {
        return null;
    }


    $disease = new Disease();
    $disease->setName($name);
    $disease->setDescription($description);
    $disease->setCategory($category);

    $this->diseaseRepository->save($disease);
    $existingDisease = $this->diseaseRepository->findDisease($name);
    return $existingDisease;
}

public function serializePatient(Disease $disease): string
{
    return $this->serializer->serialize($disease, 'json');
}

    public function deleteDdisease(string $name): void
    {
        $disease = $this->diseaseRepository->findDisease($name);

        if($disease){
         $this->diseaseRepository->remove($disease);
        }
    }

    public function findDisease(string $name): ?Disease
    {
        return $this->diseaseRepository->findDisease($name);
    }
}