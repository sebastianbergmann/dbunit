<?php
/*
 * This file is part of DBUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPUnit\DbUnit\Operation;

use PHPUnit\DbUnit\Database\IConnection;
use PHPUnit\DbUnit\DataSet\IDataSet;
use PHPUnit\DbUnit\DataSet\ITable;
use PHPUnit\DbUnit\DataSet\ITableMetadata;

/**
 * Provides basic functionality for row based operations.
 *
 * To create a row based operation you must create two functions. The first
 * one, buildOperationQuery(), must return a query that will be used to create
 * a prepared statement. The second one, buildOperationArguments(), should
 * return an array containing arguments for each row.
 */
abstract class RowBased implements Operation
{
    const ITERATOR_TYPE_FORWARD = 0;
    const ITERATOR_TYPE_REVERSE = 1;

    protected $operationName;

    protected $iteratorDirection = self::ITERATOR_TYPE_FORWARD;

    /**
     * @return string|bool String containing the query or FALSE if a valid query cannot be constructed
     */
    protected abstract function buildOperationQuery(ITableMetadata $databaseTableMetaData, ITable $table, IConnection $connection);

    protected abstract function buildOperationArguments(ITableMetadata $databaseTableMetaData, ITable $table, $row);

    /**
     * Allows an operation to disable primary keys if necessary.
     *
     * @param ITableMetadata $databaseTableMetaData
     * @param ITable $table
     * @param IConnection $connection
     */
    protected function disablePrimaryKeys(ITableMetadata $databaseTableMetaData, ITable $table, IConnection $connection)
    {
        return false;
    }

    /**
     * @param IConnection $connection
     * @param IDataSet $dataSet
     */
    public function execute(IConnection $connection, IDataSet $dataSet)
    {
        $databaseDataSet = $connection->createDataSet();

        $dsIterator = $this->iteratorDirection == self::ITERATOR_TYPE_REVERSE ? $dataSet->getReverseIterator() : $dataSet->getIterator();

        foreach ($dsIterator as $table) {
            $rowCount = $table->getRowCount();

            if ($rowCount == 0) continue;

            /* @var $table ITable */
            $databaseTableMetaData = $databaseDataSet->getTableMetaData($table->getTableMetaData()->getTableName());
            $query = $this->buildOperationQuery($databaseTableMetaData, $table, $connection);
            $disablePrimaryKeys = $this->disablePrimaryKeys($databaseTableMetaData, $table, $connection);

            if ($query === false) {
                if ($table->getRowCount() > 0) {
                    throw new Exception($this->operationName, '', [], $table, 'Rows requested for insert, but no columns provided!');
                }
                continue;
            }

            if ($disablePrimaryKeys) {
                $connection->disablePrimaryKeys($databaseTableMetaData->getTableName());
            }

            $statement = $connection->getConnection()->prepare($query);

            for ($i = 0; $i < $rowCount; $i++) {
                $args = $this->buildOperationArguments($databaseTableMetaData, $table, $i);

                try {
                    $statement->execute($args);
                } catch (Exception $e) {
                    throw new Exception(
                        $this->operationName, $query, $args, $table, $e->getMessage()
                    );
                }
            }

            if ($disablePrimaryKeys) {
                $connection->enablePrimaryKeys($databaseTableMetaData->getTableName());
            }
        }
    }

    protected function buildPreparedColumnArray($columns, IConnection $connection)
    {
        $columnArray = [];

        foreach ($columns as $columnName) {
            $columnArray[] = "{$connection->quoteSchemaObject($columnName)} = ?";
        }

        return $columnArray;
    }
}
