<?php
/*
 * This file is part of DBUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Provides access to a database instance as a data set.
 *
 * @since      Class available since Release 1.0.0
 */
class PHPUnit_Extensions_Database_DB_DataSet extends PHPUnit_Extensions_Database_DataSet_AbstractDataSet
{
    /**
     * An array of ITable objects.
     *
     * @var array
     */
    protected $tables = [];

    /**
     * The database connection this dataset is using.
     *
     * @var PHPUnit_Extensions_Database_DB_IDatabaseConnection
     */
    protected $databaseConnection;

    /**
     * Creates a new dataset using the given database connection.
     *
     * @param PHPUnit_Extensions_Database_DB_IDatabaseConnection $databaseConnection
     */
    public function __construct(PHPUnit_Extensions_Database_DB_IDatabaseConnection $databaseConnection)
    {
        $this->databaseConnection = $databaseConnection;
    }

    /**
     * Creates the query necessary to pull all of the data from a table.
     *
     * @param  PHPUnit_Extensions_Database_DataSet_ITableMetaData $tableMetaData
     * @return unknown
     */
    public static function buildTableSelect(PHPUnit_Extensions_Database_DataSet_ITableMetaData $tableMetaData, PHPUnit_Extensions_Database_DB_IDatabaseConnection $databaseConnection = NULL)
    {
        if ($tableMetaData->getTableName() == '') {
            $e = new Exception('Empty Table Name');
            echo $e->getTraceAsString();
            throw $e;
        }

        $columns = $tableMetaData->getColumns();
        if ($databaseConnection) {
            $columns = array_map([$databaseConnection, 'quoteSchemaObject'], $columns);
        }
        $columnList = implode(', ', $columns);

        if ($databaseConnection) {
            $tableName = $databaseConnection->quoteSchemaObject($tableMetaData->getTableName());
        } else {
            $tableName = $tableMetaData->getTableName();
        }

        $primaryKeys = $tableMetaData->getPrimaryKeys();
        if ($databaseConnection) {
            $primaryKeys = array_map([$databaseConnection, 'quoteSchemaObject'], $primaryKeys);
        }
        if (count($primaryKeys)) {
            $orderBy = 'ORDER BY ' . implode(' ASC, ', $primaryKeys) . ' ASC';
        } else {
            $orderBy = '';
        }

        return "SELECT {$columnList} FROM {$tableName} {$orderBy}";
    }

    /**
     * Creates an iterator over the tables in the data set. If $reverse is
     * true a reverse iterator will be returned.
     *
     * @param  bool                                         $reverse
     * @return PHPUnit_Extensions_Database_DB_TableIterator
     */
    protected function createIterator($reverse = FALSE)
    {
        return new PHPUnit_Extensions_Database_DB_TableIterator($this->getTableNames(), $this, $reverse);
    }

    /**
     * Returns a table object for the given table.
     *
     * @param  string                               $tableName
     * @return PHPUnit_Extensions_Database_DB_Table
     */
    public function getTable($tableName)
    {
        if (!in_array($tableName, $this->getTableNames())) {
            throw new InvalidArgumentException("$tableName is not a table in the current database.");
        }

        if (empty($this->tables[$tableName])) {
            $this->tables[$tableName] = new PHPUnit_Extensions_Database_DB_Table($this->getTableMetaData($tableName), $this->databaseConnection);
        }

        return $this->tables[$tableName];
    }

    /**
     * Returns a table meta data object for the given table.
     *
     * @param  string                                                   $tableName
     * @return PHPUnit_Extensions_Database_DataSet_DefaultTableMetaData
     */
    public function getTableMetaData($tableName)
    {
        return new PHPUnit_Extensions_Database_DataSet_DefaultTableMetaData($tableName, $this->databaseConnection->getMetaData()->getTableColumns($tableName), $this->databaseConnection->getMetaData()->getTablePrimaryKeys($tableName));
    }

    /**
     * Returns a list of table names for the database
     *
     * @return Array
     */
    public function getTableNames()
    {
        return $this->databaseConnection->getMetaData()->getTableNames();
    }
}
