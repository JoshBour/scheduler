<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 25/5/2014
 * Time: 11:11 μμ
 */

namespace Schedule\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Changelogs
 * @package Schedule\Entity
 * @ORM\Entity(repositoryClass="\Schedule\Repository\ChangelogRepository")
 * @ORM\Table(name="changelogs")
 */
class Changelog {
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer", length=11, name="changelog_id")
     */
    private $changelogId;

    /**
     * @ORM\OneToOne(targetEntity="Worker\Entity\Worker")
     * @ORM\JoinColumn(name="worker_id", referencedColumnName="worker_id")
     */
    private $worker;

    /**
     * @ORM\OneToOne(targetEntity="Entry")
     * @ORM\JoinColumn(name="new_entry_id", referencedColumnName="entry_id")
     */
    private $newEntry;

    /**
     * @ORM\Column(type="datetime", name="old_start_time", nullable=true)
     */
    private $oldStartTime;

    /**
     * @ORM\Column(type="datetime", name="old_end_time", nullable=true)
     */
    private $oldEndTime;

    /**
     * @ORM\OneToOne(targetEntity="Exception")
     * @ORM\JoinColumn(name="old_exception_id", referencedColumnName="exception_id", nullable=true)
     */
    private $oldException;

    /**
     * @ORM\OneToOne(targetEntity="Admin\Entity\Admin")
     * @ORM\JoinColumn(name="admin_id", referencedColumnName="admin_id")
     */
    private $admin;

    /**
     * @ORM\Column(type="datetime", name="change_time")
     */
    private $changeTime;

    /**
     * @param mixed $admin
     */
    public function setAdmin($admin)
    {
        $this->admin = $admin;
    }

    /**
     * @return mixed
     */
    public function getAdmin()
    {
        return $this->admin;
    }

    /**
     * @param mixed $changeTime
     */
    public function setChangeTime($changeTime)
    {
        $this->changeTime = $changeTime;
    }

    /**
     * @return mixed
     */
    public function getChangeTime()
    {
        return $this->changeTime;
    }

    /**
     * @param mixed $changelogId
     */
    public function setChangelogId($changelogId)
    {
        $this->changelogId = $changelogId;
    }

    /**
     * @return mixed
     */
    public function getChangelogId()
    {
        return $this->changelogId;
    }

    /**
     * @param mixed $newEntry
     */
    public function setNewEntry($newEntry)
    {
        $this->newEntry = $newEntry;
    }

    /**
     * @return mixed
     */
    public function getNewEntry()
    {
        return $this->newEntry;
    }

    /**
     * @param mixed $oldEndTime
     */
    public function setOldEndTime($oldEndTime)
    {
        $this->oldEndTime = $oldEndTime;
    }

    /**
     * @return mixed
     */
    public function getOldEndTime()
    {
        return $this->oldEndTime;
    }

    /**
     * @param mixed $oldException
     */
    public function setOldException($oldException)
    {
        $this->oldException = $oldException;
    }

    /**
     * @return mixed
     */
    public function getOldException()
    {
        return $this->oldException;
    }

    /**
     * @param mixed $oldStartTime
     */
    public function setOldStartTime($oldStartTime)
    {
        $this->oldStartTime = $oldStartTime;
    }

    /**
     * @return mixed
     */
    public function getOldStartTime()
    {
        return $this->oldStartTime;
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