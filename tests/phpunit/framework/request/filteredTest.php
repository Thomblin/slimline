<?php
namespace de\detert\sebastian\slimline\Tests;

use de\detert\sebastian\slimline\Request_Filtered;

require_once BASE_DIR . 'request' . DS . 'filtered.php';

/**
 * @author sebastian.detert <github@elygor.de>
 * @date 13.01.13
 * @time 14:23
 * @license property of Sebastian Detert
 */
class RequestFilteredTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers de\detert\sebastian\slimline\Request_Filtered
     * @covers de\detert\sebastian\slimline\Request_Filtered::set
     */
    public function testThatArrayIsStored()
    {
        $values = array(
            'abcdefghijklmnopqrstuvwxyz1234567890_' => 'value',
            'Second Value' => '2. Value',
            'some wrong &%$7(' => 'blubb',
            '%&$/' => '17', # not to be saved
        );

        $filtered = new Request_Filtered($values);

        $actual = get_object_vars($filtered);

        $expected = array(
            'abcdefghijklmnopqrstuvwxyz1234567890_' => 'value',
            'second_value' => '2. Value',
            'some_wrong_7' => 'blubb',
        );
        $this->assertEquals($expected, $actual);
    }
}
