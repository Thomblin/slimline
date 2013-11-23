<?php
namespace de\detert\sebastian\slimline\Tests;

use de\detert\sebastian\slimline\IO\Reader;

require_once BASE_DIR . 'io' . DS . 'reader.php';

/**
 * @author sebastian.detert <github@elygor.de>
 * @date 13.01.13
 * @time 14:23
 * @license property of Sebastian Detert
 */
class IoReaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Reader
     */
    private $reader;

    /**
     * @covers de\detert\sebastian\slimline\Io\Reader::__construct
     * @covers de\detert\sebastian\slimline\Io\Reader::getFolder
     * @covers de\detert\sebastian\slimline\Io\Reader::getRelativeFiles
     * @covers de\detert\sebastian\slimline\Io\Reader::createRelativePath
     */
    public function testShouldFindRelativeFiles()
    {
        $reader = new Reader(__DIR__ . DS . 'misc', '/.*\.sql/');

        $this->assertSame(__DIR__ . DS . 'misc', $reader->getFolder());

        $files = $reader->getRelativeFiles();

        $expected = array(
            'first.sql',
            'second.sql',
            'third' . DS . 'fourth.sql'
        );

        $this->assertEquals($expected, $files);
    }
    /**
     * @covers de\detert\sebastian\slimline\Io\Reader::__construct
     * @covers de\detert\sebastian\slimline\Io\Reader::getFolder
     * @covers de\detert\sebastian\slimline\Io\Reader::getFiles
     */
    public function testShouldFindFiles()
    {
        $reader = new Reader(__DIR__ . DS . 'misc', '/.*\.sql/');

        $this->assertSame(__DIR__ . DS . 'misc', $reader->getFolder());

        $files = $reader->getFiles();

        $expected = array(
            __DIR__ . DS . 'misc' . DS . 'first.sql',
            __DIR__ . DS . 'misc' . DS . 'second.sql',
            __DIR__ . DS . 'misc' . DS . 'third' . DS . 'fourth.sql'
        );

        $this->assertEquals($expected, $files);
    }
}
