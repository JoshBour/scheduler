<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 18/5/2014
 * Time: 3:58 μμ
 */

namespace Schedule\Form;


use Zend\Form\Form;

class AddExceptionForm extends Form{
    public function __construct(){
        parent::__construct('addExceptionForm');

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
            'exception' => array(
                'name',
                'color',
            )
        ));
    }
} 