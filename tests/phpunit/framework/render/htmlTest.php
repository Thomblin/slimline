<?php
namespace de\detert\sebastian\slimline\Tests;

use de\detert\sebastian\slimline\Render_Html;

require_once BASE_DIR . 'render/html.php';

/**
 * @author sebastian.detert <github@elygor.de>
 * @date 13.01.13
 * @time 14:23
 * @license property of Sebastian Detert
 */
class Render_HtmlTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException de\detert\sebastian\slimline\Tests\Helper\Exception
     * @expectedExceptionMessage html template was called
     *
     * @covers de\detert\sebastian\slimline\Render_HTML::getTemplateFolder
     */
    public function testThatHtmlFolderIsReturned()
    {
        $render = new Render_Html(BASE_DIR . 'tests' . DS . 'helper' . DS . 'templates' . DS);
        $render->render('throw_exception.php', null);
    }
}
