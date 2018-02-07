<?php
/*
 * This file is part of DbUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPUnit\DbUnit\DataSet;

use PHPUnit\DbUnit\Database\Connection;
use PHPUnit\DbUnit\Database\Table;
use PHPUnit\DbUnit\Database\TableIterator;
use PHPUnit\DbUnit\InvalidArgumentException;

/**
 * Provides access to a database instance as a data set.
 */
class QueryDataSet extends AbstractDataSet
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
     * @var Connection
     */
    protected $databaseConnection;

    /**
     * Creates a new dataset using the given database connection.
     *
     * @param Connection $databaseConnection
     */
    public function __construct(Connection $databaseConnection)
    {
        $this->databaseConnection = $databaseConnection;
    }

    public function addTable($tableName, $query = null): void
    {
        if ($query === null) {
            $query = 'SELECT * FROM ' . $tableName;
        }

        $this->tables[$tableName] = new QueryTable($tableName, $query, $this->databaseConnection);
    }

    /**
     * Returns a table object for the given table.
     *
     * @param string $tableName
     *
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
        return \array_keys($this->tables);
    }

    /**
     * Creates an iterator over the tables in the data set. If $reverse is
     * true a reverse iterator will be returned.
     *
     * @param bool $reverse
     *
     * @return TableIterator
     */
    protected function createIterator($reverse = false)
    {
        return new DefaultTableIterator($this->tables, $reverse);
    }
}
