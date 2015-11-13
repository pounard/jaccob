<?php

namespace Jaccob\MediaBundle\Import;

use Jaccob\MediaBundle\Model\Album;
use Jaccob\MediaBundle\Util\FileSystem;

/**
 * Import medias from the filesystem. This class will work on a root
 * working directory which is supposed unique for each user: file path
 * and album names will be derivated from the file path and must be
 * relative to the working directory in order to avoid importing
 * site's technical information into database.
 */
class FilesystemImporter extends DefaultImporter
{
    /**
     * Import from folder
     *
     * @param string $path
     *   Path must be relative to working directory
     *
     * @return \Jaccob\MediaBundle\Model\Album
     */
    public function importFromFolder($path)
    {
        $files = new \CallbackFilterIterator(

            new \FilesystemIterator(
                FileSystem::pathJoin($this->getWorkingDirectory(), $path),
                \FilesystemIterator::CURRENT_AS_PATHNAME |
                \FilesystemIterator::SKIP_DOTS
            ),

            function ($current, $key, $iterator) {
                return is_file($current);
            }
        );

        $album = null;

        foreach ($files as $filename) {

            // Pure optimization: avoid loading the album at each iteration
            if (null === $album) {
                $album = $this->findAlbum($filename);
            }

            $new = $this->createEntityFromFile($album, $filename);

            $this->import($new, $album);
        }

        return $album;
    }
}
