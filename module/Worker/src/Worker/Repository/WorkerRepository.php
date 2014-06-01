<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 20/5/2014
 * Time: 2:16 μμ
 */

namespace Worker\Repository;


use Doctrine\ORM\EntityRepository;

class WorkerRepository extends EntityRepository{
    public function findAllNonReleasedWorkers($startDate,$endDate){
        $qb = $this->createQueryBuilder('e');
        $qb->select('w')
            ->add('from', 'Worker\Entity\Worker w')
            ->where("w.releaseDate IS NULL OR w.releaseDate BETWEEN :startDate AND :endDate")
            ->setParameter('startDate',$startDate)
            ->setParameter('endDate', $endDate);

        $query = $qb->getQuery();
        return $query->getResult();
    }
} 