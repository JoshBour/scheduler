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

    /**
     * The changelog service
     *
     * @var Changelog
     */
    private $changelogService;

    /**
     * The entity manager
     *
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * The entry repository
     *
     * @var \Schedule\Repository\EntryRepository
     */
    private $entryRepository;

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
     * The worker repository
     *
     * @var \Worker\Repository\WorkerRepository
     */
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

    /**
     * Update and save the entities
     *
     * @param array $entities
     * @return bool
     */
    public function save($entities)
    {
        $entryRepository = $this->getEntryRepository();
        $workerRepository = $this->getWorkerRepository();
        $em = $this->getEntityManager();
        foreach ($entities as $entity) {
            $entryId = $entity["entryId"];
            $startTime = trim($entity['startTime']);
            $endTime = trim($entity['endTime']);
            $exception = trim($entity['exception']);
            if (!empty($entryId) && empty($startTime) && empty($endTime) && empty($exception)) {
                $this->remove($entryId, false);
            } else {
                $entry = empty($entryId) ? new EntryEntity() : $entryRepository->find($entity["entryId"]);
                $oldEntry = clone $entry;
                $worker = $workerRepository->find($entity["workerId"]);
                if (!empty($exception)) {
                    $exception = $this->getExceptionRepository()->findOneBy(array('name' => $exception));
                    $entry->setException($exception);
                }
                $entry->setWorker($worker);
                if (empty($startTime)) {
                    $entry->setStartTime(null);
                } else {
                    $entry->setStartTime($startTime);
                }
                if (empty($endTime)) {
                    $entry->setEndTime(null);
                } else {
                    $entry->setEndTime($endTime);
                }
                $em->persist($entry);
                if ($changelog = $this->getChangelogService()->create($oldEntry, $entry)) {
                    $em->persist($changelog);
                }
            }
        }
        try {
            $em->flush();
            return true;
        } catch (\Exception $e) {
            echo $e->getMessage();
            return false;
        }
    }

    /**
     * Remove an entry
     *
     * @param int $entryId
     * @param bool $flush
     * @return bool
     */
    public function remove($entryId, $flush = true)
    {
        $em = $this->getEntityManager();
        $entry = $this->getEntryRepository()->find($entryId);
        try {
            $em->remove($entry);
            if ($flush)
                $em->flush();
        } catch (\Exception $e) {
            return false;
        }
        return true;
    }

    /**
     * Get the changelog service
     *
     * @return Changelog
     */
    public function getChangelogService()
    {
        if (null === $this->changelogService)
            $this->changelogService = $this->getServiceManager()->get('changelog_service');
        return $this->changelogService;
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
     * @return \Worker\Repository\WorkerRepository
     */
    public function getWorkerRepository()
    {
        if (null === $this->workerRepository)
            $this->workerRepository = $this->getEntityManager()->getRepository('Worker\Entity\Worker');
        return $this->workerRepository;
    }
} 