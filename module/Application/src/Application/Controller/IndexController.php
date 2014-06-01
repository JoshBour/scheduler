<?php
namespace Application\Controller;

use Admin\Entity\Admin;
use Zend\Authentication\AuthenticationService;
use Zend\View\Model\ViewModel;

class IndexController extends BaseController
{
    const CONTROLLER_NAME = "Application\Controller\Index";
    const ROUTE_LOGIN = "login";
    const ROUTE_SCHEDULE = "schedule";

    /**
     * The authentication service.
     *
     * @var AuthenticationService
     */
    private $authService = null;

    /**
     * The authentication storage.
     *
     * @var \Application\Model\AuthStorage
     */
    private $authStorage = null;

    /**
     * The login form
     *
     * @var \Zend\Form\Form
     */
    private $loginForm;

    /**
     * The login action
     * Route: /
     *
     * @return mixed|\Zend\Http\Response|ViewModel
     */
    public function loginAction()
    {
        if (!$this->identity()) {
            $entity = new Admin();
            $loginForm = $this->getLoginForm();
            /**
             * @var $request \Zend\Http\Request
             */
            $request = $this->getRequest();
            $loginForm->bind($entity);
            if ($request->isPost()) {
                $data = $request->getPost();
                $loginForm->setData($data);
                if ($loginForm->isValid()) {
                    $redirectUrl = $this->params()->fromRoute("redirectUrl", null);
                    return $this->forward()->dispatch(static::CONTROLLER_NAME, array('action' => 'authenticate',
                        'username' => $entity->getUsername(),
                        'password' => $entity->getPassword(),
                        'remember' => $data['admin']['remember'],
                        'redirectUrl' => $redirectUrl));
                }
            }
            return new ViewModel(array(
                'form' => $loginForm,
                "pageTitle" => "Admin Login",
                "noAds" => true,
                "hideHeader" => true
            ));
        } else {
            return $this->redirect()->toRoute(self::ROUTE_SCHEDULE);
        }
    }

    /**
     * The authentication action.
     * Only accessed from the login and register actions.
     *
     * @return \Zend\Http\Response
     */
    public function authenticateAction()
    {
        $authService = $this->getAuthenticationService();
        /**
         * @var $adapter \Zend\Authentication\Adapter\AbstractAdapter
         */
        $adapter = $authService->getAdapter();

        $remember = $this->params()->fromRoute('remember', 1);
        $username = $this->params()->fromRoute('username');
        $password = $this->params()->fromRoute('password');
        $redirectUrl = $this->params()->fromRoute('redirectUrl');
        $adapter->setIdentityValue($username);
        $adapter->setCredentialValue($password);
        $authResult = $authService->authenticate();
        if ($authResult->isValid()) {
            if ($remember == 1) {
                $this->getAuthStorage()->setRememberMe(1);
                #  $authService->setStorage($this->getAuthStorage());
            }
            $identity = $authResult->getIdentity();
            $authService->getStorage()->write($identity);
        } else {
            $this->flashMessenger()->addMessage($this->translate($this->vocabulary["MESSAGE_INVALID_CREDENTIALS"]));
            return $this->redirect()->toRoute(self::ROUTE_LOGIN);
        }
        if ($redirectUrl) {
            $redirectUrl = str_replace('__', '/', $redirectUrl);
            return $this->redirect()->toUrl('/' . $redirectUrl);
        } else {
            $this->flashMessenger()->addMessage($this->translate($this->vocabulary["MESSAGE_WELCOME"]) . ', ' . $username);
            return $this->redirect()->toRoute(self::ROUTE_SCHEDULE);
        }
    }

    /**
     * The logout action
     * Route: /logout
     *
     * @return \Zend\Http\Response
     */
    public function logoutAction()
    {
        if ($this->identity()) {
            $this->getAuthStorage()->forgetMe();
            $this->getAuthenticationService()->clearIdentity();
        }
        return $this->redirect()->toRoute(static::ROUTE_LOGIN);
    }

    /**
     * Get the authentication service
     *
     * @return AuthenticationService
     */
    public function getAuthenticationService()
    {
        if (null === $this->authService) {
            $this->authService = $this->getServiceLocator()->get('auth_service');
        }
        return $this->authService;
    }

    /**
     * Get the auth storage
     *
     * @return \Application\Model\AuthStorage
     */
    public function getAuthStorage()
    {
        if (null === $this->authStorage) {
            $this->authStorage = $this->getServiceLocator()->get('authStorage');
        }
        return $this->authStorage;
    }

    /**
     * Get the login form
     *
     * @return \Zend\Form\Form
     */
    public function getLoginForm()
    {
        if (null === $this->loginForm)
            $this->loginForm = $this->getServiceLocator()->get('login_form');
        return $this->loginForm;
    }
}
