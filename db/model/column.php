<?php
/**
 * @author sebastian.detert <github@elygor.de>
 * @date 28.05.13
 * @time 20:12
 * @license property of Sebastian Detert
 */

namespace de\detert\sebastian\slimline\db;


class Model_Column {
    /**
     * @var string
     */
    public $data_type;
    /**
     * @var string
     */
    public $column_type;
    /**
     * @var string
     */
    public $character_maximum_length;
    /**
     * @var string
     */
    public $extra;
    /**
     * @var boolean
     */
    public $is_nullable;
    /**
     * @var string
     */
    public $column_comment;

    public function getDescription()
    {
        $comment = $this->column_type;
        if ( ! $this->is_nullable ) {
            $comment .= ' not null';
        }
        if ( ! empty($this->extra ) ) {
            $comment .= ' ' . $this->extra;
        }
        if ( ! empty($this->column_comment ) ) {
            $comment .= ' ' . $this->column_comment;
        }

        return $comment;
    }
}