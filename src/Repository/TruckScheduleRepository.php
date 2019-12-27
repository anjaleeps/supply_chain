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


    public function setStatusPicked(int $id ){
        $conn = $this->getEntityManager()->getConnection();
        $sql = "UPDATE truck_schedule SET status='picked' WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt -> bindParam(1,$id);
        $stmt->execute();
    }

    public function setStatusDelivered(int $id ){
        $conn = $this->getEntityManager()->getConnection();
        $sql = "UPDATE truck_schedule SET status='delivered' WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt -> bindParam(1,$id);
        $stmt->execute();
    }

    public function scheduleTruckDelivery(string $route_id, string $driver_id, string $assistant_id, string $truck_id){
        $conn = $this->getEntityManager()->getConnection();
        $sql = "INSERT INTO truck_schedule 
                SET truck_id=?, driver_id=?, driver_assistant_id=?, route_id=?, status='scheduled'";
        $stmt = $conn->prepare($sql);
        $stmt -> bindParam(1,$truck_id);
        $stmt -> bindParam(2,$driver_id);
        $stmt -> bindParam(3,$assistant_id);
        $stmt -> bindParam(4,$route_id);
        $stmt->execute();
    }
}
