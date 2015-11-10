<?php

namespace Jaccob\MediaBundle\Type;

use Jaccob\MediaBundle\Type\Impl\NullType;

/**
 * This is actually a factory service that will register all known media types
 */
class TypeFinderService
{
    /**
     * @var \Jaccob\MediaBundle\Type\TypeInterface[]
     */
    protected $registeredTypes = [];

    /**
     * Register a type
     *
     * @param string|string[] $mimetypes
     * @param \Jaccob\MediaBundle\Type\TypeInterface $type
     *
     * @return \Jaccob\MediaBundle\Type\TypeFinderService
     */
    public function addType($mimetypes, TypeInterface $type)
    {
        if (!is_array($mimetypes)) {
            $mimetypes = [$mimetypes];
        }

        foreach ($mimetypes as $mimetype) {
            $this->registeredTypes[$mimetype] = $type;
        }

        return $this;
    }

    /**
     * Get type for provided mimetype, this methods returns a null object
     * if no type is found
     *
     * @param string $mimetype
     *
     * @return \Jaccob\MediaBundle\Type\TypeInterface
     */
    public function getTypeFor($mimetype)
    {
        if (isset($this->registeredTypes[$mimetype])) {
            return $this->registeredTypes[$mimetype];
        }

        return new NullType();
    }
}
