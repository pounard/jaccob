<?php

namespace Jaccob\MediaBundle\Model;

use Jaccob\MediaBundle\Model\Structure\Device as DeviceStructure;

use PommProject\ModelManager\Model\Model;
use PommProject\ModelManager\Model\Projection;
use PommProject\ModelManager\Model\ModelTrait\WriteQueries;

use PommProject\Foundation\Where;

/**
 * Album model
 */
class DeviceModel extends Model
{
    use WriteQueries;

    /**
     * Default cosntructor
     */
    public function __construct()
    {
        $this->structure = new DeviceStructure;
        $this->flexible_entity_class = '\Jaccob\MediaBundle\Model\Device';
    }
}
