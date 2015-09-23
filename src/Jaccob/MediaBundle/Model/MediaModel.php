<?php

namespace Jaccob\MediaBundle\Model;

use Jaccob\MediaBundle\Model\Structure\Media as MediaStructure;

use PommProject\Foundation\Where;
use PommProject\ModelManager\Model\Model;
use PommProject\ModelManager\Model\ModelTrait\WriteQueries;

/**
 * Media model
 */
class MediaModel extends Model
{
    use WriteQueries;

    /**
     * Default constructor
     */
    public function __construct()
    {
        $this->structure = new MediaStructure;
        $this->flexible_entity_class = '\Jaccob\MediaBundle\Model\Media';
    }

    public function findAllByPK(array $primary_keys, $suffix = null)
    {
        $sql = strtr(
            "select :fields from :table where :condition :suffix",
            [
                ':fields'     => $this->createProjection()->formatFieldsWithFieldAlias(),
                ':table'      => $this->getStructure()->getRelation(),
                ':suffix'     => $suffix,
                ':condition'  => (new Where())->andWhere(Where::createWhereIn('id', $primary_keys)),
            ]
        );
  
        return $this->query($sql, $primary_keys);
    }

    public function findByAlbum($albumId)
    {
        $where = (new Where())
            ->andWhere('id_album = $*', [$albumId])
        ;

        return $this->paginateFindWhere($where, 100);
    }
}
