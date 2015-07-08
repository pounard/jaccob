<?php

namespace Jaccob\TaskBundle\Menu;

use Jaccob\AppBundle\Menu\AbstractMenu;
use Jaccob\TaskBundle\TaskModelAware;

use Knp\Menu\FactoryInterface;

use PommProject\Foundation\Where;

/*
<ul class="nav navbar-nav">
  <li>
    <a href="{{ path('jaccob_task_list_deadline') }}">
      <span aria-hidden="true" class="glyphicon glyphicon-exclamation-sign"></span>
      Deadline
      <span class="badge">42</span>
    </a>
  </li>
  <li>
    <a href="{{ path('jaccob_task_list_starred') }}">
      <span aria-hidden="true" class="glyphicon glyphicon-star"></span>
      Starred
    </a>
  </li>
  <li>
    <a href="{{ path('jaccob_task_list_archive') }}">Archives</a>
  </li>
</ul>
 */
class Builder extends AbstractMenu
{
    use TaskModelAware;

    public function sideMenu(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root');

        $account = $this->getCurrentAccount();

        if ($account) {

            $menu->addChild('All tasks', ['route' => 'jaccob_task_list']);

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
                ->addChild('Deadline', ['route' => 'jaccob_task_list_deadline'])
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
                ->addChild('Starred', ['route' => 'jaccob_task_list_starred'])
                ->setExtras(['count' => $count])
            ;

            $menu->addChild('Archives', ['route' => 'jaccob_task_list_archive']);
        }

        return $menu;
    }
}
