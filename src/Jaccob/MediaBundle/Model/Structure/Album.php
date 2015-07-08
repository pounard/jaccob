<?php

namespace Jaccob\MediaBundle\Model\Structure;

use PommProject\ModelManager\Model\RowStructure;

/**
 * Album structure
 */
class Album extends RowStructure
{
    /**
     * Default constructor
     */
    public function __construct()
    {
        $this
            ->setRelation('public.album')
            ->setPrimaryKey(['id'])
            ->addField('id', 'int4')
            ->addField('id_account', 'int4')
            ->addField('id_media_preview', 'int4')
            ->addField('access_level', 'int4')
            ->addField('path', 'varchar')
            ->addField('user_name', 'varchar')
            ->addField('file_count', 'int4')
            ->addField('ts_added', 'timestamp')
            ->addField('ts_updated', 'timestamp')
            ->addField('ts_user_date_begin', 'timestamp')
            ->addField('ts_user_date_end', 'timestamp')
            ->addField('share_enabled', 'bool')
            ->addField('share_token', 'varchar')
            ->addField('share_password', 'varchar')
        ;
    }
}
