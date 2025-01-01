<?php

namespace App\Entity;

use App\Repository\SpecializationRepository;
use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity]
#[ORM\Table(name: "specialization", uniqueConstraints: [
    new ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_NAME', columns: ['name'])
])]
class Specialization
{
    #[ORM\Id] 
    #[ORM\Column(length: 255, nullable: false)]
    private string $name; 

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }
}

