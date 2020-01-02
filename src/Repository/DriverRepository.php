<?php

namespace App\Repository;

use App\Entity\Driver;
use App\Entity\TruckSchedule;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use \DateInterval;
use \DateTime;

/**
 * @method Driver|null find($id, $lockMode = null, $lockVersion = null)
 * @method Driver|null findOneBy(array $criteria, array $orderBy = null)
 * @method Driver[]    findAll()
 * @method Driver[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DriverRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Driver::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        if (!$user instanceof Driver) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newEncodedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    public function getWorkedHours(){
        $conn= $this->getEntityManager()->getConnection();
        $sql = "SELECT * FROM driver_details";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getAvailableDrivers(string $user_id ){
        $conn= $this->getEntityManager()->getConnection();
        $sql = "select distinct d.id, d.first_name, d.last_name from driver d 
                inner join store_manager sm on sm.store_id=d.store_id 
                inner join route r on r.store_id=d.store_id
                where (not d.id <=> (select driver_id from truck_schedule ts 
                where ts.status='delivered' order by ts.end_time desc, ts.start_time desc, ts.id desc limit 1)) 
                and sm.id=? and hour(addtime(r.max_time, d.work_hours)) < 40 and status='available'
                order by d.work_hours";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(1, $user_id);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function changeAvailability($status, $id){
        $conn= $this->getEntityManager()->getConnection();
        $sql = "UPDATE driver SET status=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(1, $status);
        $stmt->bindParam(2, $id);
        $stmt->execute();
    }

}
