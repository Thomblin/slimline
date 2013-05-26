<?php
/**
 * @author sebastian.detert <github@elygor.de>
 * @date 26.05.13
 * @time 11:46
 * @license property of Sebastian Detert
 */

namespace de\detert\sebastian\slimline\Db;

use de\detert\sebastian\slimline\IO\Reader;

class Migration
{
    /**
     * @var Handler
     */
    private $db;

    /**
     * @var Reader
     */
    private $reader;

    /**
     * @param Handler $db
     * @param string  $path path where migration files are located
     */
    public function __construct(Handler $db, Reader $reader)
    {
        $this->db     = $db;
        $this->reader = $reader;
    }

    public function update()
    {

    }

    private function getMigrationVersions()
    {
        $sql = "CREATE TABLE IF NOT EXISTS `migration_version` (
            id INT(20) NOT NULL AUTO_INCREMENT,
            VARCHAR(100) filename NOT NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY (`filename`)
        ) ENGINE=InnoDb DEFAULT charset=utf8";

        $this->db->query($sql);

        $sql = "SELECT `filename` FROM `migration_version`";

        return $this->db->fetchIndexedBy($sql, 'filename');
    }

    /**
     * @param array $blacklist get all files that are not contained in this list
     */
    private function getMigrationFiles(array $blacklist)
    {
        $files = $this->reader->getFiles();
    }
}