<?php
namespace de\detert\sebastian\slimline\Tests;

use de\detert\sebastian\slimline\Translate;

/**
 * @author sebastian.detert <github@elygor.de>
 * @date 13.01.13
 * @time 14:23
 * @license property of Sebastian Detert
 */
class TranslateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers de\detert\sebastian\slimline\Translate::getTranslation
     * @covers de\detert\sebastian\slimline\Translate::loadCategory
     */
    public function testShouldTranslateSimpleText()
    {
        $translate = new Translate(__DIR__ . DS . 'translate', 'en');

        $actual = $translate->getTranslation('test', 'foo');
        $expected = 'it works';

        $this->assertEquals($expected, $actual);
    }
    /**
     * @covers de\detert\sebastian\slimline\Translate::getTranslation
     * @covers de\detert\sebastian\slimline\Translate::loadCategory
     */
    public function testShouldTranslateTextWithParams()
    {
        $translate = new Translate(__DIR__ . DS . 'translate', 'en');

        $actual = $translate->getTranslation('test', 'foo_var', array('i' => 17));
        $expected = 'i = 17!';

        $this->assertEquals($expected, $actual);
    }
}
