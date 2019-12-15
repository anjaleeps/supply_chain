<?php

namespace App\Repository;

use App\Entity\TruckOrder;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method TruckOrder|null find($id, $lockMode = null, $lockVersion = null)
 * @method TruckOrder|null findOneBy(array $criteria, array $orderBy = null)
 * @method TruckOrder[]    findAll()
 * @method TruckOrder[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TruckOrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TruckOrder::class);
    }

    // /**
    //  * @return TruckOrder[] Returns an array of TruckOrder objects
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
    public function findOneBySomeField($value): ?TruckOrder
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
