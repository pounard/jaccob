<?php

namespace Jaccob\MediaBundle\Model;

use Jaccob\MediaBundle\Model\Structure\MediaDerivative as MediaDerivativeStructure;

use PommProject\ModelManager\Model\Model;
use PommProject\ModelManager\Model\ModelTrait\WriteQueries;

/**
 * Media model
 */
class MediaDerivativeModel extends Model
{
    use WriteQueries;

    /**
     * Default constructor
     */
    public function __construct()
    {
        $this->structure = new MediaDerivativeStructure;
        $this->flexible_entity_class = '\Jaccob\MediaBundle\Model\MediaDerivative';
    }
}
