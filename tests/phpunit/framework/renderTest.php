<?php
namespace de\detert\sebastian\slimline\Tests;

use de\detert\sebastian\slimline\Render;
use de\detert\sebastian\slimline\Tests\Helper\Exception;

require_once BASE_DIR . 'render.php';

/**
 * @author sebastian.detert <github@elygor.de>
 * @date 13.01.13
 * @time 14:23
 * @license property of Sebastian Detert
 */
class RenderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Render
     */
    private $render;

    /**
     *
     */
    public function setUp()
    {
        $this->render = $this->getMockForAbstractClass(
            'de\detert\sebastian\slimline\Render',
            array(BASE_DIR . 'tests' . DS),
            '',
            true,
            true,
            true,
            array('getTemplateFolder')
        );

        $this->render->expects($this->once())
            ->method('getTemplateFolder')
            ->will($this->returnValue('helper'));
    }

    /**
     * @expectedException de\detert\sebastian\slimline\Tests\Helper\Exception
     * @expectedExceptionMessage content is empty
     *
     * @covers de\detert\sebastian\slimline\Render::render
     */
    public function testThatTemplateIsIncluded()
    {
        $this->render->render('throw_exception.php', null);
    }

    /**
     * @expectedException de\detert\sebastian\slimline\Tests\Helper\Exception
     * @expectedExceptionMessage this is my content
     *
     * @covers de\detert\sebastian\slimline\Render::render
     */
    public function testThatContentIsPassedToTemplate()
    {
        $this->render->render('throw_exception.php', 'this is my content');
    }

    /**
     * @expectedException de\detert\sebastian\slimline\Tests\Helper\Exception
     * @expectedExceptionMessage stdClass Message
     *
     * @covers de\detert\sebastian\slimline\Render::render
     */
    public function testThatObjectsAreExtracted()
    {
        $stdClass = new \stdClass();
        $stdClass->stdClassMessage = 'stdClass Message';

        $this->render->render('throw_exception.php', $stdClass);
    }

    /**
     * @expectedException de\detert\sebastian\slimline\Tests\Helper\Exception
     * @expectedExceptionMessage array message
     *
     * @covers de\detert\sebastian\slimline\Render::render
     */
    public function testThatArraysAreExtracted()
    {
        $this->render->render('throw_exception.php', array('arrayMessage' => 'array message'));
    }
}
