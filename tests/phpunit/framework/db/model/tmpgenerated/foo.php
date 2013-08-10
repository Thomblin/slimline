<?php
namespace de\detert\sebastian\slimline\db\modelgenerated;

use de\detert\sebastian\slimline\db\Model;

/**
 * @author sebastian.detert <github@elygor.de>
 * @date 28.05.13
 * @time 20:12
 * @license property of Sebastian Detert
 */
class Foo extends Model
{
    /**
     * @var array
     */
    protected $columns = array(
        'id', // int(20) not null auto_increment
        'text', // varchar(100) just a text
    );

    /**
     * @var string
     */
    protected $tableName = 'foo';

    /**
     * @param int $id int(20) not null auto_increment
     */
    public function setId($id) {
        $this->data['id'] = $id;
    }

    /**
     * @return boolean
     */
    public function issetId() {
        return isset($this->data['id']);
    }

    /**
     * @return int
     */
    public function getId() {
        return $this->data['id'];
    }

    /**
     * @param varchar $text varchar(100) just a text
     */
    public function setText($text) {
        $this->data['text'] = $text;
    }

    /**
     * @return boolean
     */
    public function issetText() {
        return isset($this->data['text']);
    }

    /**
     * @return varchar
     */
    public function getText() {
        return $this->data['text'];
    }
}