<?php

namespace Jaccob\TaskBundle\Menu;

use Jaccob\AppBundle\Menu\AbstractMenu;

use Knp\Menu\FactoryInterface;

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
    public function sideMenu(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root');

        $account = $this->getAccount();

        if ($account) {
            $menu->addChild('All tasks', ['route' => 'jaccob_task_list']);
            $menu->addChild('Deadline', ['route' => 'jaccob_task_list_deadline']);
            $menu->addChild('Starred', ['route' => 'jaccob_task_list_starred']);
            $menu->addChild('Archives', ['route' => 'jaccob_task_list_archive']);
        }

        return $menu;
    }
}
