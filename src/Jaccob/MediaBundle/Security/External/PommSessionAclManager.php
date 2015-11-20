<?php

namespace Jaccob\MediaBundle\Security\External;

use Jaccob\MediaBundle\Model\Pomm\StuffThatDoesQueriesTrait;

use PommProject\Foundation\Where;

class PommSessionAclManager implements SessionAclManagerInterface
{
    use StuffThatDoesQueriesTrait;

    /**
     * {intheritdoc}
     */
    public function addAlbumAuthorization($sessionId, $albumIdList, $lifetime = self::DEFAULT_LIFETIME)
    {
        // Very clever man there, all credits to him:
        //   https://stackoverflow.com/a/8702291

        if (empty($albumIdList)) {
            return;
        }

        $statements   = [];
        $values       = [];

        foreach ($albumIdList as $albumId) {
            $albumId = (int)$albumId;
            $statements[] = "({$albumId}, $*)";
            //$values[] = $albumId;
            $values[] = $sessionId;
        }

        $values[] = (new \DateTime(sprintf("now +%d second", $lifetime)))->format('Y-m-d H:i:s');

        $sql = strtr(
            "
                WITH new_values (id_album, id_session) AS (VALUES :values)
                INSERT INTO external_session_acl (
                    id_album, id_session, ts_expire
                )
                SELECT id_album, id_session, $*
                FROM new_values
                WHERE NOT EXISTS (
                    SELECT 1
                    FROM external_session_acl e
                    JOIN new_values n
                        ON e.id_album = n.id_album
                        AND e.id_session = n.id_session
                )
            ",
            [
                ':values' => join(',', $statements),
            ]
        );

        $this->query($sql, $values);
    }

    /**
     * {intheritdoc}
     */
    public function removeAlbumAuthorization($sessionId, $albumIdList)
    {
        $sql = strtr(
            "
                DELETE FROM external_session_acl
                WHERE
                    id_session = $*
                    AND id_album IN (:idAlbums)
            ",
            [
                ':idAlbums' => join(',', array_fill(0, count($albumIdList), '$*')),
            ]
        );

        $values = $albumIdList;
        array_unshift($values, $sessionId);

        $this->query($sql, $values);
    }

    /**
     * {intheritdoc}
     */
    public function deleteForSession($sessionId)
    {
        $this->query("DELETE FROM external_session_acl WHERE id_session = $*");
    }

    /**
     * {intheritdoc}
     */
    public function deleteExpired()
    {
        $this->query("DELETE FROM external_session_acl WHERE ts_expire < NOW()");
    }

    /**
     * {intheritdoc}
     */
    public function deleteAll()
    {
        $this->query("DELETE FROM external_session_acl");
    }
}
