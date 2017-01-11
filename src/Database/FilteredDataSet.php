<?php
/*
 * This file is part of DBUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
use PHPUnit\DbUnit\Database\DataSet;

/**
 * Provides access to a database instance as a data set.
 */
class PHPUnit_Extensions_Database_DB_FilteredDataSet extends DataSet
{
    /**
     * @var Array
     */
    protected $tableNames;

    /**
     * Creates a new dataset using the given database connection.
     *
     * @param PHPUnit_Extensions_Database_DB_IDatabaseConnection $databaseConnection
     */
    public function __construct(PHPUnit_Extensions_Database_DB_IDatabaseConnection $databaseConnection, Array $tableNames)
    {
        parent::__construct($databaseConnection);
        $this->tableNames = $tableNames;
    }

    /**
     * Returns a list of table names for the database
     *
     * @return Array
     */
    public function getTableNames()
    {
        return $this->tableNames;
    }
}
