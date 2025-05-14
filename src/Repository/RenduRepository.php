<?php

namespace App\Repository;

use App\Entity\Rendu;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

use App\Entity\Etape;
use App\Entity\User;

/**
 * @extends ServiceEntityRepository<Rendu>
 */
class RenduRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Rendu::class);
    }
    
    /**
     * Trouve le dernier rendu pour une étape et un utilisateur donnés
     */
    public function findLastRenduByEtapeAndUser(Etape $etape, User $user): ?Rendu
    {
        return $this->createQueryBuilder('r')
            ->where('r.etape = :etape')
            ->andWhere('r.user = :user')
            ->setParameter('etape', $etape)
            ->setParameter('user', $user)
            ->orderBy('r.dateHeure', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}