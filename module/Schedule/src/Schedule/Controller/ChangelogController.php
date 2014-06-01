<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 19/5/2014
 * Time: 6:10 μμ
 */

namespace Schedule\Controller;


use Application\Controller\BaseController;
use Zend\View\Model\ViewModel;

class ChangelogController extends BaseController
{

    /**
     * The changelog entity repository
     *
     * @var \Doctrine\ORM\EntityRepository
     */
    private $changelogRepository;

    /**
     * The changelog list action
     * Route: /changelog
     * Requires login
     *
     * @return ViewModel
     */
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
     * Get the changelog entity repository
     *
     * @return \Schedule\Repository\ChangelogRepository
     */
    public function getChangelogRepository()
    {
        if (null === $this->changelogRepository)
            $this->changelogRepository = $this->entityManager->getRepository('Schedule\Entity\Changelog');
        return $this->changelogRepository;
    }
}