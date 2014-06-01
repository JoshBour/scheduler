<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 19/5/2014
 * Time: 6:42 μμ
 */

namespace Worker\Service;


use Worker\Entity\Worker as WorkerEntity;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceManagerAwareInterface;

class Worker implements ServiceManagerAwareInterface
{
    /**
     * The entity manager
     *
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * The service manager
     *
     * @var ServiceManager
     */
    private $serviceManager;

    /**
     * The worker repository
     *
     * @var \Worker\Repository\WorkerRepository
     */
    private $workerRepository;

    /**
     * Create a new worker
     *
     * @param array $data
     * @param \Zend\Form\Form $form
     * @return bool
     */
    public function create($data, &$form)
    {
        $worker = new WorkerEntity();
        $em = $this->getEntityManager();

        $form->bind($worker);
        $form->setData($data);
        if (!$form->isValid()) return false;
        if($data['worker']['hireDate'] == null) $worker->setHireDate(null);
        if($data['worker']['releaseDate'] == null) $worker->setReleaseDate(null);
        try {
            $em->persist($worker);
            $em->flush();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Update and save the workers
     *
     * @param array $entities
     * @return bool
     */
    public function save($entities){
        $em = $this->getEntityManager();
        $workerRepository = $this->getWorkerRepository();
        foreach ($entities as $entity) {
            $worker = $workerRepository->find($entity['WorkerId']);
            array_shift($entity);
            foreach ($entity as $key => $value) {
                if (!empty($value))
                    $worker->{'set' . $key}($value);
            }
            $em->persist($worker);
        }
        try {
            $em->flush();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Remove a worker from the database.
     *
     * @param int $id
     * @return bool
     */
    public function remove($id)
    {
        $em = $this->getEntityManager();
        $worker = $this->getWorkerRepository()->find($id);
        try {
            $em->remove($worker);
            $em->flush();
        } catch (\Exception $e) {
            return false;
        }
        return true;
    }

    /**
     * Get the doctrine entity manager
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        if (null === $this->entityManager)
            $this->entityManager = $this->getServiceManager()->get('Doctrine\ORM\EntityManager');
        return $this->entityManager;
    }

    /**
     * Set service manager
     *
     * @param ServiceManager $serviceManager
     */
    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
    }

    /**
     * Get service manager instance
     *
     * @return ServiceManager
     */
    public function getServiceManager()
    {
        return $this->serviceManager;
    }

    /**
     * Get the worker repository
     *
     * @return \Doctrine\ORM\EntityRepository
     */
    public function getWorkerRepository()
    {
        if (null === $this->workerRepository)
            $this->workerRepository = $this->getEntityManager()->getRepository('Worker\Entity\Worker');
        return $this->workerRepository;
    }

} 