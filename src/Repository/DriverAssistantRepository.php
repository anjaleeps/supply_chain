<?php

namespace App\Repository;

use App\Entity\DriverAssistant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method DriverAssistant|null find($id, $lockMode = null, $lockVersion = null)
 * @method DriverAssistant|null findOneBy(array $criteria, array $orderBy = null)
 * @method DriverAssistant[]    findAll()
 * @method DriverAssistant[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DriverAssistantRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DriverAssistant::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        if (!$user instanceof DriverAssistant) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newEncodedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    // /**
    //  * @return DriverAssistant[] Returns an array of DriverAssistant objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?DriverAssistant
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function getWorkedHours(){
        $conn= $this->getEntityManager()->getConnection();
        $sql = "SELECT * FROM driver_assistant_details";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getAvailableAssistants(string $user_id){
        $conn= $this->getEntityManager()->getConnection();
        $sql = "select distinct da.id, da.first_name, da.last_name from driver_assistant da 
                inner join store_manager sm on sm.store_id=da.store_id 
                inner join route r on r.store_id=da.store_id
                where ( not da.id <=> (select driver_assistant_id from truck_schedule ts 
                where ts.status='delivered' order by ts.end_time desc, ts.start_time desc, ts.id desc limit 0,1)
                or not da.id <=> (select driver_assistant_id from truck_schedule ts where ts.status='delivered' 
                order by ts.end_time desc, ts.start_time desc, ts.id desc limit 1,1))
                and (sm.id=? and hour(addtime(r.max_time, da.work_hours)) < 60 and status='available')
                order by da.work_hours";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(1, $user_id);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function changeAvailability($status, $id){
        $conn= $this->getEntityManager()->getConnection();
        $sql = "UPDATE driver_assistant SET status=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(1, $status);
        $stmt->bindParam(2, $id);
        $stmt->execute();
    }
    
}
