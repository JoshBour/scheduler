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
use Schedule\Form\AddEntryFieldset;
use Schedule\Form\AddEntryForm;
use Schedule\Entity\Entry;

use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;

class EntryAddFormFactory implements FactoryInterface{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $entityManager
         */
        $entityManager = $serviceLocator->get('Doctrine\ORM\EntityManager');
        $formManager = $serviceLocator->get('FormElementManager');
        /**
         * @var AddEntryFieldset $fieldset
         */
        $fieldset        = $formManager->get('Schedule\Form\AddEntryFieldset');
        $form = new AddEntryForm();
        $hydrator = new DoctrineHydrator($entityManager, '\Schedule\Entity\Entry');

        $fieldset->setUseAsBaseFieldset(true)
            ->setHydrator($hydrator)
            ->setObject(new Entry);

        $form->add($fieldset)
            ->setInputFilter(new InputFilter())
            ->setHydrator($hydrator);
        return $form;
    }

} 