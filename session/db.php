<?php

namespace de\detert\sebastian\slimline\session;

use de\detert\sebastian\slimline\db\Handler AS dbHandler;

/**
 * @author sebastian.detert <github@elygor.de>
 * @date 23.06.13
 * @time 15:01
 * @license property of Sebastian Detert
 */
class Db implements Handler
{
    /**
     * @var dbHandler
     */
    private $db;
    /**
     * @var string
     */
    private $table;

    /**
     * @param dbHandler $db
     * @param string $table
     */
    function __construct(dbHandler $db, $table = 'session')
    {
        $this->db    = $db;
        $this->table = $table;
    }

    public function startSession()
    {
        session_set_save_handler(
            array($this, "open"),
            array($this, "close"),
            array($this, "read"),
            array($this, "write"),
            array($this, "destroy"),
            array($this, "gc")
        );

        session_start();
    }

    /**
     * creates the mysql table to store session
     */
    public function createTable()
    {
        $sql = "
            CREATE TABLE `".$this->table."` (
                `id` VARCHAR(32) NOT NULL,
                `created` DATETIME NOT NULL,
                `updated` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                `value` MEDIUMTEXT NOT NULL DEFAULT '',
                PRIMARY KEY (`id`)
            )";
        $this->db->query($sql);
    }

    /**
     * @return bool
     */
    public function close()
    {
        return true;
    }

    /**
     * @param string $sessionId
     *
     * @return bool
     */
    public function destroy($sessionId)
    {
        $sql = "DELETE FROM `".$this->table."` WHERE `id` = ?";
        $this->db->query($sql, array($sessionId));

        return true;
    }

    /**
     * @return bool
     */
    public function gc($maxLifetime)
    {
        $sql = "DELETE FROM `".$this->table."` WHERE `updated` < DATE_SUB(NOW(), INTERVAL ? SECOND)";
        $this->db->query($sql, array($maxLifetime));

        return true;
    }

    /**
     * @param string $savePath
     * @param string $sessionName
     *
     * @return bool
     */
    public function open($savePath, $sessionName)
    {
        return true;
    }

    /**
     * @param string $sessionId
     *
     * @return string
     */
    public function read($sessionId)
    {
        $sql    = "SELECT `value` FROM `".$this->table."` WHERE `id` = ?";
        $result = $this->db->fetch($sql, array($sessionId));

        return $result['value'] ?: '';
    }

    /**
     * @param string $sessionId
     * @param string $sessionData
     *
     * @return bool
     */
    public function write($sessionId, $sessionData)
    {
        $sql   = "INSERT INTO `".$this->table."`
            (`id`, `created`, `updated`, `value`) VALUES
            (:id, NOW(), NOW(), :value)
            ON DUPLICATE KEY UPDATE `value`=:value, `updated`=NOW()";
        $this->db->query($sql, array('id' => $sessionId, 'value' => $sessionData));

        return true;
    }

}