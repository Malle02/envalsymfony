<?php

namespace App\Repository;

use App\Entity\Parcours;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


use App\Entity\User;

/**
 * @extends ServiceEntityRepository<Parcours>
 */
class ParcoursRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Parcours::class);
    }
    
    /**
     * Trouve tous les parcours associés à un utilisateur
     */
    public function findParcoursForUser(User $user): array
    {
        $qb = $this->createQueryBuilder('p')
            ->leftJoin('p.users', 'u')
            ->where('u.id = :userId')
            ->setParameter('userId', $user->getId());
        
        // Si l'utilisateur est conseiller, il peut voir tous les parcours
        if (in_array('ROLE_CONSEILLER', $user->getRoles())) {
            $qb = $this->createQueryBuilder('p');
        }
        
        return $qb->getQuery()->getResult();
    }
}