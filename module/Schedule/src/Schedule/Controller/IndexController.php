<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 19/5/2014
 * Time: 6:10 μμ
 */

namespace Schedule\Controller;


use Application\Controller\BaseController;
use Schedule\Model\ExcelGenerator;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class IndexController extends BaseController
{
    const LAYOUT_NO_HEADER = "layout/no-header";

    /**
     * The add entry form
     *
     * @var \Zend\Form\Form
     */
    private $addEntryForm;

    /**
     * The entry repository
     *
     * @var \Schedule\Repository\EntryRepository
     */
    private $entryRepository;

    /**
     * The entry service
     *
     * @var \Schedule\Service\Entry
     */
    private $entryService;

    /**
     * The worker repository
     *
     * @var \Worker\Repository\WorkerRepository
     */
    private $workerRepository;

    /**
     * The schedule list action
     * Route: /schedule
     *
     * @return ViewModel
     */
    public function listAction()
    {
        if (!$this->identity()) $this->layout()->setTemplate(self::LAYOUT_NO_HEADER);
        $startDate = new \DateTime($this->params()->fromRoute('startDate', strftime('%d-%m-%Y', strtotime('first day of this month'))));
        $endDate = new \DateTime($this->params()->fromRoute('endDate', strftime('%d-%m-%Y', strtotime('last day of this month'))));
        $endDate->modify("+23 Hour");
        $endDate->modify("+59 Minute");
        $dates = $this->generateDates($startDate, $endDate);
        return new ViewModel(array(
            "startDate" => $startDate,
            "endDate" => $endDate,
            "dates" => $dates,
            "hideForm" => true,
            "form" => $this->getAddEntryForm(),
            "bodyClass" => "schedulePage"
        ));
    }

    /**
     * The schedule save action
     * Route: /schedule/save
     * Only accessible via xmlHttpRequest
     * Requires login
     *
     * @return array|JsonModel
     */
    public function saveAction()
    {
        if ($this->getRequest()->isXmlHttpRequest() && $this->identity()) {
            $success = 1;
            $message = $this->translate($this->vocabulary["MESSAGE_ENTRIES_SAVED"]);
            $entities = $this->params()->fromPost('entities');
            if (!$this->getEntryService()->save($entities)) {
                $success = 0;
                $message = $this->translate($this->vocabulary["ERROR_ENTRIES_NOT_SAVED"]);
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
     * The schedule add action
     * Route: /schedule/add
     * Only accessible via xmlHttpRequest
     * Requires login
     *
     * @return array|JsonModel
     */
    public function addAction()
    {
        /**
         * @var $request \Zend\Http\Request
         */
        $request = $this->getRequest();
        if ($request->isXmlHttpRequest() && $this->identity()) {
            $service = $this->getEntryService();
            $data = $request->getPost();
            if ($request->isPost()) {
                $form = $this->getAddEntryForm();
                $worker = $service->create($data, $form);
                if ($worker) {
                    $this->flashMessenger()->addMessage($this->translate($this->vocabulary["MESSAGE_ENTRY_CREATED"]));
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


    public function exportAction()
    {
        $startDate = new \DateTime($this->params()->fromRoute('startDate', strftime('%d-%m-%Y', strtotime('first day of this month'))));
        $endDate = new \DateTime($this->params()->fromRoute('endDate', strftime('%d-%m-%Y', strtotime('last day of this month'))));
        $endDate->modify("+23 Hour");
        $endDate->modify("+59 Minute");
        $dates = $this->generateDates($startDate, $endDate);

        $name = ExcelGenerator::exportExcel($dates, clone $startDate, clone $endDate);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . "schedule-" . $startDate->format('d-m-Y') . '-' . $endDate->format('d-m-Y') . ".xlsx" . '"');
        header('Cache-Control: max-age=0');
        readfile($name);

        exit();
        // http://stackoverflow.com/questions/18997710/how-to-access-files-in-data-folder-in-zend-framework
    }

    /**
     * Generate the schedule worker-date array
     *
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @return array
     */
    private function generateDates($startDate, $endDate)
    {
        $workers = $this->getWorkerRepository()->findAllNonReleasedWorkers($startDate, $endDate);
        $dates = array();
        $interval = $startDate->diff($endDate)->days + 1;
        /**
         * @var \Worker\Entity\Worker $worker
         */
        foreach ($workers as $worker) {
            $id = $worker->getEncodedId();
            $startDateClone = clone $startDate;
            for ($j = 0; $j < $interval; $j++) {
                $dates[$id][$startDateClone->format('d-m-Y')] = array();
                $startDateClone->modify('+1 day');
            }
            $entries = $this->getEntryRepository()->findEntriesBetweenDates($startDate, $endDate, $worker);
            /**
             * @var \Schedule\Entity\Entry $entry
             */
            foreach ($entries as $entry) {
                $dates[$id][$entry->getStartTime()->format('d-m-Y')][] = $entry;
            }
        }
        return $dates;
    }

    /**
     * Get the add entry form
     *
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
     * Get the entry repository
     *
     * @return \Schedule\Repository\EntryRepository
     */
    public function getEntryRepository()
    {
        if (null === $this->entryRepository)
            $this->entryRepository = $this->entityManager->getRepository('Schedule\Entity\Entry');
        return $this->entryRepository;
    }

    /**
     * Get the worker repository
     *
     * @return \Worker\Repository\WorkerRepository
     */
    public function getWorkerRepository()
    {
        if (null == $this->workerRepository)
            $this->workerRepository = $this->entityManager->getRepository('Worker\Entity\Worker');
        return $this->workerRepository;
    }
} 