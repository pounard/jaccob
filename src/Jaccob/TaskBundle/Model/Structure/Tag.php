<?php

namespace Jaccob\TaskBundle\Model\Structure;

use PommProject\ModelManager\Model\RowStructure;

/**
 * Tag structure
 */
class Tag extends RowStructure
{
    public function __construct()
    {
        $this
            ->setRelation('public.task_tag')
            ->setPrimaryKey(['id'])
            ->addField('id', 'int4')
            ->addField('id_account', 'int4')
            ->addField('name', 'varchar')
        ;
    }
}
