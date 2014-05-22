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

class IndexController extends AbstractActionController
{
    const MESSAGE_ENTRY_CREATED = "The entry has been created successfully.";
    const MESSAGE_ENTRIES_SAVED = "The entries have been saved successfully.";
    const ERROR_ENTRIES_NOT_SAVED = "There was an error when saving the entries, please try again.";

    private $addEntryForm;

    private $entityManager;

    private $entryRepository;

    private $translator;

    private $workerRepository;

    private $entryService;

    public function listAction()
    {
        $startDate = new \DateTime($this->params()->fromRoute('startDate', date('d-m-Y')));
        $endDate = new \DateTime($this->params()->fromRoute('endDate', date('d-m-Y')));

        $entries = $this->getEntryRepository()->findEntriesBetweenDates($startDate, $endDate);
        $dates = array();


        $interval = $startDate->diff($endDate)->days + 1;


        foreach ($entries as $entry) {
            $workers[] = $entry->getWorker()->getFullName();
        }
        array_unique($workers);
        $i = 0;
        while ($i < count($workers)) {
            $startDateClone = clone $startDate;
            for ($j = 0; $j < $interval; $j++) {
                $dates[$workers[$i]][$startDateClone->format('d-m-Y')] = array();
                $startDateClone->modify('+1 day');
            }
            $i++;
        }


        foreach ($entries as $entry) {
            $startTime = $entry->getStartTime()->format('d-m-Y');
            $dates[$entry->getWorker()->getFullName()][$startTime] = $entry;
        }

        return new ViewModel(array(
            "startDate" => $startDate,
            "endDate" => $endDate,
            "dates" => $dates,
            "hideForm" => true,
            "form" => $this->getAddEntryForm()
        ));
    }

    public function saveAction()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            $success = 1;
            $message = self::MESSAGE_ENTRIES_SAVED;
            $entities = $this->params()->fromPost('entities');
            if (!$this->getEntryService()->save($entities)) {
                $success = 0;
                $message = self::ERROR_ENTRIES_NOT_SAVED;
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
            $service = $this->getEntryService();
            $data = $request->getPost();
            if ($request->isPost()) {
                $form = $this->getAddEntryForm();
                $worker = $service->create($data, $form);
                if ($worker) {
                    $this->flashMessenger()->addMessage($this->getTranslator()->translate(static::MESSAGE_ENTRY_CREATED));
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
    }

    /**
     * @return \Zend\Form\Form
     */
    public function getAddEntryForm()
    {
        if (null === $this->addEntryForm)
            $this->addEntryForm = $this->getServiceLocator()->get('entry_add_form');
        return $this->addEntryForm;
    }

    /**
     * Get the entry service
     *
     * @return \Schedule\Service\Entry
     */
    public function getEntryService()
    {
        if (null === $this->entryService)
            $this->entryService = $this->getServiceLocator()->get('entry_service');
        return $this->entryService;
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
     * @return \Schedule\Repository\EntryRepository
     */
    public function getEntryRepository()
    {
        if (null === $this->entryRepository)
            $this->entryRepository = $this->getEntityManager()->getRepository('Schedule\Entity\Entry');
        return $this->entryRepository;
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
} 