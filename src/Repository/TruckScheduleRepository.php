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


// to set work_hours, initial number should be made 00:00:00 instead of null
    public function setStatusPicked(int $truck_schedule_id ){
        $conn = $this->getEntityManager()->getConnection();
        $sql = "CALL truck_order_picked(?)";
        $stmt = $conn->prepare($sql);
        $stmt -> bindParam(1,$truck_schedule_id);
        $stmt->execute();
    }

    public function setStatusDelivered(int $truck_schedule_id, int $driver_id, int $driver_assistant_id, int $truck_id){
        $conn = $this->getEntityManager()->getConnection();
        $sql = "CALL truck_order_delivered(?,?,?,?)";
        $stmt = $conn->prepare($sql);
        $stmt -> bindParam(1,$truck_schedule_id);
        $stmt -> bindParam(2,$driver_id);
        $stmt -> bindParam(3,$driver_assistant_id);
        $stmt -> bindParam(4,$truck_id );
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

    public function fetchUndeliveredSchedule(string $driver_id){
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT id, truck_id, route_id, status FROM truck_schedule WHERE driver_id=? AND (status='scheduled' OR status='picked')";
        $stmt = $conn->prepare($sql);
        $stmt -> bindParam(1,$driver_id);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    


}
