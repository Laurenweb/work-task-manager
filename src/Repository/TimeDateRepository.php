<?php

namespace App\Repository;

use App\Entity\TimeDate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TimeDate|null find($id, $lockMode = null, $lockVersion = null)
 * @method TimeDate|null findOneBy(array $criteria, array $orderBy = null)
 * @method TimeDate[]    findAll()
 * @method TimeDate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TimeDateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TimeDate::class);
    }

    // /**
    //  * @return TimeDate[] Returns an array of TimeDate objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TimeDate
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
