<?php
namespace de\detert\sebastian\slimline\Tests;

use de\detert\sebastian\slimline\db\Handler;
use de\detert\sebastian\slimline\db\Model_Create;
use de\detert\sebastian\slimline\db\Model_Repository;
use de\detert\sebastian\slimline\db\Model_Column;
use de\detert\sebastian\slimline\db\Model_Table;

require_once BASE_DIR . 'db' . DS . 'config.php';
require_once BASE_DIR . 'db' . DS . 'handler.php';
require_once BASE_DIR . 'db' . DS . 'model' . DS . 'table.php';
require_once BASE_DIR . 'db' . DS . 'model' . DS . 'column.php';
require_once BASE_DIR . 'db' . DS . 'model' . DS . 'repository.php';
require_once BASE_DIR . 'db' . DS . 'model' . DS . 'create.php';

/**
 * @author sebastian.detert <github@elygor.de>
 * @date 13.01.13
 * @time 14:23
 * @license property of Sebastian Detert
 */
class DbModelCreateTest extends Helper\TestCase
{
    /**
     * @var Handler
     */
    private $handler;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $repository;

    /**
     * @covers de\detert\sebastian\slimline\db\Handler::__construct
     */
    public function setUp()
    {
        $this->handler = new Handler($this->dbConfig);

        $this->repository = $this->getMock(
            'de\detert\sebastian\slimline\db\Model_Repository',
            array('getAllTables'),
            array(),
            '',
            false
        );
    }

    public function testShouldReturnAllTables()
    {
        $tables = array();

        $id = new Model_Column();
        $id->data_type = 'int';
        $id->column_type = 'int(20)';
        $id->extra = 'auto_increment';
        $id->is_nullable = false;
        $id->column_comment = '';
        $id->character_maximum_length = null;

        $table = new Model_Table();
        $table->columns['id'] = $id;

        $tables['foo'] = $table;

        $this->repository->expects($this->once())
            ->method('getAllTables')
            ->will($this->returnValue($tables));

        $modelCreate = new Model_Create($this->repository);

        $dir = __DIR__ . DS . 'tmp';
        $dirGenerated = __DIR__ . DS . 'tmp' . DS . 'generated';
        array_map('unlink', glob($dirGenerated . DS . '*'));

        unlink($dir . DS . 'foo.php');
        $modelCreate->createModels($dir);

        $this->assertTrue(file_exists($dirGenerated . DS . 'foo.php'));

        $this->assertEquals(
            file_get_contents( __DIR__ . DS . 'foo.php'),
            file_get_contents( $dirGenerated . DS . 'foo.php')
        );

        $this->assertTrue(file_exists($dir . DS . 'foo.php'));

        $this->assertEquals(
            file_get_contents( __DIR__ . DS . 'foo_extend.php'),
            file_get_contents( $dir . DS . 'foo.php')
        );
    }
}
