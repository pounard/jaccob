<?php

namespace Jaccob\TaskBundle\Model;

use PommProject\ModelManager\Model\Model;
use PommProject\ModelManager\Model\Projection;
use PommProject\ModelManager\Model\ModelTrait\WriteQueries;

use PommProject\Foundation\Where;

use Jaccob\TaskBundle\Model\Structure\Tag as TagStructure;

/**
 * Tag model
 */
class TagModel extends Model
{
    use WriteQueries;

    /**
     * __construct()
     *
     * Model constructor
     *
     * @access public
     */
    public function __construct()
    {
        $this->structure = new TagStructure();
        $this->flexible_entity_class = '\Jaccob\TaskBundle\Model\Tag';
    }
}
