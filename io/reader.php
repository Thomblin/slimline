<?php
/**
 * @author sebastian.detert <github@elygor.de>
 * @date 26.05.13
 * @time 13:06
 * @license property of Sebastian Detert
 */

namespace de\detert\sebastian\slimline\IO;


class Reader
{
    /**
     * @var string
     */
    private $folder;
    /**
     * @var string
     */
    private $pattern;

    /**
     * @param string $folder path to folder to be searched
     * @param string $pattern regex to select files
     */
    public function __construct($folder, $pattern)
    {
        $this->folder  = $folder;
        $this->pattern = $pattern;
    }

    public function getFolder()
    {
        return $this->folder;
    }

    /**
     * @return array
     */
    public function getFiles()
    {
        $dir      = new \RecursiveDirectoryIterator($this->folder);
        $ite      = new \RecursiveIteratorIterator($dir);
        $files    = new \RegexIterator($ite, $this->pattern, \RegexIterator::GET_MATCH);

        $fileList = array();
        foreach($files as $file) {
            $fileList = array_merge($fileList, $file);
        }

        return $fileList;
    }

    /**
     * @return array
     */
    public function getRelativeFiles()
    {
        $files = $this->getFiles();

        $offset = strlen($this->folder) + 1;
        array_walk($files, array($this, 'createRelativePath'), $offset);

        return $files;
    }

    /**
     * used to switch all absolute paths to relative path with help of array_walk
     *
     * @param string $string absolute filename
     * @param int    $key    key in array
     * @param int    $offset offset for substr
     */
    private function createRelativePath(&$string, $key, $offset = 0)
    {
        $string = substr($string, $offset);
    }
}