<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 12/5/2014
 * Time: 5:02 μμ
 */

namespace Worker\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Worker
 * @package Application\Entity
 * @ORM\Entity
 * @ORM\Table(name="workers")
 */
class Worker {

    const HASH_SALT = 'schedulerbour';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer", length=20, name="worker_id")
     */
    private $workerId;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=128)
     */
    private $password;

    /**
     * @ORM\OneToMany(targetEntity="\Schedule\Entity\Entry", mappedBy="worker")
     */
    private $entries;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $surname;

    /**
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    private $address;

    /**
     * @ORM\Column(type="string", length=50, name="first_telephone", nullable=true)
     */
    private $firstTelephone;

    /**
     * @ORM\Column(type="string", length=50, name="second_telephone", nullable=true)
     */
    private $secondTelephone;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $position;

    /**
     * @ORM\Column(type="datetime", name="hire_date", nullable=true)
     */
    private $hireDate;

    /**
     * @ORM\Column(type="string", length=10, name="work_hours", nullable=true)
     */
    private $workHours;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $salary;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $notes;

    /**
     * Hash the password.
     *
     * @param string $password
     * @return string
     */
    public static function getHashedPassword($password)
    {
        return crypt($password . self::HASH_SALT);
    }

    /**
     * Check if the user's password is the same as the provided one.
     *
     * @param Worker $worker
     * @param string $password
     * @return bool
     */
    public static function hashPassword($worker, $password)
    {
        return ($worker->getPassword() === crypt($password . self::HASH_SALT, $worker->getPassword()));
    }

    public function __construct(){
        $this->entries = new ArrayCollection();
    }

    public function getFullName(){
        return $this->name . " " . $this->surname;
    }

    /**
     * @param mixed $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    /**
     * @return mixed
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $entries
     */
    public function setEntries($entries)
    {
        $this->entries = $entries;
    }

    /**
     * @return mixed
     */
    public function getEntries()
    {
        return $this->entries;
    }

    /**
     * @param mixed $firstTelephone
     */
    public function setFirstTelephone($firstTelephone)
    {
        $this->firstTelephone = $firstTelephone;
    }

    /**
     * @return mixed
     */
    public function getFirstTelephone()
    {
        return $this->firstTelephone;
    }

    /**
     * @param mixed $hireDate
     */
    public function setHireDate($hireDate)
    {
        if(!($hireDate instanceof \DateTime)){
            $hireDate = new \DateTime($hireDate);
        }
        $this->hireDate = $hireDate;
    }

    /**
     * @return mixed
     */
    public function getHireDate()
    {
        return $this->hireDate;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $notes
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;
    }

    /**
     * @return mixed
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password)
    {
        $this->password = self::getHashedPassword($password);
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $position
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }

    /**
     * @return mixed
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param mixed $salary
     */
    public function setSalary($salary)
    {
        $this->salary = $salary;
    }

    /**
     * @return mixed
     */
    public function getSalary()
    {
        return $this->salary;
    }

    /**
     * @param mixed $secondTelephone
     */
    public function setSecondTelephone($secondTelephone)
    {
        $this->secondTelephone = $secondTelephone;
    }

    /**
     * @return mixed
     */
    public function getSecondTelephone()
    {
        return $this->secondTelephone;
    }

    /**
     * @param mixed $surname
     */
    public function setSurname($surname)
    {
        $this->surname = $surname;
    }

    /**
     * @return mixed
     */
    public function getSurname()
    {
        return $this->surname;
    }

    /**
     * @param mixed $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param mixed $workHours
     */
    public function setWorkHours($workHours)
    {
        $this->workHours = $workHours;
    }

    /**
     * @return mixed
     */
    public function getWorkHours()
    {
        return $this->workHours;
    }

    /**
     * @param mixed $workerId
     */
    public function setWorkerId($workerId)
    {
        $this->workerId = $workerId;
    }

    /**
     * @return mixed
     */
    public function getWorkerId()
    {
        return $this->workerId;
    }



} 