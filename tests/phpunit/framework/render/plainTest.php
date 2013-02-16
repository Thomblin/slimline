<?php
namespace de\detert\sebastian\slimline\Tests;

use de\detert\sebastian\slimline\Render_Plain;

require_once BASE_DIR . 'render/plain.php';

/**
 * @author sebastian.detert <github@elygor.de>
 * @date 13.01.13
 * @time 14:23
 * @license property of Sebastian Detert
 */
class Render_PlainTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException de\detert\sebastian\slimline\Tests\Helper\Exception
     * @expectedExceptionMessage plain template was called
     *
     * @covers de\detert\sebastian\slimline\Render_Plain::getTemplateFolder
     */
    public function testThatHtmlFolderIsReturned()
    {
        $render = new Render_Plain(BASE_DIR . 'tests' . DS . 'helper' . DS . 'templates' . DS);
        $render->render('throw_exception.php', null);
    }
}
