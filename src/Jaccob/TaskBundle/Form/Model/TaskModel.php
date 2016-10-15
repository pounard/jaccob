<?php

namespace Jaccob\TaskBundle\Form\Model;

use Jaccob\TaskBundle\Model\Task;

class TaskModel
{
    public $title;

    public $description;

    public $ts_deadline;

    public $priority = 0;

    public $is_starred = false;

    public $is_done = false;

    public function __construct(Task $task = null)
    {
        if ($task) {
            $this->title        = $task->getTitle();
            $this->description  = $task->getDescription();
            $this->ts_deadline  = $task->getDeadline();
            $this->priority     = $task->getPriority();
            $this->is_starred   = $task->isStarred();
            $this->is_done      = $task->isDone();
        } else {
            $this->ts_deadline  = new \DateTime("now +1 month");
        }
    }
}
