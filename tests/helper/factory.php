<?php
namespace de\detert\sebastian\slimline\Tests\Helper;

use de\detert\sebastian\slimline\Factory as FrameworkFactory;

/**
 * @author sebastian.detert <github@elygor.de>
 * @date 03.02.13
 * @time 16:04
 * @license property of Sebastian Detert
 */
class Factory extends FrameworkFactory
{
    /**
     * @var array
     */
    private $mocks;

    /**
     * @var array
     */
    private $numOfCreateCalls;

    /**
     * @param string $className
     * @param mixed  $firstParameter
     * @param mixed  $_
     *
     * @return object
     */
    public function create($className, $firstParameter = null, $_ = null)
    {
        $this->numOfCreateCalls[$className] = isset($this->numOfCreateCalls[$className])
            ? $this->numOfCreateCalls[$className] + 1
            : 1;

        if ( isset($this->mocks[$className][$this->numOfCreateCalls[$className]]) ) {
            return $this->mocks[$className][$this->numOfCreateCalls[$className]];
        } else if ( isset($this->mocks[$className][-1]) ) {
            return $this->mocks[$className][-1];
        }else {
            return call_user_func_array(array('de\detert\sebastian\slimline\Factory', 'create'), func_get_args());
        }
    }

    /**
     * @param string $className
     * @param object $class
     * @param int    $index
     */
    public function setMock($className, $class, $index = -1)
    {
        $this->mocks[$className][$index] = $class;
    }

    /**
     * @param string $className
     * @return int
     */
    public function getNumOfCreateCalls($className)
    {
        return isset($this->numOfCreateCalls[$className])
            ? $this->numOfCreateCalls[$className]
            : 0;
    }
}
