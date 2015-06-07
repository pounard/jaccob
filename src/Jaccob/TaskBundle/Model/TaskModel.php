<?php

namespace Jaccob\TaskBundle\Model;

use PommProject\ModelManager\Model\Model;
use PommProject\ModelManager\Model\Projection;
use PommProject\ModelManager\Model\ModelTrait\WriteQueries;

use PommProject\Foundation\Where;

use Jaccob\TaskBundle\Model\Structure\Task as TaskStructure;

/**
 * Task model
 */
class TaskModel extends Model
{
    use WriteQueries;

    public function __construct()
    {
        $this->structure = new TaskStructure;
        $this->flexible_entity_class = '\Jaccob\TaskBundle\Model\Task';
    }
}
