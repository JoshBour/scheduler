<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 19/5/2014
 * Time: 8:52 μμ
 */

namespace Schedule\Service;


use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceManagerAwareInterface;
use Schedule\Entity\Exception as ExceptionEntity;

class Exception implements ServiceManagerAwareInterface{

    private $addExceptionForm;

    private $entityManager;

    private $entryRepository;

    private $exceptionRepository;

    private $serviceManager;

    private $workerRepository;

    /**
     * Create a new exception
     *
     * @param array $data
     * @param \Zend\Form\Form $form
     * @return bool
     */
    public function create($data,&$form){
        $entry = new ExceptionEntity();
        $em = $this->getEntityManager();

        $form->bind($entry);
        $form->setData($data);
        if (!$form->isValid()) return false;
        if(empty($data['exception']['referencedDate'])){
            $entry->setReferencedDate(null);
        }
        try {
            $em->persist($entry);
            $em->flush();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Remove an exception
     *
     * @param int $id
     * @return bool
     */
    public function remove($id){
        $em = $this->getEntityManager();
        $exception = $this->getExceptionRepository()->find($id);
        try {
            $em->remove($exception);
            $em->flush();
        } catch (\Exception $e) {
            return false;
        }
        return true;
    }

    /**
     * Get the add exception form
     *
     * @return \Zend\Form\Form
     */
    public function getAddExceptionForm()
    {
        if (null === $this->addExceptionForm)
            $this->addExceptionForm = $this->getServiceManager()->get('exception_add_form');
        return $this->addExceptionForm;
    }

    /**
     * Get the entry repository
     *
     * @return \Schedule\Repository\EntryRepository
     */
    public function getEntryRepository(){
        if(null === $this->entryRepository)
            $this->entryRepository = $this->getEntityManager()->getRepository('Schedule\Entity\Entry');
        return $this->entryRepository;
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
     * Get the exception repository
     *
     * @return \Doctrine\ORM\EntityRepository
     */
    public function getExceptionRepository(){
        if(null === $this->exceptionRepository)
            $this->exceptionRepository = $this->getEntityManager()->getRepository('Schedule\Entity\Exception');
        return $this->exceptionRepository;
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