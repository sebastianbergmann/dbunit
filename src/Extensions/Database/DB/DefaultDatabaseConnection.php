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
 * Provides a basic interface for communicating with a database.
 *
 * @since      Class available since Release 1.0.0
 */
class PHPUnit_Extensions_Database_DB_DefaultDatabaseConnection implements PHPUnit_Extensions_Database_DB_IDatabaseConnection
{
    /**
     * @var PDO
     */
    protected $connection;

    /**
     * The metadata object used to retrieve table meta data from the database.
     *
     * @var PHPUnit_Extensions_Database_DB_IMetaData
     */
    protected $metaData;

    /**
     * Creates a new database connection
     *
     * @param PDO    $connection
     * @param string $schema     - The name of the database schema you will be testing against.
     */
    public function __construct(PDO $connection, $schema = '')
    {
        $this->connection = $connection;
        $this->metaData   = PHPUnit_Extensions_Database_DB_MetaData::createMetaData($connection, $schema);
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    /**
     * Close this connection.
     */
    public function close()
    {
        unset($this->connection);
    }

    /**
     * Returns a database metadata object that can be used to retrieve table
     * meta data from the database.
     *
     * @return PHPUnit_Extensions_Database_DB_IMetaData
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
     * @param  array                                        $tableNames
     * @return PHPUnit_Extensions_Database_DataSet_IDataSet
     * @todo Implement the filtered data set.
     */
    public function createDataSet(array $tableNames = NULL)
    {
        if (empty($tableNames)) {
            return new PHPUnit_Extensions_Database_DB_DataSet($this);
        } else {
            return new PHPUnit_Extensions_Database_DB_FilteredDataSet($this, $tableNames);
        }
    }

    /**
     * Creates a table with the result of the specified SQL statement.
     *
     * @param  string                               $resultName
     * @param  string                               $sql
     * @return PHPUnit_Extensions_Database_DB_Table
     */
    public function createQueryTable($resultName, $sql)
    {
        return new PHPUnit_Extensions_Database_DataSet_QueryTable($resultName, $sql, $this);
    }

    /**
     * Returns this connection database configuration
     *
     * @return PHPUnit_Extensions_Database_Database_DatabaseConfig
     */
    public function getConfig()
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
     * @param  string $tableName
     * @param  string $whereClause
     * @return int
     */
    public function getRowCount($tableName, $whereClause = NULL)
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
     * @param  string $object
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
    public function disablePrimaryKeys($tableName)
    {
        $this->getMetaData()->disablePrimaryKeys($tableName);
    }

    /**
     * Reenables primary keys after they have been disabled
     *
     * @param string $tableName
     */
    public function enablePrimaryKeys($tableName)
    {
        $this->getMetaData()->enablePrimaryKeys($tableName);
    }
}
