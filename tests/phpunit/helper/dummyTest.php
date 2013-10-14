<?php
namespace de\detert\sebastian\slimline\Tests;

use de\detert\sebastian\slimline\Tests\Helper\Dummy;
use de\detert\sebastian\slimline\Pool;

require_once BASE_DIR . '/tests/helper/dummy.php';

/**
 * @author sebastian.detert <github@elygor.de>
 * @date 13.01.13
 * @time 14:23
 * @license property of Sebastian Detert
 */
class DummyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers de\detert\sebastian\slimline\Tests\Helper\Dummy
     */
    public function testShouldCreateDummyClass()
    {
        $dummy = new Dummy(new Pool());
        $dummy->set123(1, 2, 3);

        $this->assertEquals(1, $dummy->one);
        $this->assertEquals(2, $dummy->two);
        $this->assertEquals(3, $dummy->three);
    }
}
