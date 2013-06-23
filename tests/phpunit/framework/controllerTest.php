<?php
namespace de\detert\sebastian\slimline\Tests;

use de\detert\sebastian\slimline\Controller;
use de\detert\sebastian\slimline\Config;
use de\detert\sebastian\slimline\Factory;
use de\detert\sebastian\slimline\Tests\Helper\Factory as FactoryMock;

require_once BASE_DIR . 'controller.php';
require_once BASE_DIR . 'config.php';
require_once BASE_DIR . 'factory.php';

/**
 * @author sebastian.detert <github@elygor.de>
 * @date 02.02.13
 * @time 18:14
 * @license property of Sebastian Detert
 */
class ControllerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers de\detert\sebastian\slimline\Controller::__construct
     * @covers de\detert\sebastian\slimline\Controller::simpleAutoload
     */
    public function testThatAutoloaderIsWorking()
    {
        $this->assertFalse(class_exists('de\detert\sebastian\slimline\Tests\Helper\Check_Autoloader', false));

        $config = new Config();
        $factory = new Factory();

        new Controller($config, $factory);
        new \de\detert\sebastian\slimline\Tests\Helper\Check_Autoloader();

        $this->assertTrue(class_exists('de\detert\sebastian\slimline\Tests\Helper\Check_Autoloader', false));
    }

    /**
     * @covers de\detert\sebastian\slimline\Controller::setHandlers
     * @covers de\detert\sebastian\slimline\Controller::run
     */
    public function testThatControllerSetsAllHandlers()
    {
        $exceptionHandler = $this->getMock(
            '\de\detert\sebastian\slimline\Exception\Handler',
            array('setAssertHandling', 'setErrorHandling')
        );
        $exceptionHandler->expects($this->once())
            ->method('setAssertHandling');
        $exceptionHandler->expects($this->once())
            ->method('setErrorHandling');

        $factory = new FactoryMock(new Factory());
        $factory->setMock('de\detert\sebastian\slimline\Exception\Handler', $exceptionHandler);

        $config = new Config();

        $controller = $this->getNewController($config, $factory);
        $controller->run();
    }

    /**
     * @param \de\detert\sebastian\slimline\Config $config
     * @param \de\detert\sebastian\slimline\Factory $factory
     *
     * @return \de\detert\sebastian\slimline\Controller
     */
    private function getNewController(Config $config, Factory $factory)
    {
        if ( empty($config->templatePath) ) {
            $config->templatePath = BASE_DIR . 'tests' . DS . 'helper' . DS . 'templates' . DS;
        }
        if ( empty($config->requestMap) ) {
            $config->requestMap = array(
                '/' => array(
                    'callbacks' => array(
                    ),
                    'render' => array(
                        'template' => array(
                            'empty.php',
                        ),
                        'call' => array('de\detert\sebastian\slimline\Render_Plain', 'render'),
                    ),
                ));
        }

        return new Controller($config, $factory);
    }

    /**
     * @covers de\detert\sebastian\slimline\Controller::setRequest
     * @covers de\detert\sebastian\slimline\Controller::run
     */
    public function testThatRequestIsCreated()
    {
        $factory = new FactoryMock(new Factory());

        $config = new Config();

        $controller = $this->getNewController($config, $factory);
        $controller->run();

        $this->assertEquals(1, $factory->getNumOfCreateCalls('de\detert\sebastian\slimline\Request'));
    }

    /**
     * @covers de\detert\sebastian\slimline\Controller::setResponse
     * @covers de\detert\sebastian\slimline\Controller::getRewriteRules
     * @covers de\detert\sebastian\slimline\Controller::run
     */
    public function testThatResponceIsCreated()
    {
        $factory = new FactoryMock(new Factory());

        $config = new Config();

        $controller = $this->getNewController($config, $factory);
        $controller->run();

        $this->assertEquals(1, $factory->getNumOfCreateCalls('de\detert\sebastian\slimline\Response'));
    }

    /**
     * @covers de\detert\sebastian\slimline\Controller::setResponse
     * @covers de\detert\sebastian\slimline\Controller::getRewriteRules
     * @covers de\detert\sebastian\slimline\Controller::run
     */
    public function testThatResponseIsCreatedForEachCallback()
    {
        $response = new \de\detert\sebastian\slimline\Response();
        $responseDummy = new \de\detert\sebastian\slimline\Response();
        $responseDummy2 = new \de\detert\sebastian\slimline\Response();

        $factory = new FactoryMock(new Factory());
        $factory->setMock('de\detert\sebastian\slimline\Response', $response, 1);
        $factory->setMock('de\detert\sebastian\slimline\Response', $responseDummy, 2);
        $factory->setMock('de\detert\sebastian\slimline\Response', $responseDummy2, 3);

        $config = new Config();
        $config->requestMap = array(
            '/' => array(
                'callbacks' => array(
                    'dummy' => array('de\detert\sebastian\slimline\Tests\Helper\Dummy', 'doNothing'),
                    'dummy2' => array('de\detert\sebastian\slimline\Tests\Helper\Dummy', 'doNothing'),
                ),
                'render' => array(
                    'template' => array(
                        'empty.php',
                    ),
                    'call' => array('de\detert\sebastian\slimline\Render_Plain', 'render'),
                ),
            ));

        $controller = $this->getNewController($config, $factory);
        $controller->run();

        $this->assertEquals(3, $factory->getNumOfCreateCalls('de\detert\sebastian\slimline\Response'));

        $this->assertTrue(isset($response->dummy));
        $this->assertTrue(isset($response->dummy2));
        $this->assertTrue($response->dummy === $responseDummy);
        $this->assertTrue($response->dummy2 === $responseDummy2);
    }

    /**
     * @covers de\detert\sebastian\slimline\Controller::setResponse
     * @covers de\detert\sebastian\slimline\Controller::getRewriteRules
     * @covers de\detert\sebastian\slimline\Controller::run
     */
    public function testThatControllerActionIsCalled()
    {
        $pool = new \de\detert\sebastian\slimline\Pool();
        $request = new \de\detert\sebastian\slimline\Request();
        $response = new \de\detert\sebastian\slimline\Response();
        $response2 = new \de\detert\sebastian\slimline\Response();
        $response3 = new \de\detert\sebastian\slimline\Response();

        $factory = new FactoryMock(new Factory());
        $factory->setMock('de\detert\sebastian\slimline\Pool', $pool);
        $factory->setMock('de\detert\sebastian\slimline\Request', $request);
        $factory->setMock('de\detert\sebastian\slimline\Response', $response, 1);
        $factory->setMock('de\detert\sebastian\slimline\Response', $response2, 2);
        $factory->setMock('de\detert\sebastian\slimline\Response', $response3, 3);

        $dummy = $this->getMock('\de\detert\sebastian\slimline\Tests\Helper\Dummy', array('run'));
        $dummy->expects($this->at(0))
            ->method('run')
            ->with($this->equalTo($pool))
            ->will($this->returnValue($response2));
        $dummy->expects($this->at(1))
            ->method('run')
            ->with($this->equalTo($pool))
            ->will($this->returnValue($response3));

        $factory->setMock('de\detert\sebastian\slimline\Tests\Helper\Dummy', $dummy, 1);
        $factory->setMock('de\detert\sebastian\slimline\Tests\Helper\Dummy', $dummy, 2);

        $config = new Config();
        $config->requestMap = array(
            '/' => array(
                'callbacks' => array(
                    'dummy' => array('de\detert\sebastian\slimline\Tests\Helper\Dummy', 'run'),
                    'dummy2' => array('de\detert\sebastian\slimline\Tests\Helper\Dummy', 'run'),
                ),
                'render' => array(
                    'template' => array(
                        'empty.php',
                    ),
                    'call' => array('de\detert\sebastian\slimline\Render_Plain', 'render'),
                ),
            ));

        $controller = $this->getNewController($config, $factory);
        $controller->run();
    }
}
