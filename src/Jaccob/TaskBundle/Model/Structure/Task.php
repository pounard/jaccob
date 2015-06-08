<?php

namespace Jaccob\TaskBundle\Model\Structure;

use PommProject\ModelManager\Model\RowStructure;

/**
 * Task structure
 */
class Task extends RowStructure
{
    public function __construct()
    {
        $this
            ->setRelation('public.task')
            ->setPrimaryKey(['id'])
            ->addField('id', 'int4')
            ->addField('id_account', 'int4')
            ->addField('is_done', 'bool')
            ->addField('is_starred', 'bool')
            ->addField('title', 'varchar')
            ->addField('description', 'text')
            ->addField('priority', 'int4')
            ->addField('ts_added', 'timestamp')
            ->addField('ts_updated', 'timestamp')
            ->addField('ts_deadline', 'timestamp')
        ;
    }
}
