<?php

namespace Jaccob\MediaBundle\Model;

use PommProject\ModelManager\Model\FlexibleEntity;

/**
 * Album entity
 */
class Album extends FlexibleEntity
{
    public function getDisplayName()
    {
        if ($this->user_name) {
            return $this->user_name;
        }

        return $this->path;
    }
}
