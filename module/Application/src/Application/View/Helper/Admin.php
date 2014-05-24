<?php
/**
 * User: Josh
 * Date: 12/9/2013
 * Time: 7:14 μμ
 */

namespace Admin\View\Helper;

use Zend\View\Helper\AbstractHelper;

class Admin extends AbstractHelper
{

    private $serviceManager;

    private $entityManager;

    private $authService;

    public function __invoke(){
        return $this->getAccount();
    }

    /**
     * @return bool|\Admin\Entity\Admin
     */
    public function getAccount(){
        $em = $this->getEntityManager();
        $auth = $this->getAuthService();
        if($auth->hasIdentity()){
            $account = $em->getRepository('Account\Entity\Account')->find($auth->getIdentity()->getAccountId());
        }else{
            $account = false;
        }
        return $account;
    }

    public function getAuthService()
    {
        if (null === $this->authService)
            $this->authService = $this->getServiceManager()->get('auth_service');
        return $this->authService;
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        if (null === $this->entityManager)
            $this->entityManager = $this->getServiceManager()->get('Doctrine\ORM\EntityManager');
        return $this->entityManager;
    }

    public function setServiceManager($sm)
    {
        $this->serviceManager = $sm;
    }

    /**
     * @return \Zend\ServiceManager\ServiceManager
     */
    public function getServiceManager()
    {
        return $this->serviceManager;
    }
}