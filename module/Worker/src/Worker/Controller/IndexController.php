<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 19/5/2014
 * Time: 6:10 μμ
 */

namespace Worker\Controller;


use Application\Controller\BaseController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class IndexController extends BaseController
{
    const ROUTE_WORKER_LIST = "workers";
    const CONTROLLER_NAME = 'Worker\Controller\Index';

    /**
     * The add worker form
     *
     * @var \Zend\Form\Form
     */
    private $addWorkerForm;

    /**
     * The worker repository
     *
     * @var \Worker\Repository\WorkerRepository
     */
    private $workerRepository;

    /**
     * The worker service
     *
     * @var \Worker\Service\Worker
     */
    private $workerService;

    /**
     * The worker list action
     * Route: /workers
     * Requires login
     *
     * @return array|ViewModel
     */
    public function listAction()
    {
        if ($this->identity()) {
            $workers = $this->getWorkerRepository()->findAll();
            return new ViewModel(array(
                "workers" => $workers,
                "form" => $this->getAddWorkerForm(),
            ));
        } else {
            return $this->notFoundAction();
        }
    }

    /**
     * The workers save action
     * Route: /workers/save
     * Only accessible via xmlHttpRequest
     * Requires login
     *
     * @return array|JsonModel
     */
    public function saveAction()
    {
        if ($this->getRequest()->isXmlHttpRequest() && $this->identity()) {
            $success = 1;
            $message = $this->translate($this->vocabulary["MESSAGE_WORKER_SAVED"]);
            $entities = $this->params()->fromPost('entities');
            if (!$this->getWorkerService()->save($entities)) {
                $success = 0;
                $message = $this->translate($this->vocabulary["ERROR_WORKER_NOT_SAVED"]);
            }
            return new JsonModel(array(
                "success" => $success,
                "message" => $message
            ));
        } else {
            return $this->notFoundAction();
        }
    }

    /**
     * The worker add action
     * Route: /workers/add
     * Only accessible via xmlHttpRequest
     * Requires login
     *
     * @return array|JsonModel|ViewModel
     */
    public function addAction()
    {
        /**
         * @var $request \Zend\Http\Request
         */
        $request = $this->getRequest();
        if ($request->isXmlHttpRequest() && $this->identity()) {
            $service = $this->getWorkerService();
            $data = $request->getPost();
            if ($request->isPost()) {
                $form = $this->getAddWorkerForm();
                $worker = $service->create($data, $form);
                if ($worker) {
                    $this->flashMessenger()->addMessage($this->translate($this->vocabulary["MESSAGE_WORKER_CREATED"]));
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

    /**
     * The worker remove action
     * Route: /workers/remove
     * Only accessible via xmlHttpRequest
     * Requires login
     *
     * @return array|JsonModel
     */
    public function removeAction()
    {
        if ($this->getRequest()->isXmlHttpRequest() && $this->identity()) {
            $id = $this->params()->fromPost("id");
            $success = 0;
            $message = $this->translate($this->vocabulary["MESSAGE_WORKER_REMOVED"]);
            if ($this->getWorkerService()->remove($id)) {
                $success = 1;
            } else {
                $message = $this->translate($this->vocabulary["ERROR_WORKER_NOT_REMOVED"]);
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
     * The worker repository
     *
     * @return \Worker\Repository\WorkerRepository
     */
    public function getWorkerRepository()
    {
        if (null == $this->workerRepository)
            $this->workerRepository = $this->entityManager->getRepository('Worker\Entity\Worker');
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