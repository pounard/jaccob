<?php

namespace Wps\Session;

use Wps\Util\Date;

class DatabaseSessionHandler implements \SessionHandlerInterface
{
    /**
     * @var \PDO
     */
    private $db;

    /**
     * @var boolean
     */
    private $opened = false;

    /**
     * Default constructor
     *
     * @param \PDO $db
     *   Database
     */
    public function __construct(\PDO $db)
    {
        $this->db = $db;
    }

    public function close()
    {
        $this->opened = false;
    }

    public function destroy($session_id)
    {
        $this
            ->db
            ->prepare("DELETE FROM session WHERE id = ?")
            ->execute(array($session_id));

        return true;
    }

    public function gc($maxlifetime)
    {
        $date = new \Datetime('@' . (time() - $maxlifetime));

        $this
            ->db
            ->prepare("DELETE FROM session WHERE touched < ?")
            ->execute(array($date->format(Date::MYSQL_DATETIME)));

        return true;
    }

    public function open($save_path, $name)
    {
        return $this->opened = true;
    }

    public function read($session_id)
    {
        if (!$this->opened) {
            return false;
        }

        $st = $this->db->prepare("SELECT data FROM session WHERE id = ?");
        $st->setFetchMode(\PDO::FETCH_COLUMN, 0);
        $st->execute(array($session_id));
        foreach ($st as $value) {
            return $value;
        }
    }

    public function write($session_id, $session_data)
    {
        if (!$this->opened) {
            return false;
        }

        $exists = false;
        $time = new \DateTime();

        $st = $this->db->prepare("SELECT 1 FROM session WHERE id = ?");
        $st->setFetchMode(\PDO::FETCH_COLUMN, 0);
        $st->execute(array($session_id));
        foreach ($st as $value) {
            $exists = true;
        }

        if ($exists) {
            $st = $this->db->prepare("
                UPDATE session
                SET
                    touched = ?,
                    data = ?
                WHERE id = ?
            ");
            $st->execute(array(
                $time->format(Date::MYSQL_DATETIME),
                $session_data,
                $session_id,
            ));
        } else {
            $st = $this->db->prepare("
                  INSERT INTO session (
                      id,
                      touched,
                      created,
                      data
                  ) VALUES (?, ?, ?, ?)
            ");
            $st->execute(array(
                $session_id,
                $time->format(Date::MYSQL_DATETIME),
                $time->format(Date::MYSQL_DATETIME),
                $session_data,
            ));
        }

        return true;
    }
}
