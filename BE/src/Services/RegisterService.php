<?php

namespace App\Services;

use App\Entity\Doctor;
use App\Repository\DoctorRepository;
use Doctrine\ORM\EntityManagerInterface;
class RegisterService
{

    public function __construct(private DoctorRepository $doctortRepository, 
                                private  EntityManagerInterface $entityManager
                               )
    { }

}