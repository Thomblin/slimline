<?php
namespace de\detert\sebastian\slimline\Tests\Helper;

/**
 * @author sebastian.detert <github@elygor.de>
 * @date 02.02.13
 * @time 11:15
 * @license property of Sebastian Detert
 */
class Dummy
{
    public $one;
    public $two;
    public $three;

    /**
     * @param mixed $one
     * @param mixed $two
     * @param mixed $three
     */
    public function __construct($one = null, $two = null, $three = null)
    {
        $this->one = $one;
        $this->two = $two;
        $this->three = $three;
    }

    public function doNothing(\de\detert\sebastian\slimline\Pool $pool)
    {
        return $pool->factory->create('de\detert\sebastian\slimline\Response');
    }

    /**
     * @param Request $request
     * @param Response $response
     */
    public function run(\de\detert\sebastian\slimline\Pool $pool)
    {
        return $pool->factory->create('de\detert\sebastian\slimline\Response');
    }
}
