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
 * Provides a basic interface for retreiving metadata from a database.
 *
 * @since      Class available since Release 1.0.0
 */
interface PHPUnit_Extensions_Database_DB_IMetaData
{
    /**
     * Returns an array containing the names of all the tables in the database.
     *
     * @return array
     */
    public function getTableNames();

    /**
     * Returns an array containing the names of all the columns in the
     * $tableName table,
     *
     * @param  string $tableName
     * @return array
     */
    public function getTableColumns($tableName);

    /**
     * Returns an array containing the names of all the primary key columns in
     * the $tableName table.
     *
     * @param  string $tableName
     * @return array
     */
    public function getTablePrimaryKeys($tableName);

    /**
     * Returns the name of the default schema.
     *
     * @return string
     */
    public function getSchema();

    /**
     * Returns a quoted schema object. (table name, column name, etc)
     *
     * @param  string $object
     * @return string
     */
    public function quoteSchemaObject($object);

    /**
     * Returns true if the rdbms allows cascading
     *
     * @return bool
     */
    public function allowsCascading();

    /**
     * Disables primary keys if rdbms does not allow setting them otherwise
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
