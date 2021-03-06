<?php
namespace de\detert\sebastian\slimline\Tests;

use de\detert\sebastian\slimline\db\Handler;
use de\detert\sebastian\slimline\db\Model_Create;
use de\detert\sebastian\slimline\db\Model_Repository;
use de\detert\sebastian\slimline\db\Model_Column;
use de\detert\sebastian\slimline\db\Model_Table;

require_once BASE_DIR . 'repository.php';
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

    /**
     * @covers de\detert\sebastian\slimline\db\Model_Create::__construct
     * @covers de\detert\sebastian\slimline\db\Model_Create::createModels
     * @covers de\detert\sebastian\slimline\db\Model_Create::filePutContents
     * @covers de\detert\sebastian\slimline\db\Model_Column::getDescription
     */
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

        $text = new Model_Column();
        $text->data_type = 'varchar';
        $text->column_type = 'varchar(100)';
        $text->extra = null;
        $text->is_nullable = true;
        $text->column_comment = 'just a text';
        $text->character_maximum_length = null;

        $table = new Model_Table();
        $table->columns['id'] = $id;
        $table->columns['text'] = $text;

        $tables['foo'] = $table;

        $this->repository->expects($this->once())
            ->method('getAllTables')
            ->will($this->returnValue($tables));

        $modelCreate = new Model_Create($this->repository);

        $dir = __DIR__ . DS . 'tmp';
        $dirGenerated = __DIR__ . DS . 'tmpgenerated';

        array_map('unlink', glob($dirGenerated . DS . '*'));
        array_map('unlink', glob($dir . DS . '*'));

        if ( file_exists($dir) ) rmdir($dir);
        if ( file_exists($dirGenerated) ) rmdir($dirGenerated);

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
