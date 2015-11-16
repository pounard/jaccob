<?php

namespace Jaccob\MediaBundle\Model\Structure;

use PommProject\ModelManager\Model\RowStructure;

/**
 * Media structure
 */
class Media extends RowStructure
{
    /**
     * Default constructor
     */
    public function __construct()
    {
        $this
            ->setRelation('public.media')
            ->setPrimaryKey(['id'])
            ->addField('id', 'int4')
            ->addField('id_album', 'int4')
            ->addField('id_account', 'int4')
            ->addField('id_device', 'int4')
            ->addField('access_level', 'int4')
            ->addField('name', 'varchar')
            ->addField('path', 'varchar')
            ->addField('physical_path', 'varchar')
            ->addField('filesize', 'int4')
            ->addField('width', 'int4')
            ->addField('height', 'int4')
            ->addField('orientation', 'int4')
            ->addField('user_name', 'varchar')
            ->addField('md5_hash', 'varchar')
            ->addField('mimetype', 'varchar')
            ->addField('ts_added', 'timestamp')
            ->addField('ts_updated', 'timestamp')
            ->addField('ts_user_date', 'timestamp')
        ;
    }
}
