<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 19/5/2014
 * Time: 4:54 μμ
 */

namespace Schedule\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\InputFilter\InputFilter;
use Schedule\Form\AddExceptionForm;
use Schedule\Form\AddExceptionFieldset;
use Schedule\Entity\Exception;

use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;

class ExceptionAddFormFactory implements FactoryInterface{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $entityManager = $serviceLocator->get('Doctrine\ORM\EntityManager');
        $fieldset = new AddExceptionFieldset($serviceLocator->get('translator'),$entityManager);
        $form = new AddExceptionForm();
        $hydrator = new DoctrineHydrator($entityManager, '\Schedule\Entity\Exception');

        $fieldset->setUseAsBaseFieldset(true)
            ->setHydrator($hydrator)
            ->setObject(new Exception);

        $form->add($fieldset)
            ->setInputFilter(new InputFilter())
            ->setHydrator($hydrator);
        return $form;
    }

} 