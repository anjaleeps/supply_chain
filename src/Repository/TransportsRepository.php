<?php

namespace App\Repository;

use App\Entity\DriverAssistant;
use App\Entity\Transports;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Transports|null find($id, $lockMode = null, $lockVersion = null)
 * @method Transports|null findOneBy(array $criteria, array $orderBy = null)
 * @method Transports[]    findAll()
 * @method Transports[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TransportsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Transports::class);
    }

    public function scheduleTrainTransport(int $order_id){
        $conn = $this->getEntityManager()->getConnection();
        $sql = "select schedule_trains(?) as train_id";
        $stmt = $conn->prepare($sql);
        $stmt -> bindParam(1,$order_id);
        $stmt->execute();
        $data = $stmt->fetchAll();
        $train_id = $data[0]['train_id'];

        $sql = "SELECT ts.id, ts.start_time, t.date 
                FROM train_schedule ts 
                INNER JOIN transports t on t.train_schedule_id=ts.id
                WHERE ts.id = ? and t.orders_id = ?";
         $stmt = $conn->prepare($sql);
         $stmt -> bindParam(1,$train_id);
         $stmt -> bindParam(2,$order_id);
         $stmt->execute();
         return $stmt->fetchAll();
    }

    // /**
    //  * @return Transports[] Returns an array of Transports objects
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
    public function findOneBySomeField($value): ?Transports
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function getExpectedTrains(string $id){
        $conn= $this->getEntityManager()->getConnection();
        $sql = "select ts.id, t.date, ts.start_time, t.status, sm.id,
                addtime(ts.start_time, ts.journey_time) as expected_arrival, count(distinct(t.orders_id)) as order_count
                from transports t 
                inner join train_schedule ts on t.train_schedule_id=ts.id
                inner join store s on s.city=ts.destination
                inner join store_manager sm on sm.store_id=s.id
                group by ts.id, t.date
                having t.status='scheduled' and sm.id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function updateTrainStatus(string $train_id, string $user_id){
        $conn= $this->getEntityManager()->getConnection();
        $sql = "update transports t
        inner join train_schedule ts on ts.id=t.train_schedule_id
        inner join store s on s.city=ts.destination
        inner join store_manager sm on sm.store_id=s.id
        set t.status='arrived'
        where t.status='scheduled' and ts.id=? and sm.id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(1, $train_id);
        $stmt->bindParam(2, $user_id);
        $stmt->execute();
        $stmt->rowCount();
    }

    public function getScheduledOrders(){
        $conn= $this->getEntityManager()->getConnection();
        $sql = "select o.id as order_id, ts.id as train_id, ts.start_time, t.date, ts.destination as city 
                from transports t
                inner join train_schedule ts on ts.id=t.train_schedule_id
                inner join orders o on o.id=t.orders_id
                where timestamp(t.date, ts.start_time) > now()
                and t.status='scheduled'
                order by timestamp(t.date, ts.start_time)";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
