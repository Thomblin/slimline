<?php
namespace de\detert\sebastian\slimline\Tests;

use de\detert\sebastian\slimline\db\Handler;
use de\detert\sebastian\slimline\db\model\HandlerModel;
use de\detert\sebastian\slimline\session\Db;

require_once BASE_DIR . 'db' . DS . 'model.php';
require_once BASE_DIR . 'db' . DS . 'config.php';
require_once BASE_DIR . 'db' . DS . 'handler.php';
require_once BASE_DIR . 'db' . DS . 'exception' . DS . 'notfound.php';
require_once BASE_DIR . 'session' . DS . 'handler.php';
require_once BASE_DIR . 'session' . DS . 'db.php';

/**
 * @author sebastian.detert <github@elygor.de>
 * @date 13.01.13
 * @time 14:23
 * @license property of Sebastian Detert
 */
class SessionDbTest extends Helper\TestCase
{
    /**
     * @var Handler
     */
    private $handler;

    /**
     * we need process isolation to prevent "headers already sent"
     *
     * but process isolation triggers another error, because phpunit tries to load all previously included files
     * where throw_exception.php should not be called
     *
     * however disabled globalState prevents constants in bootstrap.php to be loaded
     *
     * that is why we need this ugly hack method
     *
     * @param \Text_Template $template
     */
    protected function prepareTemplate(\Text_Template $template) {
        $template->setVar(array(
            'iniSettings' => '',
            'constants' => '',
            'included_files' => '',
            'globals' => '$GLOBALS[\'__PHPUNIT_BOOTSTRAP\'] = ' . var_export($GLOBALS['__PHPUNIT_BOOTSTRAP'], TRUE) . ";\n",
        ));
    }

    /**
     * @covers de\detert\sebastian\slimline\db\Handler::__construct
     */
    public function setUp()
    {
        $this->handler = new Handler($this->dbConfig);

        $sql = 'DROP TABLE IF EXISTS `session`';
        $this->handler->query($sql);
    }

    /**
     * @covers de\detert\sebastian\slimline\session\Db::__construct
     * @covers de\detert\sebastian\slimline\session\Db::createTable
     * @covers de\detert\sebastian\slimline\session\Db::startSession
     * @covers de\detert\sebastian\slimline\session\Db::write
     * @covers de\detert\sebastian\slimline\session\Db::close
     *
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     * @backupGlobals disabled
     * @backupStaticAttributes disabled
     */
    public function testShouldWriteAndStoreSession()
    {
        session_id(123);
        $session = new Db($this->handler);
        $session->createTable();
        $session->startSession();

        $minDate = date('Y-m-d H:i:s');

        $_SESSION['unit_test'] = 'foo';
        session_write_close();

        $sql = "SELECT * FROM `session` WHERE `id`=?";
        $actual = $this->handler->fetchAll($sql, array(123));

        $maxDate = date('Y-m-d H:i:s');

        $this->assertEquals(123, $actual[0]['id']);
        $this->assertTrue($minDate <= $actual[0]['created'], "$minDate <= {$actual[0]['created']}");
        $this->assertTrue($maxDate >= $actual[0]['created'], "$minDate >= {$actual[0]['created']}");
        $this->assertTrue($minDate <= $actual[0]['updated'], "$minDate <= {$actual[0]['updated']}");
        $this->assertTrue($maxDate >= $actual[0]['updated'], "$minDate >= {$actual[0]['updated']}");
        $this->assertEquals('unit_test|s:3:"foo";', $actual[0]['value']);
    }

    /**
     * @covers de\detert\sebastian\slimline\session\Db::open
     * @covers de\detert\sebastian\slimline\session\Db::read
     * @covers de\detert\sebastian\slimline\session\Db::destroy
     *
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     * @backupGlobals disabled
     * @backupStaticAttributes disabled
     */
    public function testShouldLoadAndDestroySession()
    {
        session_id(124);
        $session = new Db($this->handler);
        $session->createTable();

        $sql = "INSERT INTO `session`
            (`id`, `created`, `updated`, `value`) VALUES
            (:id, NOW(), NOW(), :value)";
        $this->handler->query($sql, array('id' => 124, 'value' => 'unit_test|s:3:"abc";'));

        $session->startSession();

        $this->assertEquals('abc', $_SESSION['unit_test']);

        session_destroy();

        $sql    = "SELECT * FROM `session` WHERE `id`=?";
        $actual = $this->handler->fetchAll($sql, array(124));

        $this->assertEquals(array(), $actual);
    }

    /**
     * @covers de\detert\sebastian\slimline\session\Db::gc
     *
     * @runInSeparateProcess
     */
    public function testShouldDestroySession()
    {
        session_id(125);
        $session = new Db($this->handler);
        $session->createTable();

        $now  = time() - 10;
        $date = date('Y-m-d H:i:s', $now);

        $sql = "INSERT INTO `session`
            (`id`, `created`, `updated`, `value`) VALUES
            (:id, :date, :date, :value)";
        $this->handler->query($sql, array('id' => 125, 'value' => 'unit_test|s:3:"abc";', 'date' => $date));

        $session->startSession();

        $session->gc(20);

        $sql    = "SELECT * FROM `session` WHERE `id`=?";
        $actual = $this->handler->fetchAll($sql, array(125));

        $this->assertEquals(125, $actual[0]['id']);
        $this->assertEquals('unit_test|s:3:"abc";', $actual[0]['value']);

        $session->gc(9);

        $sql    = "SELECT * FROM `session` WHERE `id`=?";
        $actual = $this->handler->fetchAll($sql, array(125));

        $this->assertEquals(array(), $actual);
    }
}
