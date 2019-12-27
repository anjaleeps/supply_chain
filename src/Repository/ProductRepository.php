<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    // /**
    //  * @return Product[] Returns an array of Product objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Product
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function getProductOrderCount(){
        $conn= $this->getEntityManager()->getConnection();
        $sql = "SELECT * FROM product_order_count LIMIT 20";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getHighestSoldProducts(){
        $conn= $this->getEntityManager()->getConnection();
        $sql = "select product_name, max(sales_quantity) as max_sales_quantity, month, year from
                (select p.name as product_name, sum(op.quantity) as sales_quantity, month(o.date_completed) as month, year(o.date_completed) as year 
                from product p inner join order_product op on op.product_id=p.id
                inner join orders o on o.id=op.orders_id
                group by year, month, op.product_id 
                order by year, month, sum(op.quantity)) as t
                group by year, month
                order by year desc, month desc limit 12";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();   
    }

    public function getHighestSoldCategories(){
        $conn= $this->getEntityManager()->getConnection();
        $sql = "select category_name, max(sales_quantity) as max_sales_quantity, month, year from 
                (select p.category as category_name, sum(op.quantity) as sales_quantity, month(o.date_completed) as month, year(o.date_completed) as year 
                from product p inner join order_product op on op.product_id=p.id 
                inner join orders o on o.id=op.orders_id 
                group by year, month, p.category
                order by year, month, sum(op.quantity)) as t
                group by year, month
                order by year desc, month desc limit 12";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();   
    }
}
