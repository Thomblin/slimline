<?php
/**
 * @author sebastian.detert <github@elygor.de>
 * @date 28.05.13
 * @time 19:15
 * @license property of Sebastian Detert
 */

namespace de\detert\sebastian\slimline\db;


class Model_Create
{
    /**
     * @var Model_Repository
     */
    private $repository;

    public function __construct(Model_Repository $repository)
    {
        $this->repository = $repository;
    }

    public function createModels($dir)
    {
        $tables = $this->repository->getAllTables();

        /** @var Model_Table $table */
        foreach ( $tables as $tableName => $table ) {
            $columns = '';

            /** @var Model_Column $column */
            foreach ( $table->columns as $name => $column ) {

                $columns .= "
    /**
     * {$column->getDescription()}
     *
     * @var {$column->data_type}
     */
    public \$$name;";

            }

            $template = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'template');
            $template = str_replace('%columns%', $columns, $template);

            file_put_contents($dir . DS . $tableName . '.php', $template);
        }
    }
}