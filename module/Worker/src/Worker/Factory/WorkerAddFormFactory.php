<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 19/5/2014
 * Time: 4:54 μμ
 */

namespace Worker\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\InputFilter\InputFilter;
use Worker\Form\AddWorkerFieldset;
use Worker\Form\AddWorkerForm;
use Worker\Entity\Worker;

use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;

class WorkerAddFormFactory implements FactoryInterface{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $entityManager = $serviceLocator->get('Doctrine\ORM\EntityManager');
        $fieldset = new AddWorkerFieldset($serviceLocator->get('translator'));
        $form = new AddWorkerForm();
        $hydrator = new DoctrineHydrator($entityManager, '\Worker\Entity\Worker');

        $fieldset->setUseAsBaseFieldset(true)
            ->setHydrator($hydrator)
            ->setObject(new Worker);

        $form->add($fieldset)
            ->setInputFilter(new InputFilter())
            ->setHydrator($hydrator);
        return $form;
    }

} 