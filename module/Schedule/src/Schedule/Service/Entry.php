<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 19/5/2014
 * Time: 8:52 μμ
 */

namespace Schedule\Service;


use Schedule\Entity\Entry as EntryEntity;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceManagerAwareInterface;

class Entry implements ServiceManagerAwareInterface
{

    private $addEntryForm;

    private $entityManager;

    private $entryRepository;

    private $exceptionRepository;

    private $serviceManager;

    private $workerRepository;

    /**
     * Create a new entry
     *
     * @param array $data
     * @param \Zend\Form\Form $form
     * @return bool
     */
    public function create($data, &$form)
    {
        $entry = new EntryEntity();
        $em = $this->getEntityManager();

        $form->bind($entry);
        $form->setData($data);
        if (!$form->isValid()) return false;
        $worker = $this->getWorkerRepository()->find($data['entry']['worker']);
        $entry->setWorker($worker);
        if (!empty($data['entry']['exception']) && $data['entry']['exception'] != "None") {
            $entry->setException($this->getExceptionRepository()->find($data['entry']['exception']));
        } else {
            $entry->setException(null);
        }
        try {
            $em->persist($entry);
            $em->flush();
            return true;
        } catch (\Exception $e) {
            echo $e->getMessage();
            return false;
        }
    }

    public function save($entities)
    {
        $entryRepository = $this->getEntryRepository();
        $workerRepository = $this->getWorkerRepository();
        $em = $this->getEntityManager();
        foreach ($entities as $entity) {
         #   var_dump($entity);
            $entry = $entryRepository->find($entity["entryId"]);
//            $worker = $workerRepository->findOneBy(array('surname'=>$entity["surname"]));
            if (!empty($entity["exception"])) {
                $exception = $this->getExceptionRepository()->findOneBy(array('name' => $entity["exception"]));
                $entry->setException($exception);
            }
            $entry->setStartTime($entity["startTime"]);
            $entry->setEndTime($entity["endTime"]);
            $entry->setTotalTime($entity["totalTime"]);
            $entry->setExtraTime($entity["extraTime"]);
            $em->persist($entry);
        }
        try {
            $em->flush();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get the add entry form
     *
     * @return \Zend\Form\Form
     */
    public function getAddEntryForm()
    {
        if (null === $this->addEntryForm)
            $this->addEntryForm = $this->getServiceManager()->get('entry_add_form');
        return $this->addEntryForm;
    }

    /**
     * Get the entry repository
     *
     * @return \Schedule\Repository\EntryRepository
     */
    public function getEntryRepository()
    {
        if (null === $this->entryRepository)
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
    public function getExceptionRepository()
    {
        if (null === $this->exceptionRepository)
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