<?php

namespace App\Repository;

use App\Entity\Route;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Route|null find($id, $lockMode = null, $lockVersion = null)
 * @method Route|null findOneBy(array $criteria, array $orderBy = null)
 * @method Route[]    findAll()
 * @method Route[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RouteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Route::class);
    }

    public function getCustomerRoutes(int $customer_id){
        $conn= $this->getEntityManager()->getConnection();
        $sql = "select r.id,r.decription,r.max_time from 
                customer c inner join store s on c.city=s.city 
                inner join route r on s.id=r.store_id 
                where c.id=$customer_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(1, $customer_id);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    // /**
    //  * @return Route[] Returns an array of Route objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Route
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    public function fetchRoute(int $route_id){
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT description FROM route WHERE id=? ";
        $stmt = $conn->prepare($sql);
        $stmt -> bindParam(1,$route_id);
        $stmt->execute();
        $stmt->fetchAll();
    }
}
