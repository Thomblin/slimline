<?php
namespace de\detert\sebastian\slimline\Tests;

use de\detert\sebastian\slimline\IO\Reader;

require_once BASE_DIR . 'io\reader.php';

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
     *
     */
    public function testShouldFindFiles()
    {
        $reader = new Reader(__DIR__ . DS . 'misc', '/.*\.sql/');

        $this->assertSame(__DIR__ . DS . 'misc', $reader->getFolder());

        $files = $reader->getFiles();

        $expected = array(
            'first.sql',
            'second.sql',
            'third' . DS . 'fourth.sql'
        );

        $this->assertSame($expected, $files);
    }
}
