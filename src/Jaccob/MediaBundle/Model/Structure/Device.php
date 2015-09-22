<?php

namespace Jaccob\MediaBundle\Model\Structure;

use PommProject\ModelManager\Model\RowStructure;

/**
 * Media structure
 */
class Device extends RowStructure
{
    /**
     * Default constructor
     */
    public function __construct()
    {
        $this
            ->setRelation('public.device')
            ->setPrimaryKey(['id'])
            ->addField('id', 'int4')
            ->addField('id_account', 'int4')
            ->addField('name', 'varchar')
        ;
    }
}
