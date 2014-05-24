<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 20/5/2014
 * Time: 2:17 μμ
 */

namespace Schedule\Repository;


use Doctrine\ORM\EntityRepository;

class EntryRepository extends EntityRepository{
    public function findEntriesBetweenDates(\DateTime $startDate, \DateTime $endDate, $worker){
        #$query = $this->getEntityManager()->createQuery("SELECT e FROM Schedule\Entity\Entry e WHERE DATE_FORMAT(e.startTime, '%d-%m-%Y') = '{$startDate}'  OR DATE_FORMAT(e.endTime, '%d-%m-%Y') = '{$startDate}'");
        $qb = $this->createQueryBuilder('e');
        $qb->select('e')
            ->add('from', 'Schedule\Entity\Entry e')
            ->where("e.startTime >= :startDate")
            ->andWhere("e.startTime <= :endDate")
            ->andWhere("e.worker = :worker")
            ->setParameter('startDate',$startDate)
            ->setParameter('worker',$worker)
            ->setParameter('endDate', $endDate);

        $query = $qb->getQuery();
        return $query->getResult();
    }
} 