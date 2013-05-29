<?php
namespace de\detert\sebastian\slimline\db;

/**
 * database handler for sql queries and more
 *
 * @author sebastian.detert <github@elygor.de>
 * @date 25.05.13
 * @time 09:58
 * @license property of Sebastian Detert
 */
class Handler
{
    /**
     * @var PDO
     */
    private $db;

    /**
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->db = new \PDO($config->dsn, $config->user, $config->password);
        $this->db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $this->db->exec("SET NAMES UTF8");
    }

    /**
     * @param string $sql
     * @param array  $params
     *
     * @return void
     */
    public function query($sql, array $params = array())
    {
        $this->prepareAndExecute($sql, $params);
    }

    /**
     * @param string $sql
     * @param array  $params
     *
     * @return PDOStatement
     */
    private function prepareAndExecute($sql, array $params = array())
    {
        $statement = $this->db->prepare($sql);
        $statement->execute($params);

        return $statement;
    }

    /**
     * @param string $sql
     * @param array  $params
     *
     * @return array
     */
    public function fetchAll($sql, array $params = array())
    {
        return $this->prepareAndExecute($sql, $params)->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * @param string $sql
     * @param string $indexColumn
     * @param array  $params
     *
     * @return array
     */
    public function fetchIndexedBy($sql, $indexColumn, array $params = array())
    {
        $results = array();
        foreach ( $this->fetchAll($sql, $params) as $row ) {
            $results[$row[$indexColumn]] = $row;
        }

        return $results;
    }

    /**
     * @param string $class
     * @param array  $where
     */
    public function loadModel($class, array $where)
    {
        $table = strtolower(
            preg_replace(
                '/(?<=\\w)(?=[A-Z])/',
                "_$1",
                substr($class, strrpos($class, '\\') + 1)
            )
        );

        $sql = "SELECT
                *
            FROM
                `$table`
            WHERE ";

        $params = array();
        foreach ( $where as $column => $value ) {
            $params[] = " `$column` = ? ";
        }

        $sql .= implode(' AND ', $params)." LIMIT 1";

        $result = $this->fetchAll($sql, array_values($where));

        if ( empty($result[0]) ) {
            throw new Exception_Notfound("model $class not found with " . print_r($where, true));
        }

        /** @var Model $model */
        $model = new $class();
        $model->fromArray($result[0]);

        return $model;
    }

    /**
     * @param Model $model
     */
    public function saveModel(Model $model)
    {
        $data  = $model->toArray();
        $table = $model->getTableName();

        $columns = "`" . implode('`,`', array_keys($data)) . "`";
        $values  = implode(', ', array_fill(0, count($data), '?'));

        $update = "`" . implode('` = ?, `', array_keys($data)) . "` = ?";

        $params = array_merge(array_values($data), array_values($data));

        $sql = "INSERT INTO `$table`
            ($columns) VALUES
            ($values)
            ON DUPLICATE KEY UPDATE $update";

        $this->query($sql, $params);
    }
}