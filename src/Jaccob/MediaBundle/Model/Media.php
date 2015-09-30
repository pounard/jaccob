<?php

namespace Jaccob\MediaBundle\Model;

use PommProject\ModelManager\Model\FlexibleEntity;

/**
 * Media entity
 */
class Media extends FlexibleEntity
{
    public function getDisplayName()
    {
        if ($this->user_name) {
            return $this->user_name;
        }

        return $this->name;
    }
}
