<?php

namespace Jaccob\PublicSchema;

use PommProject\ModelManager\Model\Model;
use PommProject\ModelManager\Model\Projection;
use PommProject\ModelManager\Model\ModelTrait\WriteQueries;

use PommProject\Foundation\Where;

use Jaccob\PublicSchema\AutoStructure\Account as AccountStructure;
use Jaccob\PublicSchema\Account;

/**
 * AccountModel
 *
 * Model class for table account.
 *
 * @see Model
 */
class AccountModel extends Model
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
        $this->structure = new AccountStructure;
        $this->flexible_entity_class = "\Jaccob\PublicSchema\Account";
    }
}
