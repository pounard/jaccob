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

    public function findByAlbum($albumId, $limit = 10, $page = 1)
    {
        $where = (new Where())
            ->andWhere('id_album = $*', [$albumId])
        ;

        return $this->paginateFindWhere($where, $limit, $page, 'order by ts_added asc, id asc');
    }

    public function findPreviousInAlbum($albumId, $currentMediaId)
    {
        $where = (new Where())
            ->andWhere('id_album = $*', [$albumId])
            ->andWhere('id < $*', [$currentMediaId])
        ;

        $sql = strtr(
            "select :fields from :table where :condition order by id desc limit 1",
            [
              ':fields'     => $this->createProjection()->formatFieldsWithFieldAlias(),
              ':table'      => $this->getStructure()->getRelation(),
              ':condition'  => $where,
            ]
        );

        $iterator = $this->query($sql, [$albumId, $currentMediaId]);

        return $iterator->isEmpty() ? null : $iterator->current();
    }

    public function findNextInAlbum($albumId, $currentMediaId)
    {
        $where = (new Where())
            ->andWhere('id_album = $*', [$albumId])
            ->andWhere('id > $*', [$currentMediaId])
        ;

        $sql = strtr(
            "select :fields from :table where :condition order by id asc limit 1",
            [
              ':fields'     => $this->createProjection()->formatFieldsWithFieldAlias(),
              ':table'      => $this->getStructure()->getRelation(),
              ':condition'  => $where,
            ]
        );

        $iterator = $this->query($sql, [$albumId, $currentMediaId]);

        return $iterator->isEmpty() ? null : $iterator->current();
    }
}
