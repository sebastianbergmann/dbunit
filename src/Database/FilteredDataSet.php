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

/**
 * Provides access to a database instance as a data set.
 */
class FilteredDataSet extends DataSet
{
    /**
     * @var array
     */
    protected $tableNames;

    /**
     * Creates a new dataset using the given database connection.
     *
     * @param Connection $databaseConnection
     */
    public function __construct(Connection $databaseConnection, array $tableNames)
    {
        parent::__construct($databaseConnection);
        $this->tableNames = $tableNames;
    }

    /**
     * Returns a list of table names for the database
     *
     * @return array
     */
    public function getTableNames()
    {
        return $this->tableNames;
    }
}
