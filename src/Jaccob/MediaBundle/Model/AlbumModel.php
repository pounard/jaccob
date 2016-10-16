<?php

namespace Jaccob\MediaBundle\Model;

use Jaccob\MediaBundle\Model\Structure\Album as AlbumStructure;

use PommProject\Foundation\Exception\SqlException;
use PommProject\Foundation\Where;
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

    /**
     * Is album into session
     *
     * @param int $albumId
     * @param string $sessionId
     */
    public function isAlbumInSession($albumId, $sessionId)
    {
        $sql = "
            SELECT EXISTS (
                SELECT TRUE
                FROM session_share
                WHERE :condition
            ) AS result
        ";

        $where = (new Where())
            ->andWhere('id_album = $*', [$albumId])
            ->andWhere('id_session = $*', [$sessionId])
        ;

        return (bool)$this->fetchSingleValue($sql, $where, []);
    }

    /**
     * Save album into session, do nothing when already exists
     *
     * @param int $albumId
     * @param string $sessionId
     */
    public function saveAlbumInSession($albumId, $sessionId)
    {
        $sql = "INSERT INTO session_share (id_session, id_album) SELECT $*, $*";

        try {
            $this
                ->getSession()
                ->getClientUsingPooler('prepared_query', $sql)
                ->execute([$sessionId, $albumId])
            ;
        } catch (SqlException $e) {
            // FIXME Sorry could not find better way to do insert ignore
        }
    }

    public function paginateAlbumsForSession($sessionId)
    {
        $sql = "
            SELECT DISTINCT(a.id), a.*
            FROM :table a
            JOIN session_share ss
                ON ss.id_album = a.id
            WHERE (
                ss.id_session = $*
            )
            ORDER BY a.ts_user_date_begin DESC
        ";

        $sql = strtr($sql, [
            ':table' => $this->getStructure()->getRelation(),
        ]);

        $values = [$sessionId];
        // $total  = $this->fetchSingleValue("SELECT COUNT(*) FROM (" . $sql . ") AS c", '', $values);

        return $this->paginateQuery($sql, $values, /* $total */ 100, 100);
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
            ORDER BY a.ts_user_date_begin DESC
        ";

        $sql = strtr($sql, [
            ':table' => $this->getStructure()->getRelation(),
        ]);

        $values = [$accountId, $accountId];
        // $total  = $this->fetchSingleValue("SELECT COUNT(*) FROM (" . $sql . ") AS c", '', $values);

        return $this->paginateQuery($sql, $values, /* $total */ 100, 100);
    }
}
