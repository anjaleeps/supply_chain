<?php

namespace App\Repository;

use App\Entity\Truck;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Truck|null find($truck_no, $lockMode = null, $lockVersion = null)
 * @method Truck|null findOneBy(array $criteria, array $orderBy = null)
 * @method Truck[]    findAll()
 * @method Truck[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TruckRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Truck::class);
    }

    // /**
    //  * @return Truck[] Returns an array of Truck objects
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
    public function findOneBySomeField($value): ?Truck
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function getWorkedHours(){
        $conn= $this->getEntityManager()->getConnection();
        $sql = "SELECT * FROM truck_details";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getAvailableTrucks(string $user_id){    
        $conn= $this->getEntityManager()->getConnection();
        $sql = "SELECT t.id, truck_no FROM truck t
                inner join store_manager sm on sm.store_id=t.store_id 
                where status = 'available'";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(1, $user_id);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
}
