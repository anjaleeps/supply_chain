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

}
