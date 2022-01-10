<?php

namespace App\Repository;

use App\Entity\TimeReport;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TimeReport|null find($id, $lockMode = null, $lockVersion = null)
 * @method TimeReport|null findOneBy(array $criteria, array $orderBy = null)
 * @method TimeReport[]    findAll()
 * @method TimeReport[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TimeReportRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TimeReport::class);
    }

    // /**
    //  * @return TimeReport[] Returns an array of TimeReport objects
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
    public function findOneBySomeField($value): ?TimeReport
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
