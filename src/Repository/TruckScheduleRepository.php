<?php

namespace App\Repository;

use App\Entity\TruckSchedule;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method TruckSchedule|null find($id, $lockMode = null, $lockVersion = null)
 * @method TruckSchedule|null findOneBy(array $criteria, array $orderBy = null)
 * @method TruckSchedule[]    findAll()
 * @method TruckSchedule[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TruckScheduleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TruckSchedule::class);
    }

    // /**
    //  * @return TruckSchedule[] Returns an array of TruckSchedule objects
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
    public function findOneBySomeField($value): ?TruckSchedule
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
