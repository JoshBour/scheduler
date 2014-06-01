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
    /**
     * The entity manager
     *
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * The exception repository
     *
     * @var \Doctrine\ORM\EntityRepository
     */
    private $exceptionRepository;

    /**
     * The service manager
     *
     * @var ServiceManager
     */
    private $serviceManager;

    /**
     * Create a new exception
     *
     * @param array $data
     * @param \Zend\Form\Form $form
     * @return bool
     */
    public function create($data,&$form){
        $exception = new ExceptionEntity();
        $em = $this->getEntityManager();

        $form->bind($exception);
        $form->setData($data);
        if (!$form->isValid()) return false;
        $exception->setColor(ExceptionEntity::$colors[$exception->getColor()]);
        try {
            $em->persist($exception);
            $em->flush();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Update and save the exceptions
     *
     * @param array $entities
     * @return bool
     */
    public function save($entities){
        $em = $this->getEntityManager();
        $exceptionRepository = $this->getExceptionRepository();
        foreach ($entities as $entity) {
            $exception = $exceptionRepository->find($entity['ExceptionId']);
            array_shift($entity);
            foreach ($entity as $key => $value) {
                if (empty($value)) $value = null;
                $exception->{'set' . $key}($value);
            }
            $em->persist($exception);
        }
        try {
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

} 