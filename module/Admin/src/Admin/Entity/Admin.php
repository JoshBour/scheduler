<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 24/5/2014
 * Time: 9:35 μμ
 */

namespace Admin\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Admin
 * @package Admin\Entity
 * @ORM\Entity
 * @ORM\Table(name="admins")
 */
class Admin {

    const HASH_SALT = 'schedulerbour';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer", length=5, name="admin_id")
     */
    private $adminId;

    /**
     * @ORM\Column(type="datetime", name="create_time", nullable=true)
     */
    private $createTime;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=128)
     */
    private $password;

    /**
     * @ORM\OneToOne(targetEntity="\Worker\Entity\Worker")
     * @ORM\JoinColumn(name="related_worker", referencedColumnName="worker_id", nullable=true)
     */
    private $relatedWorker;

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
     * @param Admin $admin
     * @param string $password
     * @return bool
     */
    public static function hashPassword($admin, $password)
    {
        return ($admin->getPassword() === crypt($password . self::HASH_SALT, $admin->getPassword()));
    }

    /**
     * @param mixed $adminId
     */
    public function setAdminId($adminId)
    {
        $this->adminId = $adminId;
    }

    /**
     * @return mixed
     */
    public function getAdminId()
    {
        return $this->adminId;
    }

    /**
     * @param mixed $createTime
     */
    public function setCreateTime($createTime)
    {
        if(!$createTime instanceof \DateTime){
            $createTime = new \DateTime($createTime);
        }
        $this->createTime = $createTime;
    }

    /**
     * @return mixed
     */
    public function getCreateTime()
    {
        return $this->createTime;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $relatedWorker
     */
    public function setRelatedWorker($relatedWorker)
    {
        $this->relatedWorker = $relatedWorker;
    }

    /**
     * @return mixed
     */
    public function getRelatedWorker()
    {
        return $this->relatedWorker;
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


} 