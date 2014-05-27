<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 19/5/2014
 * Time: 6:10 μμ
 */

namespace Schedule\Controller;


use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class ExceptionController extends AbstractActionController
{
    const MESSAGE_EXCEPTION_CREATED = "The exception has been created successfully.";
    const MESSAGE_EXCEPTION_REMOVED = "The exception has been removed successfully.";
    const MESSAGE_EXCEPTIONS_SAVED = "The exceptions have been saved successfully.";
    const ERROR_EXCEPTION_REMOVE = "There was a problem when removing the exception, please try again.";
    const ERROR_EXCEPTIONS_NOT_SAVED = "There was a problem when saving the exceptions, please try again.";

    private $addExceptionForm;

    private $entityManager;

    private $exceptionRepository;

    private $translator;

    private $exceptionService;

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

    public function saveAction()
    {
        if ($this->getRequest()->isXmlHttpRequest() && $this->identity()) {
            $success = 1;
            $message = self::MESSAGE_EXCEPTIONS_SAVED;
            $entities = $this->params()->fromPost('entities');
            $em = $this->getEntityManager();
            $exceptionRepository = $this->getExceptionRepository();
            foreach ($entities as $entity) {
                $exception = $exceptionRepository->find($entity['ExceptionId']);
                array_shift($entity);
                foreach ($entity as $key => $value) {
                    if (empty($value)) $value = null;
                    $exception->{'set' . $key}($value);
                }
                $em->persist($exception);
            }
            try {
                $em->flush();
            } catch (\Exception $e) {
                $success = 0;
                $message = self::ERROR_EXCEPTIONS_NOT_SAVED;
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
        if ($request->isXmlHttpRequest() && $this->identity()) {
            $service = $this->getExceptionService();
            $data = $request->getPost();
            if ($request->isPost()) {
                $form = $this->getAddExceptionForm();
                $worker = $service->create($data, $form);
                if ($worker) {
                    $this->flashMessenger()->addMessage($this->getTranslator()->translate(static::MESSAGE_EXCEPTION_CREATED));
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
        if ($this->getRequest()->isXmlHttpRequest() && $this->identity()) {
            $id = $this->params()->fromPost("id");
            $success = 0;
            $message = self::MESSAGE_EXCEPTION_REMOVED;
            if ($this->getExceptionService()->remove($id)) {
                $success = 1;
            } else {
                $message = self::ERROR_EXCEPTION_REMOVE;
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
     * Get the entity manager
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        if (null === $this->entityManager)
            $this->entityManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        return $this->entityManager;
    }

    /**
     * Get the exception repository
     *
     * @return \Doctrine\ORM\EntityRepository
     */
    public function getExceptionRepository()
    {
        if (null === $this->exceptionRepository)
            $this->exceptionRepository = $this->getEntityManager()->getRepository('Schedule\Entity\Exception');
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

    /**
     * Get the translator
     *
     * @return \Zend\I18n\Translator\Translator
     */
    public function getTranslator()
    {
        if (null === $this->translator)
            $this->translator = $this->getServiceLocator()->get('translator');
        return $this->translator;
    }
}