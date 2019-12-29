<?php

namespace App\Repository;

use App\Entity\PhoneNumber;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method PhoneNumber|null find($id, $lockMode = null, $lockVersion = null)
 * @method PhoneNumber|null findOneBy(array $criteria, array $orderBy = null)
 * @method PhoneNumber[]    findAll()
 * @method PhoneNumber[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PhoneNumberRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PhoneNumber::class);
    }

    // /**
    //  * @return PhoneNumber[] Returns an array of PhoneNumber objects
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
    public function findOneBySomeField($value): ?PhoneNumber
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function insertPNum(string $phone_number, int $customer_id ){
        $conn= $this->getEntityManager()->getConnection();
        $sql = "insert into phone_number (customer_id, phone_number) 
                values (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(1, $customer_id);
        $stmt->bindParam(2, $phone_number);
        $stmt->execute();
    }
}
