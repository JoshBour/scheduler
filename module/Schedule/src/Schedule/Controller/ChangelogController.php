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

class ChangelogController extends AbstractActionController
{
    private $changelogRepository;

    private $entityManager;

    private $translator;

    public function listAction()
    {
        if ($this->identity()) {
            $startDate = new \DateTime($this->params()->fromRoute('startDate', 'first day of this month'));
            $endDate = new \DateTime($this->params()->fromRoute('endDate', 'last day of this month'));

            $changelogs = $this->getChangelogRepository()->findEntriesBetweenDates($startDate, $endDate);


            return new ViewModel(array(
                "changelogs" => $changelogs,
                "bodyClass" => "changelogPage"
            ));
        } else {
            return $this->notFoundAction();
        }
    }

    /**
     * @return \Schedule\Repository\ChangelogRepository
     */
    public function getChangelogRepository()
    {
        if (null === $this->changelogRepository)
            $this->changelogRepository = $this->getEntityManager()->getRepository('Schedule\Entity\Changelog');
        return $this->changelogRepository;
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