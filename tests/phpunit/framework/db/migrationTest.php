<?php
namespace de\detert\sebastian\slimline\Tests;

use de\detert\sebastian\slimline\db\Handler;
use de\detert\sebastian\slimline\db\Migration;
use de\detert\sebastian\slimline\db\Migration_Repository;
use de\detert\sebastian\slimline\Io\Reader;

require_once BASE_DIR . 'db' . DS . 'config.php';
require_once BASE_DIR . 'db' . DS . 'handler.php';
require_once BASE_DIR . 'db' . DS . 'migration.php';
require_once BASE_DIR . 'db' . DS . 'migration' . DS . 'repository.php';
require_once BASE_DIR . 'db' . DS . 'migration' . DS . 'statement.php';
require_once BASE_DIR . 'io' . DS . 'reader.php';

/**
 * @author sebastian.detert <github@elygor.de>
 * @date 13.01.13
 * @time 14:23
 * @license property of Sebastian Detert
 */
class DbMigrationTest extends Helper\TestCase
{
    /**
     * @var Handler
     */
    private $handler;
    /**
     * @var Migration
     */
    private $migration;


    /**
     * @covers de\detert\sebastian\slimline\db\Handler::__construct
     */
    public function setUp()
    {
        $this->handler = new Handler($this->dbConfig);

        $sql = "SELECT
                CONCAT('DROP TABLE IF EXISTS `', table_name, '`;') AS query
            FROM
                information_schema.tables
            WHERE table_schema = ?";
        $queries = $this->handler->fetchAll($sql, array($this->dbConfig->dbName));

        foreach ( $queries as $sql ) {
            $this->handler->query($sql['query']);
        }
    }

    /**
     * @param $path
     */
    private function initMigrationClass($path)
    {
        $reader = new Reader($path, '/.*\.php/');
        $repository = new Migration_Repository($this->handler);

        $this->migration = new Migration($repository, $reader);
    }

    /**
     * @covers de\detert\sebastian\slimline\db\Migration::__construct
     * @covers de\detert\sebastian\slimline\db\Migration::update
     * @covers de\detert\sebastian\slimline\db\Migration_Repository::initMigrationTable
     */
    public function testShouldInitializeMigrationTable()
    {
        $this->initMigrationClass(__DIR__ . DS . 'migration' . DS . 'empty');
        $this->migration->update();

        $sql = "SHOW CREATE TABLE `migration_version`";
        $actual = $this->handler->fetchAll($sql);
        $expected = array (
            'Table' => 'migration_version',
            'Create Table' => 'CREATE TABLE `migration_version` (' . "\n" .
                '  `id` int(20) NOT NULL AUTO_INCREMENT,' . "\n" .
                '  `filename` varchar(100) NOT NULL,' . "\n" .
                '  PRIMARY KEY (`id`),' . "\n" .
                '  UNIQUE KEY `filename` (`filename`)' . "\n" .
              ') ENGINE=InnoDB DEFAULT CHARSET=utf8'
        );

        $this->assertSame($expected, $actual[0]);
    }

    /**
     * @covers de\detert\sebastian\slimline\db\Migration::__construct
     * @covers de\detert\sebastian\slimline\db\Migration::update
     * @covers de\detert\sebastian\slimline\db\Migration::doMigration
     * @covers de\detert\sebastian\slimline\db\Migration_Repository::getMigrationVersions
     * @covers de\detert\sebastian\slimline\db\Migration::getFilesForUpdate
     * @covers de\detert\sebastian\slimline\db\Migration::getMigrationClasses
     * @covers de\detert\sebastian\slimline\db\Migration::upAction
     * @covers de\detert\sebastian\slimline\db\Migration_Repository::insertMigrationVersion
     */
    public function testShouldPerformMigration()
    {
        $actual   = $this->handler->fetchAll("SHOW TABLES");
        $expected = array();

        $this->assertSame($expected, $actual);

        $this->initMigrationClass(__DIR__ . DS . 'migration' . DS . 'step1');
        $this->migration->update();

        $actual   = $this->handler->fetchAll("SHOW TABLES");
        $expected = array(
            array('Tables_in_slimline_test' => 'migration_version'),
            array('Tables_in_slimline_test' => 'foo'),
        );

        $this->assertEquals($expected, $actual, '', 0, 10, true);

        $sql      = "SELECT * FROM `migration_version`";
        $actual   = $this->handler->fetchAll($sql);
        $expected = array(
            array('id' => 1, 'filename' => '1_create_foo')
        );

        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers de\detert\sebastian\slimline\db\Migration::__construct
     * @covers de\detert\sebastian\slimline\db\Migration::update
     * @covers de\detert\sebastian\slimline\db\Migration::doMigration
     * @covers de\detert\sebastian\slimline\db\Migration_Repository::getMigrationVersions
     * @covers de\detert\sebastian\slimline\db\Migration::getFilesForUpdate
     * @covers de\detert\sebastian\slimline\db\Migration::getMigrationClasses
     * @covers de\detert\sebastian\slimline\db\Migration::upAction
     */
    public function testShouldNotPerformMigration1()
    {
        $actual   = $this->handler->fetchAll("SHOW TABLES");
        $expected = array();

        $this->assertSame($expected, $actual);

        // create migration_version table
        $this->initMigrationClass(__DIR__ . DS . 'migration' . DS . 'empty');
        $this->migration->update();

        $sql = "INSERT INTO `migration_version` (`id`, `filename`) VALUES (NULL, ?)";
        $this->handler->query($sql, array('1_throw_exception'));

        // test starts here
        $this->initMigrationClass(__DIR__ . DS . 'migration' . DS . 'step2');
        $this->migration->update();

        $actual   = $this->handler->fetchAll("SHOW TABLES");
        $expected = array(
            array('Tables_in_slimline_test' => 'migration_version'),
            array('Tables_in_slimline_test' => 'foo'),
        );

        $this->assertEquals($expected, $actual, '', 0, 10, true);

        $sql      = "SELECT * FROM `migration_version`";
        $actual   = $this->handler->fetchAll($sql);
        $expected = array(
            array('id' => 1, 'filename' => '1_throw_exception'),
            array('id' => 2, 'filename' => '2_create_foo'),
        );

        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers de\detert\sebastian\slimline\db\Migration::getMigrationClasses
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage 1_no_class.php' does not contain class 'de\detert\sebastian\slimline\db\NoClass1'
     */
    public function testShouldThrowErrorIfClassIsMissing()
    {
        $this->initMigrationClass(__DIR__ . DS . 'migration' . DS . 'no_class');
        $this->migration->update();
    }

    /**
     * @covers de\detert\sebastian\slimline\db\Migration::getMigrationClasses
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage must extend Migration_Statement
     */
    public function testShouldThrowErrorIfClassHasWrongType()
    {
        $this->initMigrationClass(__DIR__ . DS . 'migration' . DS . 'wrong_class');
        $this->migration->update();
    }
}
