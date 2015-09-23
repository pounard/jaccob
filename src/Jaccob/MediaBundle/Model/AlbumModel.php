<?php

namespace Jaccob\MediaBundle\Model;

use Jaccob\MediaBundle\Model\Structure\Album as AlbumStructure;

use PommProject\ModelManager\Model\Model;
use PommProject\ModelManager\Model\ModelTrait\WriteQueries;

/**
 * Album model
 */
class AlbumModel extends Model
{
    use WriteQueries;

    /**
     * Default constructor
     */
    public function __construct()
    {
        $this->structure = new AlbumStructure;
        $this->flexible_entity_class = '\Jaccob\MediaBundle\Model\Album';
    }

    public function paginateAlbumsFor($accountId)
    {
        $sql = "
            SELECT DISTINCT(a.id), a.*
            FROM :table a
            LEFT JOIN album_acl aa
                ON aa.id_album = a.id
            WHERE (
                a.id_account = $*
                OR aa.id_account = $*
            )
            ORDER BY a.ts_user_date_end DESC
        ";
  
        $sql = strtr($sql, [
            ':table' => $this->getStructure()->getRelation(),
        ]);

        $values = [$accountId, $accountId];
        // $total  = $this->fetchSingleValue("SELECT COUNT(*) FROM (" . $sql . ") AS c", '', $values);

        return $this->paginateQuery($sql, $values, /* $total */ 100, 100);
    }
}
