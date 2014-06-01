<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 19/5/2014
 * Time: 8:52 μμ
 */

namespace Schedule\Service;


use Schedule\Entity\Changelog as ChangelogEntity;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceManagerAwareInterface;

class Changelog implements ServiceManagerAwareInterface
{
    /**
     * The active admin
     *
     * @var \Admin\Entity\Admin
     */
    private $admin;

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
     * Create a new changelog
     *
     * @param \Schedule\Entity\Entry $oldEntry
     * @param \Schedule\Entity\Entry $newEntry
     * @return ChangelogEntity
     */
    public function create($oldEntry, $newEntry)
    {
        $changelog = new ChangelogEntity();
        $changelog->setWorker($newEntry->getWorker());
        $oldEndTime = $oldEntry->getEndTime();
        $oldStartTime = $oldEntry->getStartTime();
        $oldException = $oldEntry->getException();
        if (!($oldStartTime == $newEntry->getStartTime() && $oldEndTime == $newEntry->getEndTime())) {
            if (empty($oldEndTime)) {
                $changelog->setOldEndTime(null);
            } else {
                $changelog->setOldEndTime($oldEndTime);
            }
            if (empty($oldStartTime)) {
                $changelog->setOldStartTime(null);
            } else {
                $changelog->setOldStartTime($oldStartTime);
            }
            if (empty($oldException)) {
                $changelog->setOldException(null);
            } else {
                $changelog->setOldException($oldException);
            }
            $changelog->setNewEntry($newEntry);
            $changelog->setChangeTime(new \DateTime('now'));
            $changelog->setAdmin($this->getAdmin());

            return $changelog;
        }
        return false;
    }

    /**
     * Get the admin repository
     *
     * @return \Admin\Entity\Admin
     */
    public function getAdmin()
    {
        if (null === $this->admin)
            $this->admin = $this->getServiceManager()->get('ControllerPluginManager')->get('admin')->getAdmin();
        return $this->admin;
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