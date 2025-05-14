<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Trouve tous les utilisateurs ayant un rôle spécifique
     */
    // public function findByRole(string $role): array
    // {
    //     $qb = $this->createQueryBuilder('u');
    //     $qb->where('JSON_CONTAINS(u.roles, :role) = 1')
    //        ->setParameter('role', '"'.$role.'"');

    //     return $qb->getQuery()->getResult();
    // }

    public function findByRole(string $role): array
{
    $qb = $this->createQueryBuilder('u');
    $qb->where('u.roles LIKE :role')
       ->setParameter('role', '%"'.$role.'"%');

    return $qb->getQuery()->getResult();
}


//     public function findCandidatsNonAccompagnes(User $conseiller): array
// {
//     $qb = $this->createQueryBuilder('u');
//     $qb->where('JSON_CONTAINS(u.roles, :role) = 1')
//        ->andWhere('u.id NOT IN (
//             SELECT IDENTITY(u2.id) 
//             FROM App\Entity\User c 
//             JOIN c.accompagnes u2 
//             WHERE c.id = :conseillerId
//         )')
//        ->setParameter('role', '"ROLE_USER"')
//        ->setParameter('conseillerId', $conseiller->getId())
//        ->orderBy('u.lastname', 'ASC');

//     return $qb->getQuery()->getResult();
// }



public function findCandidatsNonAccompagnes(User $conseiller): array
{
    $qb = $this->createQueryBuilder('u');
    $qb->where('u.roles LIKE :role')
       ->andWhere('u.id NOT IN (
            SELECT IDENTITY(u2.id) 
            FROM App\Entity\User c 
            JOIN c.accompagnes u2 
            WHERE c.id = :conseillerId
        )')
       ->setParameter('role', '%"ROLE_USER"%')
       ->setParameter('conseillerId', $conseiller->getId())
       ->orderBy('u.lastname', 'ASC');

    return $qb->getQuery()->getResult();
}

}