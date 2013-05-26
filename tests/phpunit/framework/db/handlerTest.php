<?php
namespace de\detert\sebastian\slimline\Tests;

use de\detert\sebastian\slimline\Db\Handler;

require_once BASE_DIR . 'db' . DS . 'config.php';
require_once BASE_DIR . 'db' . DS . 'handler.php';

/**
 * @author sebastian.detert <github@elygor.de>
 * @date 13.01.13
 * @time 14:23
 * @license property of Sebastian Detert
 */
class DbHandlerTest extends Helper\TestCase
{
    /**
     * @var Handler
     */
    private $handler;


    /**
     * @covers de\detert\sebastian\slimline\Db\Handler::__construct
     */
    public function setUp()
    {
        $this->handler = new Handler($this->dbConfig);

        $sql = 'DROP TABLE IF EXISTS `foo`';
        $this->handler->query($sql);
    }

    /**
     * @covers de\detert\sebastian\slimline\Db\Handler::query
     * @covers de\detert\sebastian\slimline\Db\Handler::fetchAll
     */
    public function testShouldExecuteQuery()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS `foo` (`id` INT(20))';
        $this->handler->query($sql);

        $sql = 'INSERT INTO `foo` VALUES (?), (?)';
        $params = array(1, 2);
        $this->handler->query($sql, $params);

        $expected = array(
            0 => array('id' => 1),
            1 => array('id' => 2),
        );
        $this->assertEquals($expected, $this->handler->fetchAll("SELECT * from `foo`"));
    }

    /**
     * @covers de\detert\sebastian\slimline\Db\Handler::fetchIndexedBy
     */
    public function testShouldReturnRowsByIndex()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS `foo` (`id` INT(20), `misc` VARCHAR(3))';
        $this->handler->query($sql);

        $sql = 'INSERT INTO `foo` VALUES (?, ?), (?, ?)';
        $params = array(1, 'one', 2, 'two');
        $this->handler->query($sql, $params);

        $expected = array(
            'one' => array('id' => 1, 'misc' => 'one'),
            'two' => array('id' => 2, 'misc' => 'two'),
        );
        $this->assertEquals($expected, $this->handler->fetchIndexedBy("SELECT * from `foo`", 'misc'));
    }
}
