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

/**
 * Provides a basic interface for returning table meta data.
 */
interface ITableMetadata
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
     * @param ITableMetadata $other
     */
    public function matches(ITableMetadata $other);
}
