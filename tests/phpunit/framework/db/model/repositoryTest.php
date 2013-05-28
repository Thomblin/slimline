<?php
namespace de\detert\sebastian\slimline\Tests;

use de\detert\sebastian\slimline\db\Handler;
use de\detert\sebastian\slimline\db\Model_Repository;
use de\detert\sebastian\slimline\db\Model_Column;
use de\detert\sebastian\slimline\db\Model_Table;

require_once BASE_DIR . 'db' . DS . 'config.php';
require_once BASE_DIR . 'db' . DS . 'handler.php';
require_once BASE_DIR . 'db' . DS . 'model' . DS . 'table.php';
require_once BASE_DIR . 'db' . DS . 'model' . DS . 'column.php';
require_once BASE_DIR . 'db' . DS . 'model' . DS . 'repository.php';

/**
 * @author sebastian.detert <github@elygor.de>
 * @date 13.01.13
 * @time 14:23
 * @license property of Sebastian Detert
 */
class DbModelRepositoryTest extends Helper\TestCase
{
    /**
     * @var Handler
     */
    private $handler;
    /**
     * @var Model_Repository
     */
    private $repository;

    /**
     * @covers de\detert\sebastian\slimline\db\Handler::__construct
     */
    public function setUp()
    {
        $this->handler = new Handler($this->dbConfig);

        $this->repository = new Model_Repository($this->handler);

        $sql = 'DROP TABLE IF EXISTS `foo`';
        $this->handler->query($sql);

        $sql = 'DROP TABLE IF EXISTS `misc`';
        $this->handler->query($sql);
    }

    public function testShouldReturnAllTables()
    {
        $sql = 'CREATE TABLE `foo` (
            `id` INT(20) AUTO_INCREMENT,
            `text` VARCHAR(100) COMMENT "just a text",
            PRIMARY KEY (`id`)
        )';
        $this->handler->query($sql);

        $sql = 'CREATE TABLE `misc` (
            `misc_id` TINYINT(3) UNSIGNED,
            `date` DATETIME NOT NULL DEFAULT 0
        )';
        $this->handler->query($sql);

        $actual = $this->repository->getAllTables();

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
        $text->extra = '';
        $text->is_nullable = true;
        $text->column_comment = 'just a text';
        $text->character_maximum_length = 100;

        $foo = new Model_Table();
        $foo->columns['id'] = $id;
        $foo->columns['text'] = $text;

        $miscId = new Model_Column();
        $miscId->data_type = 'tinyint';
        $miscId->column_type = 'tinyint(3) unsigned';
        $miscId->extra = '';
        $miscId->is_nullable = true;
        $miscId->column_comment = '';
        $miscId->character_maximum_length = null;

        $date = new Model_Column();
        $date->data_type = 'datetime';
        $date->column_type = 'datetime';
        $date->extra = '';
        $date->is_nullable = false;
        $date->column_comment = '';
        $date->character_maximum_length = null;

        $misc = new Model_Table();
        $misc->columns['misc_id'] = $miscId;
        $misc->columns['date'] = $date;

        $expected = array(
            'foo' =>$foo,
            'misc' => $misc,
        );

        foreach ( $expected as $table => $columns ) {
            $this->assertTrue(isset($actual[$table]), "expected $table in result");
            $this->assertEquals($expected[$table], $actual[$table]);
        }
    }
}
