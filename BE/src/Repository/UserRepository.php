<?php
// src/Repository/UserRepository.php
namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, User::class);
    }

    public function save(User $user): void
    {
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    public function remove(User $user): void
    {
        $this->entityManager->remove($user);
        $this->entityManager->flush();
    }

    public function findByCnp(string $cnp): ?User
    {
        return $this->findOneBy(['cnp' => $cnp]);
    }

    public function findByEmail(string $email): ?User
    {
        return $this->findOneBy(['email' => $email]);
    }

    public function findDoctorNamesBySpecialization(string $specializationName): array
{
    $qb = $this->createQueryBuilder('u')
        ->select('u.firstName, u.lastName, u.email') 
        ->join('u.specialization', 's')
        ->where('s.name = :specializationName')
        ->setParameter('specializationName', $specializationName);

    return $qb->getQuery()->getArrayResult();
}
    public function findAllUsers(): array
    {
        return $this->findAll();
    } 

 
public function findByDoctor(): array
{
    return $this->createQueryBuilder('u')
        ->select('u')
        ->where('u.roles LIKE :role')
        ->setParameter('role', '%doctor%')
        ->getQuery()
        ->getResult();
}


}
