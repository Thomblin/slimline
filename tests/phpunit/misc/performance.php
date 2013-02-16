<?php
namespace de\detert\sebastian\slimline\Tests;

class PerformanceVoid
{
}

class PerformanceDirect
{
    public $get;
}

class PerformanceGetterSetterSimple
{
    public function get($k)
    {
        return $this->$k;
    }

    public function set($k, $v)
    {
        $this->$k = $v;
    }
}

class PerformanceGetterSetter
{
    private $data;

    public function get($k)
    {
        return $this->data[$k];
    }

    public function set($k, $v)
    {
        $this->data[$k] = $v;
    }
}

class PerformanceMagicSimple
{
    public function __get($k)
    {
        return $this->$k;
    }

    public function __set($k, $v)
    {
        $this->$k = $v;
    }
}

class PerformanceMagic
{
    private $data;

    public function __get($k)
    {
        return $this->data[$k];
    }

    public function __set($k, $v)
    {
        $this->data[$k] = $v;
    }
}

/**
 * @author sebastian.detert <github@elygor.de>
 * @date 13.01.13
 * @time 14:23
 * @license property of Sebastian Detert
 */
class PerformanceTest extends \PHPUnit_Framework_TestCase
{
    public function testPerformanceOfMagicMethods()
    {
        $void = new PerformanceVoid();
        $direct = new PerformanceDirect();
        $getsetS = new PerformanceGetterSetterSimple();
        $getset = new PerformanceGetterSetter();
        $magicS = new PerformanceMagicSimple();
        $magic = new PerformanceMagic();

        echo "no class:       " . $this->getSetGetPerformance(null) . " per sec\n";
        echo "void:           " . $this->getSetGetPerformance($void) . " per sec\n";
        echo "direct:         " . $this->getSetGetPerformance($direct) . " per sec\n";
        echo "magic simple:   " . $this->getSetGetPerformance($magicS) . " per sec\n";
        echo "get/set simple: " . $this->getSetGetPerformance($getsetS) . " per sec\n";
        echo "get/set:        " . $this->getSetGetPerformance($getset) . " per sec\n";
        echo "magic:          " . $this->getSetGetPerformance($magic) . " per sec\n";
    }

    public function getSetGetPerformance($class, $iterations = 5000)
    {
        if (empty($class)) {
            $start = microtime(true);
            for ($i = 0; $i < $iterations; ++$i) {
                $myvar = $i;
                $i = $myvar;
            }
            $end = microtime(true);
        } else if ($class instanceof PerformanceGetterSetter || $class instanceof PerformanceGetterSetterSimple) {
            $start = microtime(true);
            for ($i = 0; $i < $iterations; ++$i) {
                $class->set('get', $i);
                $i = $class->get('get');
            }
            $end = microtime(true);
        } else {
            $start = microtime(true);
            for ($i = 0; $i < $iterations; ++$i) {
                $class->get = $i;
                $i = $class->get;
            }
            $end = microtime(true);
        }


        return number_format($iterations / ($end - $start), 0, ',', '.');
    }
}
