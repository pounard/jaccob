<?php

namespace Jaccob\TaskBundle\Menu;

use Jaccob\AppBundle\Menu\AbstractMenuProvider;
use Jaccob\TaskBundle\TaskModelAware;

use Knp\Menu\ItemInterface;

use PommProject\Foundation\Where;

use Symfony\Component\HttpFoundation\RequestStack;

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
class MenuProvider extends AbstractMenuProvider
{
    use TaskModelAware;

    public function attachMainMenuChildren(ItemInterface $root, RequestStack $requestStack)
    {
        $account = $this->getCurrentAccount();

        if ($account) {

            // @todo If account can access tasks and if account is in one of
            // task pages, build submenu, else drop it because it's terrible
            // it does SQL queries and all.
            $menu = $root->addChild('Tasks', ['route' => 'jaccob_task_list']);

            $menu->addChild('All tasks', ['route' => 'jaccob_task_list']);
return;
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
    }
}
