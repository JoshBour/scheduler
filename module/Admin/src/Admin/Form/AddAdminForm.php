<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 18/5/2014
 * Time: 3:58 μμ
 */

namespace Admin\Form;


use Zend\Form\Form;

class AddAdminForm extends Form{
    public function __construct(){
        parent::__construct('addAdminForm');

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
            'admin' => array(
                'username',
                'password',
                'relatedWorker',
            )
        ));
    }
} 