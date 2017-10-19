<?php
/*
 * This file is part of DbUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPUnit\DbUnit\Operation;

use PHPUnit\DbUnit\Database\Connection;
use PHPUnit\DbUnit\DataSet\ITable;
use PHPUnit\DbUnit\DataSet\ITableMetadata;

/**
 * Deletes the rows in a given dataset using primary key columns.
 */
class Delete extends RowBased
{
    protected $operationName = 'DELETE';

    protected $iteratorDirection = self::ITERATOR_TYPE_REVERSE;

    protected function buildOperationQuery(ITableMetadata $databaseTableMetaData, ITable $table, Connection $connection)
    {
        $keys = $databaseTableMetaData->getPrimaryKeys();

        $whereStatement = 'WHERE ' . \implode(' AND ', $this->buildPreparedColumnArray($keys, $connection));

        $query = "
            DELETE FROM {$connection->quoteSchemaObject($table->getTableMetaData()->getTableName())}
            {$whereStatement}
        ";

        return $query;
    }

    protected function buildOperationArguments(ITableMetadata $databaseTableMetaData, ITable $table, $row)
    {
        $args = [];
        foreach ($databaseTableMetaData->getPrimaryKeys() as $columnName) {
            $args[] = $table->getValue($row, $columnName);
        }

        return $args;
    }
}
