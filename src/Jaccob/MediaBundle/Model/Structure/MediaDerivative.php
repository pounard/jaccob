<?php

namespace Jaccob\MediaBundle\Model\Structure;

use PommProject\ModelManager\Model\RowStructure;

/**
 * Media structure
 */
class MediaDerivative extends RowStructure
{
    /**
     * Default constructor
     */
    public function __construct()
    {
        $this
            ->setRelation('public.media_derivative')
            ->setPrimaryKey(['id'])
            ->addField('id', 'int4')
            ->addField('id_media', 'int4')
            ->addField('name', 'varchar')
            ->addField('physical_path', 'varchar')
            ->addField('filesize', 'int4')
            ->addField('width', 'int4')
            ->addField('height', 'int4')
            ->addField('md5_hash', 'varchar')
            ->addField('mimetype', 'varchar')
        ;
    }
}
