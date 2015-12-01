<?php

namespace Jaccob\TaskBundle\Menu;

use Jaccob\AccountBundle\Menu\AbstractSecurityAwareMenuProvider;
use Jaccob\TaskBundle\TaskModelAware;

use PommProject\Foundation\Where;

class TaskMenuProvider extends AbstractSecurityAwareMenuProvider
{
    use TaskModelAware;

    /**
     * {@inheritdoc}
     */
    public function get($name, array $options = array())
    {
        switch ($name) {

            case 'main.task':
                $account = $this->getCurrentUserAccount();

                if ($account) {
                    $menu = $this->factory->createItem('Tasks', ['route' => 'jaccob_task.list']);
                    $menu->addChild('All tasks', ['route' => 'jaccob_task.list']);

                    $where = (new Where())
                        ->andWhere("id_account = $*", [$account->getId()])
                        ->andWhere("is_done = $*", [0])
                        ->andWhere("ts_deadline < $*", [(new \DateTime())->format('Y-m-d H:i:s')])
                    ;
                    $count = $this
                        ->getTaskModel()
                        ->countWhere($where)
                    ;
                    $menu
                        ->addChild('Deadline', ['route' => 'jaccob_task.list_deadline'])
                        ->setExtras(['count' => $count])
                    ;

                    $where = (new Where())
                        ->andWhere("id_account = $*", [$account->getId()])
                        ->andWhere("is_starred = $*", [1])
                    ;
                    $count = $this
                        ->getTaskModel()
                        ->countWhere($where)
                    ;
                    $menu
                        ->addChild('Starred', ['route' => 'jaccob_task.list_starred'])
                        ->setExtras(['count' => $count])
                    ;

                    $menu->addChild('Archives', ['route' => 'jaccob_task.list_archive']);


                    return $menu;
                }
                break;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function has($name, array $options = array())
    {
        switch ($name) {

            case 'main.task':
                return true;

            default:
                return false;
        }
    }
}
