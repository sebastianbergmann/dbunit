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
use PHPUnit\DbUnit\Database\Metadata\AbstractMetadata;
use PHPUnit\DbUnit\Database\Metadata\Metadata;
use PHPUnit\DbUnit\DataSet\IDataSet;
use PHPUnit\DbUnit\DataSet\QueryTable;

/**
 * Provides a basic interface for communicating with a database.
 */
class DefaultConnection implements Connection
{
    /**
     * @var PDO
     */
    protected $connection;

    /**
     * The metadata object used to retrieve table meta data from the database.
     *
     * @var Metadata
     */
    protected $metaData;

    /**
     * Creates a new database connection
     *
     * @param PDO    $connection
     * @param string $schema     - The name of the database schema you will be testing against
     */
    public function __construct(PDO $connection, $schema = '')
    {
        $this->connection = $connection;
        $this->metaData   = AbstractMetadata::createMetaData($connection, $schema);
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    /**
     * Close this connection.
     */
    public function close(): void
    {
        unset($this->connection, $this->metaData);
    }

    /**
     * Returns a database metadata object that can be used to retrieve table
     * meta data from the database.
     *
     * @return Metadata
     */
    public function getMetaData()
    {
        return $this->metaData;
    }

    /**
     * Returns the schema for the connection.
     *
     * @return string
     */
    public function getSchema()
    {
        return $this->getMetaData()->getSchema();
    }

    /**
     * Creates a dataset containing the specified table names. If no table
     * names are specified then it will created a dataset over the entire
     * database.
     *
     * @param array $tableNames
     *
     * @return IDataSet
     *
     * @todo Implement the filtered data set.
     */
    public function createDataSet(array $tableNames = null)
    {
        if (empty($tableNames)) {
            return new DataSet($this);
        }

        return new FilteredDataSet($this, $tableNames);
    }

    /**
     * Creates a table with the result of the specified SQL statement.
     *
     * @param string $resultName
     * @param string $sql
     *
     * @return Table
     */
    public function createQueryTable($resultName, $sql)
    {
        return new QueryTable($resultName, $sql, $this);
    }

    /**
     * Returns this connection database configuration
     */
    public function getConfig(): void
    {
    }

    /**
     * Returns a PDO Connection
     *
     * @return PDO
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * Returns the number of rows in the given table. You can specify an
     * optional where clause to return a subset of the table.
     *
     * @param string $tableName
     * @param string $whereClause
     *
     * @return int
     */
    public function getRowCount($tableName, $whereClause = null)
    {
        $query = 'SELECT COUNT(*) FROM ' . $this->quoteSchemaObject($tableName);

        if (isset($whereClause)) {
            $query .= " WHERE {$whereClause}";
        }

        return (int) $this->connection->query($query)->fetchColumn();
    }

    /**
     * Returns a quoted schema object. (table name, column name, etc)
     *
     * @param string $object
     *
     * @return string
     */
    public function quoteSchemaObject($object)
    {
        return $this->getMetaData()->quoteSchemaObject($object);
    }

    /**
     * Returns the command used to truncate a table.
     *
     * @return string
     */
    public function getTruncateCommand()
    {
        return $this->getMetaData()->getTruncateCommand();
    }

    /**
     * Returns true if the connection allows cascading
     *
     * @return bool
     */
    public function allowsCascading()
    {
        return $this->getMetaData()->allowsCascading();
    }

    /**
     * Disables primary keys if connection does not allow setting them otherwise
     *
     * @param string $tableName
     */
    public function disablePrimaryKeys($tableName): void
    {
        $this->getMetaData()->disablePrimaryKeys($tableName);
    }

    /**
     * Reenables primary keys after they have been disabled
     *
     * @param string $tableName
     */
    public function enablePrimaryKeys($tableName): void
    {
        $this->getMetaData()->enablePrimaryKeys($tableName);
    }
}
