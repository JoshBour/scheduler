<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 19/5/2014
 * Time: 4:54 μμ
 */

namespace Admin\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\InputFilter\InputFilter;
use Admin\Form\AddAdminFieldset;
use Admin\Form\AddAdminForm;
use Admin\Entity\Admin;

use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;

class AdminAddFormFactory implements FactoryInterface{
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
        $fieldset = new AddAdminFieldset($serviceLocator->get('translator'),$entityManager);
        $form = new AddAdminForm();
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