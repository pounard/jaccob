<?php

namespace Jaccob\TaskBundle\Controller;

use Jaccob\AccountBundle\Controller\AbstractUserAwareController;
use Jaccob\TaskBundle\TaskModelAware;

use PommProject\Foundation\Where;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class TaskController extends AbstractUserAwareController
{
    use TaskModelAware;

    /**
     * Menu
     */
    public function menuAction()
    {
        $items = [];

        return $this->render('JaccobTaskBundle:Task:menu.html.twig', ['items' => $items]);
    }

    /**
     * List
     */
    public function listAction()
    {
        $account = $this->getCurrentUserAccount();

        $where = (new Where())
            ->andWhere("id_account = $*", [$account->getId()])
        ;

        $taskList = $this
            ->getTaskModel()
            ->paginateFindWhere($where, 50)
        ;

        return $this->render('JaccobTaskBundle:Task:list.html.twig', ['tasks' => $taskList->getIterator()]);
    }

    /**
     * Starred
     */
    public function listStarredAction()
    {
        $account = $this->getCurrentUserAccount();

        $where = (new Where())
            ->andWhere("id_account = $*", [$account->getId()])
            ->andWhere("is_starred = $*", [1])
        ;

        $taskList = $this
            ->getTaskModel()
            ->paginateFindWhere($where, 50)
        ;

        return $this->render('JaccobTaskBundle:Task:list.html.twig', ['tasks' => $taskList->getIterator()]);
    }

    /**
     * Deadline
     */
    public function listDeadlineAction()
    {
        $account = $this->getCurrentUserAccount();

        $where = (new Where())
            ->andWhere("id_account = $*", [$account->getId()])
            ->andWhere("is_done = $*", [0])
            ->andWhere("ts_deadline < $*", [(new \DateTime())->format('Y-m-d H:i:s')])
        ;

        $taskList = $this
            ->getTaskModel()
            ->paginateFindWhere($where, 50)
        ;

        return $this->render('JaccobTaskBundle:Task:list.html.twig', ['tasks' => $taskList->getIterator()]);
    }
}
