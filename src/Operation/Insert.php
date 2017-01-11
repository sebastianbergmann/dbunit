<?php
/*
 * This file is part of DBUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
use PHPUnit\DbUnit\Database\IConnection;

/**
 * This class provides functionality for inserting rows from a dataset into a database.
 */
class PHPUnit_Extensions_Database_Operation_Insert extends PHPUnit_Extensions_Database_Operation_RowBased
{
    protected $operationName = 'INSERT';

    protected function buildOperationQuery(PHPUnit_Extensions_Database_DataSet_ITableMetaData $databaseTableMetaData, PHPUnit_Extensions_Database_DataSet_ITable $table, IConnection $connection)
    {
        $columnCount = count($table->getTableMetaData()->getColumns());

        if ($columnCount > 0) {
            $placeHolders = implode(', ', array_fill(0, $columnCount, '?'));

            $columns = '';
            foreach ($table->getTableMetaData()->getColumns() as $column) {
                $columns .= $connection->quoteSchemaObject($column) . ', ';
            }

            $columns = substr($columns, 0, -2);

            $query = "
                INSERT INTO {$connection->quoteSchemaObject($table->getTableMetaData()->getTableName())}
                ({$columns})
                VALUES
                ({$placeHolders})
            ";

            return $query;
        } else {
            return false;
        }
    }

    protected function buildOperationArguments(PHPUnit_Extensions_Database_DataSet_ITableMetaData $databaseTableMetaData, PHPUnit_Extensions_Database_DataSet_ITable $table, $row)
    {
        $args = [];
        foreach ($table->getTableMetaData()->getColumns() as $columnName) {
            $args[] = $table->getValue($row, $columnName);
        }

        return $args;
    }

    protected function disablePrimaryKeys(PHPUnit_Extensions_Database_DataSet_ITableMetaData $databaseTableMetaData, PHPUnit_Extensions_Database_DataSet_ITable $table, IConnection $connection)
    {
        if (count($databaseTableMetaData->getPrimaryKeys())) {
            return true;
        }

        return false;
    }
}
