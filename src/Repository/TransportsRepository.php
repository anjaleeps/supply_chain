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

    public function scheduleTrainTransport(int $order_id, string $date){
        $conn = $this->getEntityManager()->getConnection();
        $sql = "CALL schedule_train_transportation(?,?)";
        $stmt = $conn->prepare($sql);
        $stmt -> bindParam(1,$order_id);
        $stmt ->bindParam(2,$date);
        $stmt->execute();

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
}
