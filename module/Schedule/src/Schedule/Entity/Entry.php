<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 19/5/2014
 * Time: 8:54 μμ
 */

namespace Schedule\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Entry
 * @package Schedule\Entity
 * @ORM\Entity(repositoryClass="\Schedule\Repository\EntryRepository")
 * @ORM\Table(name="entries")
 */
class Entry {

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="bigint", length=20, name="entry_id")
     */
    private $entryId;

    /**
     * @ORM\OneToOne(targetEntity="\Worker\Entity\Worker")
     * @ORM\JoinColumn(name="worker_id", referencedColumnName="worker_id")
     */
    private $worker;

    /**
     * @ORM\OneToOne(targetEntity="Exception")
     * @ORM\JoinColumn(name="exception_id", referencedColumnName="exception_id", nullable=false)
     */
    private $exception;

    /**
     * @ORM\Column(type="datetime", name="start_time")
     */
    private $startTime;

    /**
     * @ORM\Column(type="datetime", name="end_time")
     */
    private $endTime;

    /**
     * @param mixed $endTime
     */
    public function setEndTime($endTime)
    {
        if(!($endTime instanceof \DateTime) && $endTime !== null){
            $endTime = new \DateTime($endTime);
        }
        $this->endTime = $endTime;
    }

    /**
     * @return mixed
     */
    public function getEndTime()
    {
        return $this->endTime;
    }

    /**
     * @param mixed $entryId
     */
    public function setEntryId($entryId)
    {
        $this->entryId = $entryId;
    }

    /**
     * @return mixed
     */
    public function getEntryId()
    {
        return $this->entryId;
    }

    /**
     * @param mixed $exception
     */
    public function setException($exception)
    {
        $this->exception = $exception;
    }

    /**
     * @return mixed
     */
    public function getException()
    {
        return $this->exception;
    }

    /**
     * @param mixed $startTime
     */
    public function setStartTime($startTime)
    {
        if(!($startTime instanceof \DateTime) && $startTime !== null){
            $startTime = new \DateTime($startTime);
        }
        $this->startTime = $startTime;
    }

    /**
     * @return mixed
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * @param mixed $worker
     */
    public function setWorker($worker)
    {
        $this->worker = $worker;
    }

    /**
     * @return mixed
     */
    public function getWorker()
    {
        return $this->worker;
    }


} 