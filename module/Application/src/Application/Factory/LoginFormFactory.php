<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 16/3/2014
 * Time: 8:15 μμ
 */

namespace Application\Factory;


use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\InputFilter\InputFilter;
use Application\Form\LoginFieldset;
use Application\Form\LoginForm;
use Admin\Entity\Admin;

use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;

class LoginFormFactory implements FactoryInterface{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $entityManager = $serviceLocator->get('Doctrine\ORM\EntityManager');
        $fieldset = new LoginFieldset($serviceLocator->get('translator'));
        $form = new LoginForm();
        $hydrator = new DoctrineHydrator($entityManager, '\Admin\Entity\Admin');

        $fieldset->setUseAsBaseFieldset(true)
            ->setHydrator($hydrator)
            ->setObject(new Admin);

        $form->add($fieldset)
            ->setInputFilter(new InputFilter())
            ->setHydrator($hydrator);

        return $form;
    }

} 