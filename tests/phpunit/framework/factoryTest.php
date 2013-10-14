<?php
namespace de\detert\sebastian\slimline\Tests;

use de\detert\sebastian\slimline\Factory;
use de\detert\sebastian\slimline\Pool;

require_once BASE_DIR . 'factory.php';
require_once BASE_DIR . 'tests' . DS . 'helper' . DS . 'dummy.php';

/**
 * @author sebastian.detert <github@elygor.de>
 * @date 13.01.13
 * @time 14:23
 * @license property of Sebastian Detert
 */
class FrameworkTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers de\detert\sebastian\slimline\Factory::create
     */
    public function testShouldCreateNewClass()
    {
        $factory = new Factory();
        /** @var $dummy \de\detert\sebastian\slimline\Tests\Helper\Dummy */
        $dummy = $factory->create('\de\detert\sebastian\slimline\Tests\Helper\Dummy', new Pool());
        $dummy->set123(1, 2, 3);

        $this->assertInstanceOf('\de\detert\sebastian\slimline\Tests\Helper\Dummy', $dummy);
        $this->assertEquals(1, $dummy->one);
        $this->assertEquals(2, $dummy->two);
        $this->assertEquals(3, $dummy->three);
    }

    /**
     * @covers de\detert\sebastian\slimline\Factory::create
     */
    public function testShouldCreateNewClassWithEachCall()
    {
        $factory = new Factory();
        /** @var $dummy \de\detert\sebastian\slimline\Tests\Helper\Dummy */
        $dummy = $factory->create('\de\detert\sebastian\slimline\Tests\Helper\Dummy', new Pool());
        $dummy2 = $factory->create('\de\detert\sebastian\slimline\Tests\Helper\Dummy', new Pool());

        $this->assertFalse($dummy === $dummy2, "factory should create new instances with each call");
    }
}
