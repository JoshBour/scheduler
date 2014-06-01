<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 1/6/2014
 * Time: 6:09 μμ
 */

namespace Application\Service;

use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceManagerAwareInterface;

class Lang implements ServiceManagerAwareInterface
{
    private $serviceManager;

    private $translator;

    public function getTranslations(){

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
     * @return ServiceManager
     */
    public function getServiceManager(){
        return $this->serviceManager;
    }

    public function getTranslator(){
        if(null === $this->translator)
            $this->translator = $this->getServiceManager()->get('translator');
        return $this->translator;
    }
}