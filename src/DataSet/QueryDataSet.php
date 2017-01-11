<?php
/*
 * This file is part of DBUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use PHPUnit\DbUnit\Database\IConnection;
use PHPUnit\DbUnit\Database\Table;
use PHPUnit\DbUnit\InvalidArgumentException;

/**
 * Provides access to a database instance as a data set.
 */
class PHPUnit_Extensions_Database_DataSet_QueryDataSet extends PHPUnit_Extensions_Database_DataSet_AbstractDataSet
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
     * @var IConnection
     */
    protected $databaseConnection;

    /**
     * Creates a new dataset using the given database connection.
     *
     * @param IConnection $databaseConnection
     */
    public function __construct(IConnection $databaseConnection)
    {
        $this->databaseConnection = $databaseConnection;
    }

    public function addTable($tableName, $query = null)
    {
        if ($query === null) {
            $query = 'SELECT * FROM ' . $tableName;
        }

        $this->tables[$tableName] = new PHPUnit_Extensions_Database_DataSet_QueryTable($tableName, $query, $this->databaseConnection);
    }

    /**
     * Creates an iterator over the tables in the data set. If $reverse is
     * true a reverse iterator will be returned.
     *
     * @param  bool                                         $reverse
     * @return PHPUnit_Extensions_Database_DB_TableIterator
     */
    protected function createIterator($reverse = false)
    {
        return new PHPUnit_Extensions_Database_DataSet_DefaultTableIterator($this->tables, $reverse);
    }

    /**
     * Returns a table object for the given table.
     *
     * @param  string                               $tableName
     * @return Table
     */
    public function getTable($tableName)
    {
        if (!isset($this->tables[$tableName])) {
            throw new InvalidArgumentException("$tableName is not a table in the current database.");
        }

        return $this->tables[$tableName];
    }

    /**
     * Returns a list of table names for the database
     *
     * @return array
     */
    public function getTableNames()
    {
        return array_keys($this->tables);
    }
}
