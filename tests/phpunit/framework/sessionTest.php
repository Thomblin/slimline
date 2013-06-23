<?php
namespace de\detert\sebastian\slimline\Tests;

use de\detert\sebastian\slimline\Session;
use de\detert\sebastian\slimline\Request;
use de\detert\sebastian\slimline\Request_Filter;

/**
 * @author sebastian.detert <github@elygor.de>
 * @date 13.01.13
 * @time 14:23
 * @license property of Sebastian Detert
 */
class SessionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Request
     */
    private $request;
    /**
     * used to restore superglobals
     *
     * @var array
     */
    private $superglobals;

    /**
     * @covers de\detert\sebastian\slimline\Session::validateSuperglobals
     * @covers de\detert\sebastian\slimline\Session::unsetSuperglobals
     * @covers de\detert\sebastian\slimline\Session::set
     * @covers de\detert\sebastian\slimline\Session::__destruct
     */
    public function testShouldSetAndGetSession()
    {
        session_start();
        $_SESSION['foo'] = 'test';

        $session = new Session();

        $this->assertEquals(array(), $_SESSION);

        $filter = new Request_Filter();
        $filter->foo = array(
            'scope' => array('SESSION'),
            'filter' => FILTER_SANITIZE_STRING,
            'options' => array(),
        );

        $actual = $session->getFilteredData($filter);

        $this->assertInstanceOf('de\detert\sebastian\slimline\Request_Filtered', $actual);
        $this->assertEquals('test', $actual->foo);

        $session->set('test', '123');

        unset($session);

        $this->assertEquals(array('foo' => 'test', 'test' => '123'), $_SESSION);

        session_destroy();
    }
}
