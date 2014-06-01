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
class Exception
{
    public static $colors = array(
        '0' => 'aqua', '1' => 'black', '2' => 'blue', '3' => 'fuchsia',
        '4' => 'gray', '5' => 'green', '6' => 'lime', '7' => 'maroon',
        '8' => 'navy', '9' => 'olive', '10' => 'orange', '11' => 'purple',
        '12' => 'red', '13' => 'silver', '14' => 'teal', '15' => 'white', '16' => 'yellow');

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
     * @ORM\Column(type="string", length=20)
     */
    private $color;

    /**
     * @ORM\OneToMany(targetEntity="Entry", mappedBy="exception")
     */
    private $relatedEntries;

    public function __construct()
    {
        $this->relatedEntries = new ArrayCollection();
    }

    /**
     * @param mixed $color
     */
    public function setColor($color)
    {
        $this->color = $color;
    }

    /**
     * @return mixed
     */
    public function getColor()
    {
        return $this->color;
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