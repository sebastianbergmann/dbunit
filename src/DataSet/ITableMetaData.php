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
 * Provides a basic interface for returning table meta data.
 *
 * @since      Class available since Release 1.0.0
 */
interface PHPUnit_Extensions_Database_DataSet_ITableMetaData
{
    /**
     * Returns the names of the columns in the table.
     *
     * @return array
     */
    public function getColumns();

    /**
     * Returns the names of the primary key columns in the table.
     *
     * @return array
     */
    public function getPrimaryKeys();

    /**
     * Returns the name of the table.
     *
     * @return string
     */
    public function getTableName();

    /**
     * Asserts that the given tableMetaData matches this tableMetaData.
     *
     * @param PHPUnit_Extensions_Database_DataSet_ITableMetaData $other
     */
    public function matches(PHPUnit_Extensions_Database_DataSet_ITableMetaData $other);
}
