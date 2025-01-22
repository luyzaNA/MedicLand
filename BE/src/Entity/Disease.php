<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "disease", uniqueConstraints: [
    new ORM\UniqueConstraint(name: 'UNIQ_DISEASE_NAME', columns: ['name'])
])]
class Disease
{
    #[ORM\Id]
    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(enumType: DiseaseCategory::class, nullable: false)]
    private DiseaseCategory $category;

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }


    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getCategory(): DiseaseCategory
    {
        return $this->category;
    }

    public function setCategory(DiseaseCategory $category): static
    {
        $this->category = $category; 
        return $this;
    }
}
enum DiseaseCategory: string
{
    case INFECTIOUS = 'Infectious';
    case CHRONIC = 'Chronic';
    case GENETIC = 'Genetic';
    case AUTOIMMUNE = 'Autoimmune';
    case OTHER = 'Other';
}


