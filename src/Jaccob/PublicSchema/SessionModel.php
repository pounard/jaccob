<?php

namespace Jaccob\PublicSchema;

use PommProject\ModelManager\Model\Model;
use PommProject\ModelManager\Model\Projection;
use PommProject\ModelManager\Model\ModelTrait\WriteQueries;

use PommProject\Foundation\Where;

use Jaccob\PublicSchema\AutoStructure\Session as SessionStructure;
use Jaccob\PublicSchema\Session;

/**
 * SessionModel
 *
 * Model class for table session.
 *
 * @see Model
 */
class SessionModel extends Model
{
    use WriteQueries;

    /**
     * __construct()
     *
     * Model constructor
     *
     * @access public
     * @return void
     */
    public function __construct()
    {
        $this->structure = new SessionStructure;
        $this->flexible_entity_class = "\Jaccob\PublicSchema\Session";
    }
}
