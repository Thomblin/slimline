<?php
namespace de\detert\sebastian\slimline\Tests;

use de\detert\sebastian\slimline\Config;

require_once BASE_DIR . 'config.php';

/**
 * @author sebastian.detert <github@elygor.de>
 * @date 13.01.13
 * @time 14:23
 * @license property of Sebastian Detert
 */
class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers de\detert\sebastian\slimline\Config
     */
    public function testThatConfigClassMembersAreGlobal()
    {
        $config = new Config();

        $actual = get_object_vars($config);

        $expected = array(
            'requestMap' => null,
            'baseDir' => null,
            'includes' => array(),
            'templatePath' => null,
            'exceptionHandler' => 'de\detert\sebastian\slimline\Exception\Handler',
            'setAssertHandler' => true,
            'setErrorHandler' => true,
            'renderError' => array(
                'template' => array(
                    'exception.php',
                ),
                'call' => array('de\detert\sebastian\slimline\Render_Plain', 'render'),
            ),
        );

        $this->assertEquals($expected, $actual);
    }
}
