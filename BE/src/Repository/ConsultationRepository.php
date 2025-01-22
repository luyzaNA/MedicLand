<?php
namespace App\Repository;

use App\Entity\Consultation;
use App\Entity\Patient;
use App\Entity\User;
use App\Entity\DiseaseCategory;
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

     
    public function findConsultation($id): ?Consultation
    {
        return $this->find($id);
    }

    public function findPatientByConsultationId(int $consultationId): ?Patient
    {
        $consultation = $this->findConsultation($consultationId);
        return $consultation ? $consultation->getPatient() : null;
    }

    public function findAllConsultations(): array
    {
        return $this->findAll();
    } 

    public function findByDoctor(User $doctor): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.doctor = :doctor')
            ->setParameter('doctor', $doctor)
            ->getQuery()
            ->getResult();
    }

    
    public function getPatientsFromDoctor(string $doctorEmail): array
{
    $query = $this->createQueryBuilder('c')
        ->select('p')
        ->innerJoin(Patient::class, 'p', 'WITH', 'c.patient = p.cnp')
        ->where('c.doctor = :doctorEmail')  
        ->setParameter('doctorEmail', $doctorEmail)
        ->getQuery();

    return $query->getResult();
}

public function findConsultationsByPatientCnp(string $cnp): array
{
    $query = $this->createQueryBuilder('c')
        ->innerJoin(Patient::class, 'p', 'WITH', 'c.patient = p.cnp')
        ->where('p.cnp = :cnp')
        ->setParameter('cnp', $cnp)
        ->getQuery();

    return $query->getResult();
}

public function findPatientsByDoctorAndDisease(string $doctorEmail, string $diseaseName): array
{
    $queryBuilder = $this->createQueryBuilder('c')
        ->join('c.patient', 'p')                     
        ->leftJoin('p.medicalHistory', 'mh')         
        ->leftJoin('c.diagnostic', 'diag')           
        ->where('c.doctor = :doctor')               
        ->andWhere('mh.name = :diseaseName OR diag.name = :diseaseName')  
        ->setParameter('doctor', $doctorEmail)
        ->setParameter('diseaseName', $diseaseName)
        ->distinct('p.cnp')                          
        ->addOrderBy('c.date', 'DESC')               
        ->getQuery();

    return $queryBuilder->getResult();
}
public function countPatientsBySpecialization(): array
{
    $queryBuilder = $this->createQueryBuilder('c')
        ->join('c.patient', 'p')                    
        ->leftJoin('c.doctor', 'd')                  
        ->leftJoin('d.specialization', 's')          
        ->groupBy('s.name')                          
        ->select('s.name, COUNT(p.cnp) as patientCount') 
        ->getQuery();

    return $queryBuilder->getResult();
}

public function countPatientsByChronicDisease(): array
{
    $queryBuilder = $this->createQueryBuilder('c')
        ->join('c.patient', 'p')                   
        ->join('p.medicalHistory', 'd')                 
        ->where('d.category = :chronicCategory')  
        ->groupBy('d.name')                      
        ->select('d.name as diseaseName, COUNT(DISTINCT p.cnp) as patientCount') 
        ->setParameter('chronicCategory', DiseaseCategory::CHRONIC)
        ->getQuery();

    return $queryBuilder->getResult();
}


    
}
