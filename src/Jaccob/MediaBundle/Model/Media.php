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
        if ($path = $this->physical_path) {
            if (false !== ($position = strrpos($path, '.'))) {
                return substr($path, $position + 1);
            }
        }
    }

    public function getPhysicalPathWithoutExtension()
    {
        if ($path = $this->physical_path) {
            if (false !== ($position = strrpos($path, '.'))) {
                return substr($path, 0, $position);
            }
            return $path;
        }
    }
}
