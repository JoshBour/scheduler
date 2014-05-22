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

    private $addWorkerForm;

    private $entityManager;

    private $serviceManager;

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
        $worker->setPassword(WorkerEntity::getHashedPassword($worker->getPassword()));
        try {
            $em->persist($worker);
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
     * Get the add worker form
     *
     * @return \Zend\Form\Form
     */
    public function getAddWorkerForm()
    {
        if (null === $this->addWorkerForm)
            $this->addWorkerForm = $this->getServiceManager()->get('worker_add_form');
        return $this->addWorkerForm;
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