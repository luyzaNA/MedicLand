<?php

namespace App\Services;

use App\Entity\Disease;
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
        private DiseaseRepository $diseaseRepositorye
    ) {}

    public function addDdisease(string $name, ?string $description = null): Disease
    {
        $disease = new Disease();
        $disease->setName($name);
        $disease->setDescription($description);

        $this->diseaseRepositorye->save($disease);

        return $disease;
    }

    public function deleteDdisease(string $name): void
    {
        $disease = $this->diseaseRepositorye->findDisease($name);
        if($disease){
         $this->diseaseRepositorye->remove($disease);
        }
    }


}