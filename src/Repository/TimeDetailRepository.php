<?php

namespace App\Repository;

use App\Entity\TimeDetail;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TimeDetail|null find($id, $lockMode = null, $lockVersion = null)
 * @method TimeDetail|null findOneBy(array $criteria, array $orderBy = null)
 * @method TimeDetail[]    findAll()
 * @method TimeDetail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TimeDetailRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TimeDetail::class);
    }

    // /**
    //  * @return TimeDetail[] Returns an array of TimeDetail objects
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
    public function findOneBySomeField($value): ?TimeDetail
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
