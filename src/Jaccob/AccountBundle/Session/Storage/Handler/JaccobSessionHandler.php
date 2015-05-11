<?php

namespace Jaccob\AccountBundle\Session\Storage\Handler;

use Jaccob\AccountBundle\AccountModelAware;

class JaccobSessionHandler implements \SessionHandlerInterface
{
    use AccountModelAware;

    /**
     * {@inheritdoc}
     */
    public function open($savePath, $name)
    {
        return true;
    }

    /**
     * Close the session
     * @link http://www.php.net/manual/en/sessionhandlerinterface.close.php
     * @return bool The return value (usually true on success, false on failure). Note this value is returned internally to PHP for processing.
     */
    public function close()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function read($sessionId)
    {
        $ret = $this
            ->getAccountSession()
            ->getQueryManager()
            ->query("SELECT s.data FROM session AS s WHERE s.id = $*", [$sessionId])
        ;

        foreach ($ret as $object) {
            return $object['data'];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function write($sessionId, $sessionData)
    {
        // FIXME This should merge gracefully (multiple threads in //).
        $this
            ->getAccountSession()
            ->getQueryManager()
            ->query("
                MERGE INTO session AS s
                    ON s.id = $*
                WHEN MATCH THEN
                    UPDATE SET
                        data = $*,
                        touched = NOW()
                WHEN NOT MATCHED
                    THEN INSERT (id, created, touched, data)
                        VALUES ($*, NOW(), NOW(), $*)
                )
            ", [
                $sessionId,
                $sessionData,
                $sessionId,
                $sessionData
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function destroy($sessionId)
    {
        return (bool)count($this
            ->getAccountSession()
            ->getQueryManager()
            ->query("DELETE FROM session WHERE id = $* RETURNING 1", [$sessionId])
        );
    }

    /**
     * Cleanup old sessions
     * @link http://www.php.net/manual/en/sessionhandlerinterface.gc.php
     * @param maxlifetime string <p>
     * Sessions that have not updated for the last maxlifetime seconds will be removed.
     * </p>
     * @return bool The return value (usually true on success, false on failure). Note this value is returned internally to PHP for processing.
     */
    public function gc($maxlifetime)
    {
        // FIXME
    }
}