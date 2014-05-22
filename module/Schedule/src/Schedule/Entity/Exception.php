<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 19/5/2014
 * Time: 8:56 μμ
 */

namespace Schedule\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Exception
 * @package Schedule\Entity
 * @ORM\Entity
 * @ORM\Table(name="exceptions")
 */
class Exception {
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer", length=3, name="exception_id")
     */
    private $exceptionId;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $name;

    /**
     * @ORM\Column(type="datetime", name="referenced_date", nullable=true)
     */
    private $referencedDate;

    /**
     * @ORM\OneToMany(targetEntity="Entry", mappedBy="exception")
     */
    private $relatedEntries;

    public function __construct(){
        $this->relatedEntries = new ArrayCollection();
    }

    /**
     * @param mixed $exceptionId
     */
    public function setExceptionId($exceptionId)
    {
        $this->exceptionId = $exceptionId;
    }

    /**
     * @return mixed
     */
    public function getExceptionId()
    {
        return $this->exceptionId;
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
     * @param mixed $referencedDate
     */
    public function setReferencedDate($referencedDate)
    {
        if(!($referencedDate instanceof \DateTime) && !empty($referencedDate))
            $referencedDate = new \DateTime($referencedDate);
        $this->referencedDate = $referencedDate;
    }

    /**
     * @return mixed
     */
    public function getReferencedDate()
    {
        return $this->referencedDate;
    }

    /**
     * @param mixed $relatedEntries
     */
    public function setRelatedEntries($relatedEntries)
    {
        $this->relatedEntries = $relatedEntries;
    }

    /**
     * @return mixed
     */
    public function getRelatedEntries()
    {
        return $this->relatedEntries;
    }


} 