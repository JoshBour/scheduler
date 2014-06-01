<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 19/5/2014
 * Time: 6:10 μμ
 */

namespace Schedule\Controller;


use Application\Controller\BaseController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class ExceptionController extends BaseController
{

    /**
     * The add exception form
     *
     * @var \Zend\Form\Form
     */
    private $addExceptionForm;

    /**
     * The exception repository
     *
     * @var \Doctrine\ORM\EntityRepository
     */
    private $exceptionRepository;

    /**
     * The exception service
     *
     * @var \Schedule\Service\Exception
     */
    private $exceptionService;

    /**
     * The exception list action
     * Route: /exceptions
     * Requires login
     *
     * @return array|ViewModel
     */
    public function listAction()
    {
        if ($this->identity()) {
            $exceptions = $this->getExceptionRepository()->findAll();
            return new ViewModel(array(
                "exceptions" => $exceptions,
                "hideForm" => true,
                "form" => $this->getAddExceptionForm(),
                "bodyClass" => 'exceptionPage'
            ));
        } else {
            return $this->notFoundAction();
        }
    }

    /**
     * The exception save action
     * Route: /exceptions/save
     * Only accessible via xmlHttpRequest
     * Requires login
     *
     * @return array|JsonModel
     */
    public function saveAction()
    {
        if ($this->getRequest()->isXmlHttpRequest() && $this->identity()) {
            $success = 1;
            $message = $this->getTranslator()->translate($this->vocabulary["MESSAGE_EXCEPTIONS_SAVED"]);
            $entities = $this->params()->fromPost('entities');
            if (!$this->getExceptionService()->save($entities)) {
                $success = 0;
                $message = $this->getTranslator()->translate($this->vocabulary["ERROR_EXCEPTIONS_NOT_SAVED"]);
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
     * The add exception action
     * Route: /exceptions/add
     * Only accessible via xmlHttpRequest
     * Requires login
     *
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
            $service = $this->getExceptionService();
            $data = $request->getPost();
            if ($request->isPost()) {
                $form = $this->getAddExceptionForm();
                $worker = $service->create($data, $form);
                if ($worker) {
                    $this->flashMessenger()->addMessage($this->translate($this->vocabulary["MESSAGE_EXCEPTION_CREATED"]));
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
     * The exception remove action
     * Route: /exceptions/remove
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
            $message = $this->translate($this->vocabulary["MESSAGE_EXCEPTION_REMOVED"]);
            if ($this->getExceptionService()->remove($id)) {
                $success = 1;
            } else {
                $message = $this->translate($this->vocabulary["ERROR_EXCEPTION_REMOVE"]);
            }
            return new JsonModel(array(
                "success" => $success,
                "message" => $message
            ));
        }
        return $this->notFoundAction();
    }

    /**
     * Get the add exception form
     *
     * @return \Zend\Form\Form
     */
    public function getAddExceptionForm()
    {
        if (null === $this->addExceptionForm)
            $this->addExceptionForm = $this->getServiceLocator()->get('exception_add_form');
        return $this->addExceptionForm;
    }

    /**
     * Get the exception repository
     *
     * @return \Doctrine\ORM\EntityRepository
     */
    public function getExceptionRepository()
    {
        if (null === $this->exceptionRepository)
            $this->exceptionRepository = $this->entityManager->getRepository('Schedule\Entity\Exception');
        return $this->exceptionRepository;
    }

    /**
     * Get the exception service
     *
     * @return \Schedule\Service\Exception
     */
    public function getExceptionService()
    {
        if (null === $this->exceptionService)
            $this->exceptionService = $this->getServiceLocator()->get('exception_service');
        return $this->exceptionService;
    }
}