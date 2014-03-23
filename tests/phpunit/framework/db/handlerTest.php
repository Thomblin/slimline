<?php
namespace de\detert\sebastian\slimline\Tests;

use de\detert\sebastian\slimline\db\Handler;
use de\detert\sebastian\slimline\Response_Debug_Sql;
use de\detert\sebastian\slimline\db\model\HandlerModel;

require_once BASE_DIR . 'db' . DS . 'model.php';
require_once BASE_DIR . 'db' . DS . 'config.php';
require_once BASE_DIR . 'db' . DS . 'handler.php';
require_once BASE_DIR . 'db' . DS . 'exception' . DS . 'notfound.php';
require_once 'handler_model.php';

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
     * @covers de\detert\sebastian\slimline\db\Handler::__construct
     */
    public function setUp()
    {
        $this->handler = new Handler($this->dbConfig);

        $sql = 'DROP TABLE IF EXISTS `foo`';
        $this->handler->query($sql);

        $sql = 'DROP TABLE IF EXISTS `handler_model`';
        $this->handler->query($sql);
    }

    /**
     * @covers de\detert\sebastian\slimline\db\Handler::query
     * @covers de\detert\sebastian\slimline\db\Handler::prepareAndExecute
     * @covers de\detert\sebastian\slimline\db\Handler::fetchAll
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
     * @covers de\detert\sebastian\slimline\db\Handler::fetch
     */
    public function testShouldFetchOneRow()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS `foo` (`id` INT(20))';
        $this->handler->query($sql);

        $sql = 'INSERT INTO `foo` VALUES (?), (?)';
        $params = array(1, 2);
        $this->handler->query($sql, $params);

        $expected = array('id' => 1);
        $this->assertEquals($expected, $this->handler->fetch("SELECT * from `foo` ORDER BY `id` ASC"));
    }

    /**
     * @covers de\detert\sebastian\slimline\db\Handler::fetchIndexedBy
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

    /**
     * @covers de\detert\sebastian\slimline\db\Handler::loadModel
     */
    public function testShouldReturnModel()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS `handler_model` (`id` INT(20), `text` VARCHAR(100))';
        $this->handler->query($sql);

        $sql = 'INSERT INTO `handler_model` VALUES (?, ?), (?, ?)';
        $params = array(1, 'one', 2, 'two');
        $this->handler->query($sql, $params);

        $actual = $this->handler->loadModel('de\detert\sebastian\slimline\db\model\HandlerModel', array('id' => 1));

        $expected = new HandlerModel();
        $expected->fromArray(
            array(
                'id' => 1,
                'text' => 'one',
            )
        );

        $this->assertEquals(
            $expected,
            $actual,
            "expected\n" . print_r($expected, true) . "\actual\n" . print_r($actual, true)
        );
    }

    /**
     * @covers de\detert\sebastian\slimline\db\Handler::loadModel
     *
     * @expectedException de\detert\sebastian\slimline\db\Exception_Notfound
     */
    public function testShouldThrowExceptionIfModelNotFoundInDb()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS `handler_model` (`id` INT(20), `text` VARCHAR(100))';
        $this->handler->query($sql);

        $this->handler->loadModel('de\detert\sebastian\slimline\db\model\HandlerModel', array('id' => 666));
    }

    /**
     * @covers de\detert\sebastian\slimline\db\Handler::saveModel
     */
    public function testShouldSaveModel()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS `handler_model` (`id` INT(20), `text` VARCHAR(100), PRIMARY KEY (`id`))';
        $this->handler->query($sql);

        $expected = new HandlerModel();
        $expected->fromArray(
            array(
                'id' => 1,
                'text' => 'one',
            )
        );

        $this->handler->saveModel($expected);

        $actual = $this->handler->loadModel('de\detert\sebastian\slimline\db\model\HandlerModel', array('id' => 1));

        $this->assertEquals($expected->toArray(), $actual->toArray());

        $expected->fromArray(
            array(
                'id' => 1,
                'text' => 'second',
            )
        );

        $this->handler->saveModel($expected);

        $actual = $this->handler->loadModel('de\detert\sebastian\slimline\db\model\HandlerModel', array('id' => 1));

        $this->assertEquals($expected->toArray(), $actual->toArray());
    }

    /**
     * @covers de\detert\sebastian\slimline\db\Handler::loadAllModels
     */
    public function testShouldReturnAllModels()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS `handler_model` (`id` INT(20), `text` VARCHAR(100))';
        $this->handler->query($sql);

        $sql = 'INSERT INTO `handler_model` VALUES (?, ?), (?, ?)';
        $params = array(1, 'one', 2, 'two');
        $this->handler->query($sql, $params);

        $actual = $this->handler->loadAllModels('de\detert\sebastian\slimline\db\model\HandlerModel', array());

        $model1 = new HandlerModel();
        $model1->fromArray(
            array(
                'id' => 1,
                'text' => 'one',
            )
        );
        $model2 = new HandlerModel();
        $model2->fromArray(
            array(
                'id' => 2,
                'text' => 'two',
            )
        );

        $this->assertEquals(
            array($model1, $model2),
            $actual,
            "expected\n" . print_r(array($model1, $model2), true) . "\actual\n" . print_r($actual, true)
        );
    }

    /**
     * testShouldGetAffectedRows
     *
     * @param $debug
     *
     * @dataProvider getResponseDebugSql
     * @covers de\detert\sebastian\slimline\db\Handler::getAffectedRows
     */
    public function testShouldGetAffectedRows(Response_Debug_Sql $debug = null)
    {
        $handler = new Handler($this->dbConfig);
        if ( !is_null($debug) ) {
            $handler->setDebugResponse($debug);
        }

        $sql = 'CREATE TABLE IF NOT EXISTS `foo` (`id` INT(20))';
        $this->handler->query($sql);

        $sql = 'INSERT INTO `foo` VALUES (?), (?)';
        $params = array(1, 2);
        $handler->query($sql, $params);

        $this->assertEquals(2, $handler->getAffectedRows());
    }

    public function getResponseDebugSql()
    {
        return array(
            array(new Response_Debug_Sql()),
            array(null),
        );
    }

    /**
     * testShouldGetInsertId
     *
     * @param $debug
     *
     * @dataProvider getResponseDebugSql
     * @covers de\detert\sebastian\slimline\db\Handler::getAffectedRows
     */
    public function testShouldGetInsertId(Response_Debug_Sql $debug = null)
    {
        $handler = new Handler($this->dbConfig);
        if ( !is_null($debug) ) {
            $handler->setDebugResponse($debug);
        }

        $sql = 'CREATE TABLE IF NOT EXISTS `foo` (`id` INT(20) AUTO_INCREMENT, PRIMARY KEY (`id`))';
        $this->handler->query($sql);

        $sql = 'INSERT INTO `foo` VALUES (NULL)';
        $params = array();

        $handler->query($sql, $params);
        $this->assertEquals(1, $handler->getLastInsertId());

        $handler->query($sql, $params);
        $this->assertEquals(2, $handler->getLastInsertId());
    }

    /**
     * testShouldGetFoundRows
     *
     * @param $debug
     *
     * @dataProvider getResponseDebugSql
     * @covers de\detert\sebastian\slimline\db\Handler::getAffectedRows
     */
    public function testShouldGetFoundRows(Response_Debug_Sql $debug = null)
    {
        $handler = new Handler($this->dbConfig);
        if ( !is_null($debug) ) {
            $handler->setDebugResponse($debug);
        }

        $sql = 'CREATE TABLE IF NOT EXISTS `foo` (`id` INT(20))';
        $this->handler->query($sql);

        $sql = 'INSERT INTO `foo` VALUES (?), (?)';
        $params = array(1, 2);
        $handler->query($sql, $params);

        $handler->query("SELECT SQL_CALC_FOUND_ROWS * FROM `foo` LIMIT 1");

        $this->assertEquals(2, $handler->getFoundRows());
    }
}
