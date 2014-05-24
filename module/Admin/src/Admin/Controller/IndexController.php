<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 19/5/2014
 * Time: 6:10 μμ
 */

namespace Admin\Controller;


use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
    const MESSAGE_ADMIN_CREATED = "The admin has been created successfully.";
    const MESSAGE_ADMIN_REMOVED = "The admin has been removed successfully.";
    const MESSAGE_ADMIN_SAVED = "The admins have been saved successfully";
    const ERROR_ADMIN_NOT_REMOVED = "There was a problem when removing the admin, please try again.";
    const ERROR_ADMIN_NOT_SAVED = "Something went wrong when saving the admins, please try again.";
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
     * The entity manager
     *
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * The zend translator
     *
     * @var \Zend\I18n\Translator\Translator
     */
    private $translator;

    /**
     * The admin list action
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
     *
     * @return array|JsonModel
     */
    public function saveAction()
    {
        if ($this->getRequest()->isXmlHttpRequest() && $this->identity()) {
            $success = 1;
            $message = self::MESSAGE_ADMIN_SAVED;
            $entities = $this->params()->fromPost('entities');
            if (!$this->getAdminService()->save($entities)) {
                $success = 0;
                $message = self::ERROR_ADMIN_NOT_SAVED;
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
                    $this->flashMessenger()->addMessage($this->getTranslator()->translate(static::MESSAGE_ADMIN_CREATED));
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
     *
     * @return array|JsonModel
     */
    public function removeAction()
    {
        if ($this->getRequest()->isXmlHttpRequest() && $this->identity()) {
            $id = $this->params()->fromPost("id");
            $success = 0;
            $message = self::MESSAGE_ADMIN_REMOVED;
            if ($this->getAdminService()->remove($id)) {
                $success = 1;
            } else {
                $message = self::ERROR_ADMIN_NOT_REMOVED;
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
     * @return \Doctrine\ORM\EntityRepository
     */
    public function getAdminRepository()
    {
        if (null == $this->adminRepository)
            $this->adminRepository = $this->getEntityManager()->getRepository('Admin\Entity\Admin');
        return $this->adminRepository;
    }

    /**
     * Gets the worker service.
     *
     * @return \Admin\Service\Admin
     */
    public function getAdminService()
    {
        if (null == $this->adminService)
            $this->adminService = $this->getServiceLocator()->get('admin_service');
        return $this->adminService;
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
} 