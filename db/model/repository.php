<?php
namespace de\detert\sebastian\slimline\db;

use de\detert\sebastian\slimline\Repository as SlimlineRepository;

/**
 * @author sebastian.detert <github@elygor.de>
 * @date 26.05.13
 * @time 22:28
 * @license property of Sebastian Detert
 */
class Model_Repository extends SlimlineRepository
{
    protected $db;

    public function __construct(Handler $db)
    {
        $this->db = $db;
    }

    /**
     * @return array of Model_Table
     */
    public function getAllTables()
    {
        $db = $this->db->fetchAll("SELECT DATABASE()");
        $db = current($db[0]);

        $sql = "SELECT
            TABLE_NAME,
            COLUMN_NAME,
            IS_NULLABLE,
            DATA_TYPE,
            CHARACTER_MAXIMUM_LENGTH,
            COLUMN_TYPE,
            EXTRA,
            COLUMN_COMMENT
        FROM
            information_schema.COLUMNS AS c
        WHERE
            c.TABLE_SCHEMA = ?
        ";
        $result = $this->db->fetchAll($sql, array($db));

        $tables = array();

        foreach ( $result as $row ) {
            isset($tables[$row['TABLE_NAME']])
                || $tables[$row['TABLE_NAME']] = new Model_Table();

            $column = new Model_Column();
            $column->data_type = $row['DATA_TYPE'];
            $column->column_type = $row['COLUMN_TYPE'];
            $column->is_nullable = $row['IS_NULLABLE'] === 'YES';
            $column->column_comment = $row['COLUMN_COMMENT'];
            $column->character_maximum_length = $row['CHARACTER_MAXIMUM_LENGTH'];
            $column->extra = $row['EXTRA'];

            $tables[$row['TABLE_NAME']]->columns[$row['COLUMN_NAME']] = $column;
        }

        return $tables;
    }
}