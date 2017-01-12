<?php
/*
 * This file is part of DbUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPUnit\DbUnit\Database;

use PDO;
use PHPUnit\DbUnit\Database\Metadata\Metadata;
use PHPUnit\DbUnit\DataSet\IDataSet;
use PHPUnit\DbUnit\DataSet\ITable;

/**
 * Provides a basic interface for communicating with a database.
 */
interface Connection
{
    /**
     * Close this connection.
     */
    public function close();

    /**
     * Creates a dataset containing the specified table names. If no table
     * names are specified then it will created a dataset over the entire
     * database.
     *
     * @param array $tableNames
     *
     * @return IDataSet
     */
    public function createDataSet(array $tableNames = null);

    /**
     * Creates a table with the result of the specified SQL statement.
     *
     * @param string $resultName
     * @param string $sql
     *
     * @return ITable
     */
    public function createQueryTable($resultName, $sql);

    /**
     * Returns a PDO Connection
     *
     * @return PDO
     */
    public function getConnection();

    /**
     * Returns a database metadata object that can be used to retrieve table
     * meta data from the database.
     *
     * @return Metadata
     */
    public function getMetaData();

    /**
     * Returns the number of rows in the given table. You can specify an
     * optional where clause to return a subset of the table.
     *
     * @param string $tableName
     * @param string $whereClause
     * @param int
     */
    public function getRowCount($tableName, $whereClause = null);

    /**
     * Returns the schema for the connection.
     *
     * @return string
     */
    public function getSchema();

    /**
     * Returns a quoted schema object. (table name, column name, etc)
     *
     * @param string $object
     *
     * @return string
     */
    public function quoteSchemaObject($object);

    /**
     * Returns the command used to truncate a table.
     *
     * @return string
     */
    public function getTruncateCommand();

    /**
     * Returns true if the connection allows cascading
     *
     * @return bool
     */
    public function allowsCascading();

    /**
     * Disables primary keys if connection does not allow setting them otherwise
     *
     * @param string $tableName
     */
    public function disablePrimaryKeys($tableName);

    /**
     * Reenables primary keys after they have been disabled
     *
     * @param string $tableName
     */
    public function enablePrimaryKeys($tableName);
}
