<?php
namespace de\detert\sebastian\slimline\Tests;

use de\detert\sebastian\slimline\Request;
use de\detert\sebastian\slimline\Request_Filter;
use de\detert\sebastian\slimline\Request_Filtered;

require_once BASE_DIR . 'request.php';
require_once BASE_DIR . 'request' . DS . 'filter.php';
require_once BASE_DIR . 'request' . DS . 'filtered.php';

/**
 * @author sebastian.detert <github@elygor.de>
 * @date 13.01.13
 * @time 14:23
 * @license property of Sebastian Detert
 */
class RequestTest extends \PHPUnit_Framework_TestCase
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
     *
     */
    public function setUp()
    {
        $this->superglobals = array(
            'get' => $_GET,
            'post' => $_POST,
            'server' => $_SERVER,
            'cookie' => $_COOKIE,
            'request' => $_REQUEST,
        );

        $_GET = array('any_value' => 1, 'second' => '1b');
        $_POST = array('postOne' => array('k' => 'v'));
        $_SERVER = array('num' => 3);
        $_COOKIE = array('any_value' => 2);
        $_REQUEST = array('R-E-Q-U-E-S-T' => 5);

        $this->request = new Request();
    }

    /**
     *
     */
    public function tearDown()
    {
        $_GET = $this->superglobals['get'];
        $_POST = $this->superglobals['post'];
        $_SERVER = $this->superglobals['server'];
        $_COOKIE = $this->superglobals['cookie'];
        $_REQUEST = $this->superglobals['request'];
    }

    /**
     * @covers de\detert\sebastian\slimline\Request::unsetSuperglobals
     */
    public function testThatSuperglobalsAreDeleted()
    {
        $this->assertEquals(array(), $_GET);
        $this->assertEquals(array(), $_POST);
        $this->assertEquals(array(), $_SERVER);
        $this->assertEquals(array(), $_COOKIE);
        $this->assertEquals(array(), $_REQUEST);
    }

    /**
     * @covers de\detert\sebastian\slimline\Request::validateSuperglobals
     * @covers de\detert\sebastian\slimline\Request::normalize
     * @covers de\detert\sebastian\slimline\Request::getFilteredData
     */
    public function testShouldReturnEmptyRequestFiltered()
    {
        $actual = $this->request->getFilteredData(new Request_Filter());

        $this->assertInstanceOf('de\detert\sebastian\slimline\Request_Filtered', $actual);
        $this->assertEquals(array(), get_object_vars($actual));
    }

    /**
     * @covers de\detert\sebastian\slimline\Request::validateSuperglobals
     * @covers de\detert\sebastian\slimline\Request::normalize
     * @covers de\detert\sebastian\slimline\Request::getFilteredData
     */
    public function testShouldReturnRequestFiltered()
    {
        $filter = new Request_Filter();
        $filter->num = array(
            'scope' => array('SERVER'),
            'filter' => FILTER_SANITIZE_NUMBER_INT,
            'options' => array('default' => 'sebastian'),
        );

        $actual = $this->request->getFilteredData($filter);

        $this->assertInstanceOf('de\detert\sebastian\slimline\Request_Filtered', $actual);
        $this->assertEquals($actual->num, 3);
    }

    /**
     * @covers de\detert\sebastian\slimline\Request::validateSuperglobals
     * @covers de\detert\sebastian\slimline\Request::normalize
     * @covers de\detert\sebastian\slimline\Request::getFilteredData
     */
    public function testShouldOnlyReturnMatchingValues()
    {
        $filter = new Request_Filter();
        $filter->any_value = array(
            'scope' => array('GET'),
            'filter' => FILTER_SANITIZE_STRING,
            'options' => array('default' => 'sebastian'),
        );
        $filter->second = array(
            'scope' => array('GET'),
            'filter' => FILTER_SANITIZE_STRING,
            'options' => array('default' => 'sebastian'),
        );

        $actual = $this->request->getFilteredData($filter);

        $this->assertInstanceOf('de\detert\sebastian\slimline\Request_Filtered', $actual);
        $this->assertEquals($actual->second, '1b');
    }

    /**
     * @covers de\detert\sebastian\slimline\Request::validateSuperglobals
     * @covers de\detert\sebastian\slimline\Request::normalize
     * @covers de\detert\sebastian\slimline\Request::getFilteredData
     */
    public function testShouldReturnFirstValueInScope()
    {
        $filter = new Request_Filter();
        $filter->any_value = array(
            'scope' => array('GET', 'COOKIE'),
            'filter' => FILTER_SANITIZE_STRING,
            'options' => array('default' => 'sebastian'),
        );

        $actual = $this->request->getFilteredData($filter);

        $this->assertInstanceOf('de\detert\sebastian\slimline\Request_Filtered', $actual);
        $this->assertEquals($actual->any_value, 1);

        $filter = new Request_Filter();
        $filter->any_value = array(
            'scope' => array('COOKIE', 'GET'),
            'filter' => FILTER_SANITIZE_STRING,
            'options' => array('default' => 'sebastian'),
        );

        $actual = $this->request->getFilteredData($filter);

        $this->assertInstanceOf('de\detert\sebastian\slimline\Request_Filtered', $actual);
        $this->assertEquals($actual->any_value, 2);
    }
}
