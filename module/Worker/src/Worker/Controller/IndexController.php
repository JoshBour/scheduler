<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 19/5/2014
 * Time: 6:10 μμ
 */

namespace Worker\Controller;


use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
    const MESSAGE_WORKER_CREATED = "The worker has been created successfully.";
    const MESSAGE_WORKER_REMOVED = "The worker has been removed successfully.";
    const MESSAGE_WORKER_SAVED = "The workers have been saved successfully";
    const ERROR_WORKER_NOT_REMOVED = "There was a problem when removing the worker, please try again.";
    const ERROR_WORKER_NOT_SAVED = "Something went wrong when saving the workers, please try again.";
    const ROUTE_WORKER_LIST = "workers";
    const CONTROLLER_NAME = 'Worker\Controller\Index';

    private $addWorkerForm;

    private $entityManager;

    private $translator;

    private $workerRepository;

    private $workerService;

    public function listAction()
    {
        $workers = $this->getWorkerRepository()->findAll();
        $hideForm = false;
        if (!$form = $this->params()->fromQuery('form')) {
            $form = $this->getAddWorkerForm();
            $hideForm = true;
        }
        return new ViewModel(array(
            "workers" => $workers,
            "form" => $form,
            "hideForm" => $hideForm
        ));
    }

    public function saveAction()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            $success = 1;
            $message = self::MESSAGE_WORKER_SAVED;
            $entities = $this->params()->fromPost('entities');
            $em = $this->getEntityManager();
            $workerRepository = $this->getWorkerRepository();
            foreach ($entities as $entity) {
                $worker = $workerRepository->find($entity['WorkerId']);
                array_shift($entity);
                foreach ($entity as $key => $value) {
                    if (!empty($value))
                        $worker->{'set' . $key}($value);
                }
                $em->persist($worker);
            }
            try {
                $em->flush();
            } catch (\Exception $e) {
                $success = 0;
                $message = self::ERROR_WORKER_NOT_SAVED;
            }
            return new JsonModel(array(
                "success" => $success,
                "message" => $message
            ));
        } else {
            return $this->notFoundAction();
        }
    }

    public function addAction()
    {
        /**
         * @var $request \Zend\Http\Request
         */
        $request = $this->getRequest();
        if ($request->isXmlHttpRequest()) {
            $service = $this->getWorkerService();
            $data = $request->getPost();
            if ($request->isPost()) {
                $form = $this->getAddWorkerForm();
                $worker = $service->create($data, $form);
                if ($worker) {
                    $this->flashMessenger()->addMessage($this->getTranslator()->translate(static::MESSAGE_WORKER_CREATED));
                    return new JsonModel(array('redirect' => true));
                } else {
                    $viewModel = new ViewModel(array("form" => $form));
                    $viewModel->setTerminal(true);
                    return $viewModel;
                }
            }
        }
        return $this->notFoundAction();
    }

    public function removeAction()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            $id = $this->params()->fromPost("id");
            $success = 0;
            $message = self::MESSAGE_WORKER_REMOVED;
            if ($this->getWorkerService()->remove($id)) {
                $success = 1;
            } else {
                $message = "There was a problem when removing the worker, please try again.";
            }
            return new JsonModel(array(
                "success" => $success,
                "message" => $message
            ));
        }
        return $this->notFoundAction();
    }

    /**
     * Get the add worker form
     *
     * @return \Zend\Form\Form
     */
    public function getAddWorkerForm()
    {
        if (null == $this->addWorkerForm)
            $this->addWorkerForm = $this->getServiceLocator()->get('worker_add_form');
        return $this->addWorkerForm;
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        if (null === $this->entityManager)
            $this->entityManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        return $this->entityManager;
    }

    /**
     * @return \Zend\I18n\Translator\Translator
     */
    public function getTranslator()
    {
        if (null === $this->translator)
            $this->translator = $this->getServiceLocator()->get('translator');
        return $this->translator;
    }

    /**
     * @return \Doctrine\ORM\EntityRepository
     */
    public function getWorkerRepository()
    {
        if (null == $this->workerRepository)
            $this->workerRepository = $this->getEntityManager()->getRepository('Worker\Entity\Worker');
        return $this->workerRepository;
    }

    /**
     * Gets the worker service.
     *
     * @return \Worker\Service\Worker
     */
    public function getWorkerService()
    {
        if (null == $this->workerService)
            $this->workerService = $this->getServiceLocator()->get('worker_service');
        return $this->workerService;
    }
} 