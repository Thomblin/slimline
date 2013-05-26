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
        $this->initMigrationTable();
        $this->doMigration();
    }

    private function initMigrationTable()
    {
        $sql = "CREATE TABLE IF NOT EXISTS `migration_version` (
            `id` INT(20) NOT NULL AUTO_INCREMENT,
            `filename` VARCHAR(100) NOT NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY (`filename`)
        ) ENGINE=InnoDb DEFAULT charset=utf8";

        $this->db->query($sql);
    }

    /**
     * Gets an array of the database migrations.
     *
     * @return array
     */
    private function doMigration()
    {
        $filesDone  = $this->getMigrationVersions();
        $filesKnown = $this->getMigrationFiles();

        $filesForUpdate = $this->getFilesForUpdate($filesKnown, $filesDone);

        $classesForUpdate = $this->getMigrationClasses($filesForUpdate);

        $this->upAction($classesForUpdate);
    }

    private function getMigrationVersions()
    {
        $sql = "SELECT `filename` FROM `migration_version`";

        return $this->db->fetchIndexedBy($sql, 'filename');
    }

    private function getMigrationFiles()
    {
        return $this->reader->getFiles();
    }

    /**
     * @param $filesKnown
     * @param $filesDone
     * @return array
     */
    private function getFilesForUpdate($filesKnown, $filesDone)
    {
        $filesForUpdate = array();

        foreach ($filesKnown as $filename) {
            $shortFilename = substr(basename($filename), 0, -4);
            if (!isset($filesDone[$shortFilename])) {
                $filesForUpdate[] = $filename;
            }
        }
        return $filesForUpdate;
    }

    /**
     * @param array $files array contains path to migration files
     *
     * @return array
     *
     * @throws \InvalidArgumentException
     */
    private function getMigrationClasses(array $files)
    {
        $versions = array();

        foreach ($files as $filePath) {
            if (preg_match('/(([0-9]+)_([a-z0-9_]*)).php$/i', basename($filePath), $match)) {
                $fileName = $match[1];

                $className  = 'de\\detert\\sebastian\\slimline\Db\\';
                $className .= ucfirst( preg_replace( '/_(.?)/e', "strtoupper('$1')", strtolower( $match[3] ) ) );
                $className .= $match[2];

                require_once $filePath;
                if (!class_exists($className)) {
                    throw new \InvalidArgumentException("file '$filePath' does not contain class '$className'");
                }

                $class = new $className($this->db);

                if (!($class instanceof MigrationStatement)) {
                    throw new \InvalidArgumentException("class '$className' must extend MigrationStatement");
                }

                $versions[$fileName] = $class;
            }
        }

        ksort($versions);

        return $versions;
    }

    /**
     * @param array $classes array containing all classes which should be executed
     */
    private function upAction(array $classes)
    {
        foreach( $classes as $filename => $class ) {
            $class->up();

            $sql = "INSERT INTO `migration_version` (`id`, `filename`) VALUES (NULL, ?)";
            $this->db->query($sql, array($filename));
        }
    }
}