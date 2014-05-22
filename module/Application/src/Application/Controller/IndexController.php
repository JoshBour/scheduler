<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

class IndexController extends AbstractActionController
{
    private $entityManager;

    private $workerRepository;

    public function indexAction()
    {
        return $this->redirect()->toRoute('schedule');
//        $workers = $this->getWorkerRepository()->findAll();
//        return new ViewModel(array(
//            "workers" => $workers
//        ));
    }
	
    public function testAction()
    {
        return new ViewModel();
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager(){
        if(null === $this->entityManager)
            $this->entityManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        return $this->entityManager;
    }

    /**
     * @return \Doctrine\ORM\EntityRepository
     */
    public function getWorkerRepository(){
        if(null == $this->workerRepository)
            $this->workerRepository = $this->getEntityManager()->getRepository('Worker\Entity\Worker');
        return $this->workerRepository;
    }
}
