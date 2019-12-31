<?php

namespace App\Repository;

use App\Entity\Orders;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Orders|null find($id, $lockMode = null, $lockVersion = null)
 * @method Orders|null findOneBy(array $criteria, array $orderBy = null)
 * @method Orders[]    findAll()
 * @method Orders[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrdersRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Orders::class);
    }


    public function getSalesReport(){
        $conn= $this->getEntityManager()->getConnection();
        $sql = "select YEAR(o.date_completed) as year, MONTH(o.date_completed) as month, c.city as city, o.route_id as route_id, 
                sum(op.quantity) as product_count, sum(p.unit_price*op.quantity) as earnings, count(o.id) as order_count
                from orders o 
                inner join order_product op on o.id=op.orders_id 
                inner join product p on p.id=op.product_id
                inner join customer c on c.id=o.customer_id
                group by YEAR(o.date_completed), MONTH(o.date_completed),c.city, o.route_id
                order by year desc, month desc;";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getQuarterlyReport(string $year){
        $conn= $this->getEntityManager()->getConnection();
        $sql = "select count(distinct(c.id)) as customer_count, count(distinct(o.id)) as order_count,
                sum(op.quantity) as product_count, sum(op.quantity*p.unit_price) as revenue, 
                quarter(o.date_completed) as quarter, o.date_completed from orders o 
                inner join customer c on c.id=o.customer_id
                inner join order_product op on op.orders_id=o.id
                inner join product p on p.id=op.product_id
                group by quarter(o.date_completed) 
                having year(o.date_completed) = ? 
                order by quarter(o.date_completed)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(1, $year);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getRecordedYears(){
        $conn= $this->getEntityManager()->getConnection();
        $sql = "select distinct year(date_completed) as year from orders
                order by year(date_completed) desc";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getStoredOrders(string $id){
        $conn= $this->getEntityManager()->getConnection();
        $sql = "select o.id as order_id, c.first_name, c.last_name, p.name as product_name, op.quantity, r.id as route_id from orders o 
                inner join customer c on c.id=o.customer_id
                inner join route r on r.id=o.route_id
                inner join order_product op on op.orders_id=o.id
                inner join product p on p.id=op.product_id
                inner join store s on s.id=r.store_id
                inner join store_manager sm on sm.store_id=s.id
                where sm.id=? and o.order_status= 'on store'
                order by r.id, o.id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->fetchAll();   
    }

    public function setStatusDelivered(int $order_id){
        $conn = $this->getEntityManager()->getConnection();
        $sql = "CALL order_delivered(?)";
        $stmt = $conn->prepare($sql);
        $stmt -> bindParam(1,$order_id);
        $stmt->execute();
    }

    public function placeOrder(int $customer_id, int $route_id, string $status, string $date ){
        $conn= $this->getEntityManager()->getConnection();
        $sql = "insert into orders (customer_id, route_id, order_status, date_placed) 
                values (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(1, $customer_id);
        $stmt->bindParam(2, $route_id);
        $stmt->bindParam(3, $status);
        $stmt->bindParam(4, $date);
        $stmt->execute();

        $last_id = $conn->lastInsertId();
        return $last_id;
    }

    public function getCustomerOrders(int $customer_id){
        $conn= $this->getEntityManager()->getConnection();
        $sql = "select o.id, o.route_id, o.order_status, o.date_placed, o.date_completed, op.product_id, op.quantity, 
                p.name, p.unit_price from orders o 
                inner join order_product op on o.id=op.orders_id
                inner join product p on op.product_id=p.id
                where o.customer_id=$customer_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(1, $customer_id);
        $stmt->execute();

        return $stmt->fetchAll();

    }
}
