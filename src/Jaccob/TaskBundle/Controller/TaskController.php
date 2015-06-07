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
     * List action
     */
    public function listAction()
    {
        $account = $this->getCurrentUserAccount();

        $where = new Where();
        $where->addWhere("id_account = $*", [$account->getId()], '=');

        $taskList = $this
            ->getTaskModel()
            ->paginateFindWhere($where,50)
        ;

        return $this->render('JaccobTaskBundle:Task:list.html.twig', ['tasks' => $taskList->getIterator()]);
    }

    /**
     * Post action
     */
    public function postAction()
    {
        // @todo
    }

    /**
     * Delete action
     */
    public function deleteAction()
    {
        // @todo
    }

    /**
     * Update action
     */
    public function updateAction()
    {
        // @todo
    }
}
