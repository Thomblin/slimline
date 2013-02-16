<?php
namespace de\detert\sebastian\slimline\Tests;

use de\detert\sebastian\slimline\Tests\Helper\Factory;
use de\detert\sebastian\slimline\Tests\Helper\Dummy;

require_once BASE_DIR . 'factory.php';
require_once BASE_DIR . 'tests' . DS . 'helper' . DS . 'factory.php';
require_once BASE_DIR . 'tests' . DS . 'helper' . DS . 'dummy.php';

/**
 * @author sebastian.detert <github@elygor.de>
 * @date 13.01.13
 * @time 14:23
 * @license property of Sebastian Detert
 */
class FactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers de\detert\sebastian\slimline\Tests\Helper\Factory::create
     */
    public function testShouldCreateDummyClass()
    {
        $factory = new Factory();
        $dummy   = $factory->create('\de\detert\sebastian\slimline\Tests\Helper\Dummy');

        $this->assertInstanceOf('\de\detert\sebastian\slimline\Tests\Helper\Dummy', $dummy);
    }

    /**
     * @covers de\detert\sebastian\slimline\Tests\Helper\Factory::create
     * @covers de\detert\sebastian\slimline\Tests\Helper\Factory::setMock
     */
    public function testShouldReturnMock()
    {
        $factory = new Factory();
        $mock   = $this->getMock('\de\detert\sebastian\slimline\Tests\Helper\Dummy');
        $factory->setMock('\de\detert\sebastian\slimline\Tests\Helper\Dummy', $mock);

        $dummy   = $factory->create('\de\detert\sebastian\slimline\Tests\Helper\Dummy');

        $this->assertTrue($mock === $dummy);
    }

    /**
     * @covers de\detert\sebastian\slimline\Tests\Helper\Factory::create
     * @covers de\detert\sebastian\slimline\Tests\Helper\Factory::setMock
     */
    public function testShouldReturnMockAtSecondCall()
    {
        $factory = new Factory();
        $mock   = $this->getMock('\de\detert\sebastian\slimline\Tests\Helper\Dummy');
        $factory->setMock('\de\detert\sebastian\slimline\Tests\Helper\Dummy', $mock, 2);

        $firstCall  = $factory->create('\de\detert\sebastian\slimline\Tests\Helper\Dummy');
        $secondCall = $factory->create('\de\detert\sebastian\slimline\Tests\Helper\Dummy');
        $thirdCall  = $factory->create('\de\detert\sebastian\slimline\Tests\Helper\Dummy');

        $this->assertTrue($mock !== $firstCall);
        $this->assertTrue($mock === $secondCall);
        $this->assertTrue($mock !== $thirdCall);
    }

    /**
     * @covers de\detert\sebastian\slimline\Tests\Helper\Factory::create
     * @covers de\detert\sebastian\slimline\Tests\Helper\Factory::setMock
     */
    public function testShouldReturnMockAtFirstAndSecondCall()
    {
        $factory = new Factory();
        $mock   = $this->getMock('\de\detert\sebastian\slimline\Tests\Helper\Dummy');
        $factory->setMock('\de\detert\sebastian\slimline\Tests\Helper\Dummy', $mock, 1);
        $factory->setMock('\de\detert\sebastian\slimline\Tests\Helper\Dummy', $mock, 2);

        $firstCall  = $factory->create('\de\detert\sebastian\slimline\Tests\Helper\Dummy');
        $secondCall = $factory->create('\de\detert\sebastian\slimline\Tests\Helper\Dummy');
        $thirdCall  = $factory->create('\de\detert\sebastian\slimline\Tests\Helper\Dummy');

        $this->assertTrue($mock === $firstCall);
        $this->assertTrue($mock === $secondCall);
        $this->assertTrue($mock !== $thirdCall);
    }

    /**
     * @covers de\detert\sebastian\slimline\Tests\Helper\Factory::create
     * @covers de\detert\sebastian\slimline\Tests\Helper\Factory::setMock
     */
    public function testShouldReturnDifferentMocks()
    {
        $factory = new Factory();
        $mock   = $this->getMock('\de\detert\sebastian\slimline\Tests\Helper\Dummy');
        $mock2  = $this->getMock('\de\detert\sebastian\slimline\Tests\Helper\Dummy');
        $mock3  = $this->getMock('\de\detert\sebastian\slimline\Tests\Helper\Dummy');

        $factory->setMock('\de\detert\sebastian\slimline\Tests\Helper\Dummy', $mock3);
        $factory->setMock('\de\detert\sebastian\slimline\Tests\Helper\Dummy', $mock, 1);
        $factory->setMock('\de\detert\sebastian\slimline\Tests\Helper\Dummy', $mock2, 2);

        $firstCall  = $factory->create('\de\detert\sebastian\slimline\Tests\Helper\Dummy');
        $secondCall = $factory->create('\de\detert\sebastian\slimline\Tests\Helper\Dummy');
        $thirdCall  = $factory->create('\de\detert\sebastian\slimline\Tests\Helper\Dummy');

        $this->assertTrue($mock === $firstCall);
        $this->assertTrue($mock2 !== $firstCall);
        $this->assertTrue($mock3 !== $firstCall);

        $this->assertTrue($mock !== $secondCall);
        $this->assertTrue($mock2 === $secondCall);
        $this->assertTrue($mock3 !== $secondCall);

        $this->assertTrue($mock !== $thirdCall);
        $this->assertTrue($mock2 !== $thirdCall);
        $this->assertTrue($mock3 === $thirdCall);
    }

    /**
     * @covers de\detert\sebastian\slimline\Tests\Helper\Factory::create
     * @covers de\detert\sebastian\slimline\Tests\Helper\Factory::getNumOfCreateCalls
     */
    public function testShouldReturnCountOfCreateCalls()
    {
        $factory = new Factory();
        $expected = 0;
        $actual = $factory->getNumOfCreateCalls('\de\detert\sebastian\slimline\Tests\Helper\Dummy');
        $this->assertEquals($expected, $actual);

        $factory->create('\de\detert\sebastian\slimline\Tests\Helper\Dummy');

        $actual = $factory->getNumOfCreateCalls('\de\detert\sebastian\slimline\Tests\Helper\Dummy');
        $this->assertEquals(++$expected, $actual);

        $factory->create('\de\detert\sebastian\slimline\Tests\Helper\Dummy');

        $actual = $factory->getNumOfCreateCalls('\de\detert\sebastian\slimline\Tests\Helper\Dummy');
        $this->assertEquals(++$expected, $actual);
    }

    /**
     * @covers de\detert\sebastian\slimline\Tests\Helper\Factory::create
     * @covers de\detert\sebastian\slimline\Tests\Helper\Factory::getNumOfCreateCalls
     */
    public function testThatFactoryHandlesMultipleMocks()
    {
        $factory = new Factory();

        $this->assertEquals(0, $factory->getNumOfCreateCalls('dummy'));

        $dummy = new Dummy();
        $factory->setMock('dummy', $dummy);

        $this->assertEquals(0, $factory->getNumOfCreateCalls('dummy'));

        $mock   = $this->getMock('\de\detert\sebastian\slimline\Tests\Helper\Dummy');
        $factory->setMock('mock', $mock);

        $this->assertEquals(0, $factory->getNumOfCreateCalls('mock'));

        $actual = $factory->create('dummy');
        $this->assertTrue($dummy === $actual);
        $this->assertEquals(1, $factory->getNumOfCreateCalls('dummy'));
        $this->assertEquals(0, $factory->getNumOfCreateCalls('mock'));

        $actual = $factory->create('mock');
        $this->assertTrue($mock === $actual);
        $this->assertEquals(1, $factory->getNumOfCreateCalls('dummy'));
        $this->assertEquals(1, $factory->getNumOfCreateCalls('mock'));


        $this->assertEquals(0, $factory->getNumOfCreateCalls(get_class($this)));
        $factory->create(get_class($this));
        $this->assertEquals(1, $factory->getNumOfCreateCalls(get_class($this)));
        $this->assertEquals(1, $factory->getNumOfCreateCalls('dummy'));
        $this->assertEquals(1, $factory->getNumOfCreateCalls('mock'));
    }

}
