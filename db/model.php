<?php
namespace de\detert\sebastian\slimline\db;

/**
 * @author sebastian.detert <github@elygor.de>
 * @date 29.05.13
 * @time 20:52
 * @license property of Sebastian Detert
 */
class Model
{
    /**
     * @var array
     */
    protected $columns = array();

    /**
     * @var array
     */
    protected $data;

    /**
     * @var string
     */
    protected $tableName;

    /**
     * @param string $key
     *
     * @return $this
     *
     * @throws Exception_UnkownColumn
     */
    protected function validateColumn($key)
    {
        if ( ! in_array($key, $this->columns) ) {
            throw new Exception_UnkownColumn($key);
        }

        return $this;
    }

    /**
     * @return $this
     *
     * @throws Exception_UnkownColumn
     */
    public function fromArray($array)
    {
        foreach ( $array as $key => $value ) {
            $this->validateColumn($key);
            $this->data[$key] = $value;
        }

        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->data;
    }

    public function getTableName()
    {
        return $this->tableName;
    }
}