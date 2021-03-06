<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 18/5/2014
 * Time: 3:58 μμ
 */

namespace Schedule\Form;


use Zend\Form\Form;

class AddEntryForm extends Form{
    /**
     * The add entry form constructor
     */
    public function __construct(){
        parent::__construct('addEntryForm');

        $this->setAttributes(array(
           'method' => 'post',
            'class' => 'standardForm'
        ));

        $this->add(array(
            'name' => 'security',
            'type' => 'Zend\Form\Element\Csrf'
        ));

        $this->add(array(
            'name' => 'submit',
            'type' => 'submit'
        ));

        $this->setValidationGroup(array(
            'security',
            'entry' => array(
                'entryId',
                'worker',
                'exception',
                'startTime',
                'endTime',
            )
        ));
    }
} 