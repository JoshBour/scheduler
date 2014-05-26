<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 20/5/2014
 * Time: 2:17 μμ
 */

namespace Schedule\Repository;


use Doctrine\ORM\EntityRepository;

class ChangelogRepository extends EntityRepository{
    public function findEntriesBetweenDates(\DateTime $startDate, \DateTime $endDate){
        $qb = $this->createQueryBuilder('c');
        $qb->select('c')
            ->add('from','Schedule\Entity\Changelog c')
           # ->add('from', 'Schedule\Entity\Changelog c LEFT JOIN c.newEntry e')
            ->where("c.oldStartTime >= :startDate")
            ->andWhere("c.oldStartTime <= :endDate")
            ->setParameter('startDate',$startDate)
            ->setParameter('endDate', $endDate);

        $query = $qb->getQuery();
        return $query->getResult();
    }
} 