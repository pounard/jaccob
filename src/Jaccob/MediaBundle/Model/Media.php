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

    public function getFileExtension()
    {
        if ($name = $this->name) {
            if (false !== ($position = strrpos($name, '.'))) {
                return substr($name, $position + 1);
            }
        }
    }

    public function getNameWithoutExtension()
    {
        if ($name = $this->name) {
            if (false !== ($position = strrpos($name, '.'))) {
                return substr($name, 0, $position);
            }
            return $name;
        }
    }
}
