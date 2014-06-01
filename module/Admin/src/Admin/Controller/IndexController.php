<?php
namespace Admin\Controller;


use Application\Controller\BaseController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class IndexController extends BaseController
{
    const ROUTE_ADMIN_LIST = "admins";
    const CONTROLLER_NAME = 'Admin\Controller\Index';

    /**
     * The add admin form
     *
     * @var \Zend\Form\Form
     */
    private $addAdminForm;

    /**
     * The admin repository
     *
     * @var \Doctrine\ORM\EntityRepository
     */
    private $adminRepository;

    /**
     * The admin repository
     *
     * @var \Doctrine\ORM\EntityRepository
     */
    private $adminService;

    /**
     * The admin list action
     * Route: /admins
     *
     * @return array|ViewModel
     */
    public function listAction()
    {
        if ($this->identity()) {
            $admins = $this->getAdminRepository()->findAll();
            $hideForm = false;
            if (!$form = $this->params()->fromQuery('form')) {
                $form = $this->getAddAdminForm();
                $hideForm = true;
            }
            return new ViewModel(array(
                "admins" => $admins,
                "form" => $form,
                "hideForm" => $hideForm
            ));
        } else {
            return $this->notFoundAction();
        }
    }

    /**
     * The admin save action
     * Only accessible via xmlHttpRequest
     * Requires login
     *
     * @return array|JsonModel
     */
    public function saveAction()
    {
        if ($this->getRequest()->isXmlHttpRequest() && $this->identity()) {
            $success = 1;
            $message = $this->translate($this->vocabulary["MESSAGE_ADMIN_SAVED"]);
            $entities = $this->params()->fromPost('entities');
            if (!$this->getAdminService()->save($entities)) {
                $success = 0;
                $message = $this->translate($this->vocabulary["ERROR_ADMIN_NOT_SAVED"]);
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
     * The admin add action
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
            $service = $this->getAdminService();
            $data = $request->getPost();
            if ($request->isPost()) {
                $form = $this->getAddAdminForm();
                $worker = $service->create($data, $form);
                if ($worker) {
                    $this->flashMessenger()->addMessage($this->translate($this->vocabulary["MESSAGE_ADMIN_CREATED"]));
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
     * The admin remove action
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
            $message = $this->translate($this->vocabulary["MESSAGE_ADMIN_REMOVED"]);
            if ($this->getAdminService()->remove($id)) {
                $success = 1;
            } else {
                $message = $this->translate($this->vocabulary["ERROR_ADMIN_NOT_REMOVED"]);
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
    public function getAddAdminForm()
    {
        if (null == $this->addAdminForm)
            $this->addAdminForm = $this->getServiceLocator()->get('admin_add_form');
        return $this->addAdminForm;
    }

    /**
     * Get the admin repository
     *
     * @return \Doctrine\ORM\EntityRepository
     */
    public function getAdminRepository()
    {
        if (null == $this->adminRepository)
            $this->adminRepository = $this->getEntityManager()->getRepository('Admin\Entity\Admin');
        return $this->adminRepository;
    }

    /**
     * Gets the worker service
     *
     * @return \Admin\Service\Admin
     */
    public function getAdminService()
    {
        if (null == $this->adminService)
            $this->adminService = $this->getServiceLocator()->get('admin_service');
        return $this->adminService;
    }


} 