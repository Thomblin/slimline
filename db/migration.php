<?php
namespace de\detert\sebastian\slimline\db;

use de\detert\sebastian\slimline\IO\Reader;

/**
 * @author sebastian.detert <github@elygor.de>
 * @date 26.05.13
 * @time 11:46
 * @license property of Sebastian Detert
 */
class Migration
{
    /**
     * @var Migration_Repository
     */
    private $repository;

    /**
     * @var Reader
     */
    private $reader;

    /**
     * @param Handler $db
     * @param string  $path path where migration files are located
     */
    public function __construct(Migration_Repository $repository, Reader $reader)
    {
        $this->repository = $repository;
        $this->reader     = $reader;
    }

    public function update()
    {
        $this->repository->initMigrationTable();
        $this->doMigration();
    }

    /**
     * Gets an array of the database migrations.
     *
     * @return array
     */
    private function doMigration()
    {
        $filesDone  = $this->repository->getMigrationVersions();
        $filesKnown = $this->reader->getFiles();

        $filesForUpdate = $this->getFilesForUpdate($filesKnown, $filesDone);

        $classesForUpdate = $this->getMigrationClasses($filesForUpdate);

        $this->upAction($classesForUpdate);
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

        $strtoupper = function ($match) {
            return strtoupper($match[1]);
        };

        foreach ($files as $filePath) {
            if (preg_match('/(([0-9]+)_([a-z0-9_]*)).php$/i', basename($filePath), $match)) {
                $fileName = $match[1];

                $className  = 'de\\detert\\sebastian\\slimline\db\\';
                $className .= ucfirst( preg_replace_callback( '/_(.?)/', $strtoupper, strtolower( $match[3] ) ) );
                $className .= $match[2];

                require_once $filePath;
                if (!class_exists($className)) {
                    throw new \InvalidArgumentException("file '$filePath' does not contain class '$className'");
                }

                $class = new $className($this->repository->getHandler());

                if (!($class instanceof Migration_Statement)) {
                    throw new \InvalidArgumentException("class '$className' must extend Migration_Statement");
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

            $this->repository->insertMigrationVersion($filename);
        }
    }
}