<?php
/**
 * @author sebastian.detert <github@elygor.de>
 * @date 28.05.13
 * @time 20:12
 * @license property of Sebastian Detert
 */

namespace de\detert\sebastian\slimline\db\model;
use de\detert\sebastian\slimline\db\Model as Model;

class HandlerModel extends Model
{
    /**
     * @var array
     */
    protected $columns = array(
        'id', // int(20) not null auto_increment
        'text', // varchar(100)
    );
}