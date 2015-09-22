<?php

namespace Jaccob\MediaBundle\Util;

/**
 * File system high level abstraction
 */
final class FileSystem
{
    /**
     * Build path given the provided segments
     *
     * @param string|string[] $segments
     */
    static public function pathJoin($segments)
    {
        if (is_string($segments)) {
            $segments = func_get_args();
        }

        return preg_replace('/\/{2,}/', '/', implode('/', $segments));
    }

    /**
     * Create directory
     *
     * @todo I am stupid and I could have used recursive mkdir directly
     *
     * @param string $path
     */
    static public function createDirectory($path)
    {
        $current = null;

        foreach (explode('/', trim($path, '/')) as $segment) {

            if (null === $current) {
                $current = '/' . $segment;
            } else {
                $current .= '/' . $segment;
            }

            if (!is_dir($current)) {
                if (!mkdir($current)) {
                    throw new \RuntimeException(sprintf("Could not create directory '%s'", $current));
                }
            }
        }
    }

    /**
     * Ensure directory exists
     *
     * @param string $path
     *   Directory path
     * @param boolean $writable
     *   Should it be writable
     * @param boolean $create
     *   Should we create it if it does not exist
     */
    static public function ensureDirectory($path, $writable = false, $create = false)
    {
        if (!is_dir($path)) {
            if ($create) {
                self::createDirectory($path);
            } else {
                throw new \RuntimeException(sprintf("Directory does not exists '%s'", $path));
            }
        }

        if ($writable) {
            if (!is_writable($path)) {
                throw new \RuntimeException(sprintf("Directory is not writable '%s'", $path));
            }
        } else if (!is_readable($path)) {
            throw new \RuntimeException(sprintf("Directory is not readable '%s'", $path));
        }
    }
}
