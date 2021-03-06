<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 19/5/2014
 * Time: 6:42 μμ
 */

namespace Admin\Service;


use Admin\Entity\Admin as AdminEntity;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceManagerAwareInterface;

class Admin implements ServiceManagerAwareInterface
{
    /**
     * The admin repository
     *
     * @var \Doctrine\ORM\EntityRepository
     */
    private $adminRepository;

    /**
     * The entity manager
     *
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * The service manager
     *
     * @var ServiceManager
     */
    private $serviceManager;

    /**
     * Create a new admin
     *
     * @param array $data
     * @param \Zend\Form\Form $form
     * @return bool
     */
    public function create($data, &$form)
    {
        $admin = new AdminEntity();
        $em = $this->getEntityManager();

        $form->bind($admin);
        $form->setData($data);
        if (!$form->isValid()) return false;
        $admin->setPassword(AdminEntity::getHashedPassword($admin->getPassword()));
        $admin->setCreateTime("now");
        try {
            $em->persist($admin);
            $em->flush();
            return true;
        } catch (\Exception $e) {
            echo $e->getMessage();
            return false;
        }
    }

    /**
     * Update and save the admins
     *
     * @param $entities
     * @return bool
     */
    public function save($entities)
    {
        $em = $this->getEntityManager();
        $adminRepository = $this->getAdminRepository();
        foreach ($entities as $entity) {
            $admin = $adminRepository->find($entity['AdminId']);
            array_shift($entity);
            $admin->setUsername($entity['Username']);
            if ($entity['Password'] != '**********')
                $admin->setPassword(AdminEntity::getHashedPassword($entity['Password']));
            if (!empty($entity['RelatedWorker']))
                $admin->setRelatedWorker($entity['RelatedWorker']);
            $em->persist($admin);
        }
        try {
            $em->flush();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Remove an admin from the database.
     *
     * @param int $id
     * @return bool
     */
    public function remove($id)
    {
        $em = $this->getEntityManager();
        $admin = $this->getAdminRepository()->find($id);
        try {
            $em->remove($admin);
            $em->flush();
        } catch (\Exception $e) {
            return false;
        }
        return true;
    }

    /**
     * Get the admin repository
     *
     * @return \Doctrine\ORM\EntityRepository
     */
    public function getAdminRepository()
    {
        if (null === $this->adminRepository)
            $this->adminRepository = $this->getEntityManager()->getRepository('Admin\Entity\Admin');
        return $this->adminRepository;
    }

    /**
     * Get the doctrine entity manager
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        if (null === $this->entityManager)
            $this->entityManager = $this->getServiceManager()->get('Doctrine\ORM\EntityManager');
        return $this->entityManager;
    }

    /**
     * Set service manager
     *
     * @param ServiceManager $serviceManager
     */
    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
    }

    /**
     * Get service manager instance
     *
     * @return ServiceManager
     */
    public function getServiceManager()
    {
        return $this->serviceManager;
    }

} 