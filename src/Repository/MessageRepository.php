<?php

namespace App\Repository;

use App\Entity\Message;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

use App\Entity\User;

/**
 * @extends ServiceEntityRepository<Message>
 */
class MessageRepository extends ServiceEntityRepository
{
   
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }
    
    /**
     * Trouve les messages reçus par un utilisateur
     */
    public function findMessagesRecus(User $user): array
    {
        return $this->createQueryBuilder('m')
            ->where('m.receiver = :user')
            ->setParameter('user', $user)
            ->orderBy('m.dateHeure', 'DESC')
            ->getQuery()
            ->getResult();
    }
    
    /**
     * Trouve les messages envoyés par un utilisateur
     */
    public function findMessagesEnvoyes(User $user): array
    {
        return $this->createQueryBuilder('m')
            ->where('m.sender = :user')
            ->setParameter('user', $user)
            ->orderBy('m.dateHeure', 'DESC')
            ->getQuery()
            ->getResult();
    }
}