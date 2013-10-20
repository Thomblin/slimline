<?php
namespace de\detert\sebastian\slimline\Tests\Helper;
use de\detert\sebastian\slimline\Controllable;

/**
 * @author sebastian.detert <github@elygor.de>
 * @date 02.02.13
 * @time 11:15
 * @license property of Sebastian Detert
 */
class Dummy extends Controllable
{
    public $one;
    public $two;
    public $three;

    /**
     * @param mixed $one
     * @param mixed $two
     * @param mixed $three
     */
    public function set123($one = null, $two = null, $three = null)
    {
        $this->one = $one;
        $this->two = $two;
        $this->three = $three;
    }

    public function doNothing()
    {
        return $this->getPool()->factory->create('de\detert\sebastian\slimline\Response');
    }

    /**
     */
    public function run()
    {
        return $this->getPool()->factory->create('de\detert\sebastian\slimline\Response');
    }
}
