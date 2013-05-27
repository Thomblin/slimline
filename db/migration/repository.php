<?php
/**
 * @author sebastian.detert <github@elygor.de>
 * @date 26.05.13
 * @time 22:28
 * @license property of Sebastian Detert
 */

namespace de\detert\sebastian\slimline\db;

class Migration_Repository
{
    protected $db;

    public function __construct(Handler $db)
    {
        $this->db = $db;
    }

    /**
     * @return Handler
     */
    public function getHandler()
    {
        return $this->db;
    }

    public function initMigrationTable()
    {
        $sql = "CREATE TABLE IF NOT EXISTS `migration_version` (
            `id` INT(20) NOT NULL AUTO_INCREMENT,
            `filename` VARCHAR(100) NOT NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY (`filename`)
        ) ENGINE=InnoDb DEFAULT charset=utf8";

        $this->db->query($sql);
    }

    public function getMigrationVersions()
    {
        $sql = "SELECT `filename` FROM `migration_version`";

        return $this->db->fetchIndexedBy($sql, 'filename');
    }

    public function insertMigrationVersion($filename)
    {
        $sql = "INSERT INTO `migration_version` (`id`, `filename`) VALUES (NULL, ?)";
        $this->db->query($sql, array($filename));
    }
}