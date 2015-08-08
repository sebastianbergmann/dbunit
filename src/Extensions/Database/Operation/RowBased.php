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
 * Provides basic functionality for row based operations.
 *
 * To create a row based operation you must create two functions. The first
 * one, buildOperationQuery(), must return a query that will be used to create
 * a prepared statement. The second one, buildOperationArguments(), should
 * return an array containing arguments for each row.
 *
 * @since      Class available since Release 1.0.0
 */
abstract class PHPUnit_Extensions_Database_Operation_RowBased implements PHPUnit_Extensions_Database_Operation_IDatabaseOperation
{
    const ITERATOR_TYPE_FORWARD = 0;
    const ITERATOR_TYPE_REVERSE = 1;

    protected $operationName;

    protected $iteratorDirection = self::ITERATOR_TYPE_FORWARD;

    /**
     * @return string|bool String containing the query or FALSE if a valid query cannot be constructed
     */
    protected abstract function buildOperationQuery(PHPUnit_Extensions_Database_DataSet_ITableMetaData $databaseTableMetaData, PHPUnit_Extensions_Database_DataSet_ITable $table, PHPUnit_Extensions_Database_DB_IDatabaseConnection $connection);

    protected abstract function buildOperationArguments(PHPUnit_Extensions_Database_DataSet_ITableMetaData $databaseTableMetaData, PHPUnit_Extensions_Database_DataSet_ITable $table, $row);

    /**
     * Allows an operation to disable primary keys if necessary.
     *
     * @param PHPUnit_Extensions_Database_DataSet_ITableMetaData $databaseTableMetaData
     * @param PHPUnit_Extensions_Database_DataSet_ITable         $table
     * @param PHPUnit_Extensions_Database_DB_IDatabaseConnection $connection
     */
    protected function disablePrimaryKeys(PHPUnit_Extensions_Database_DataSet_ITableMetaData $databaseTableMetaData, PHPUnit_Extensions_Database_DataSet_ITable $table, PHPUnit_Extensions_Database_DB_IDatabaseConnection $connection)
    {
        return FALSE;
    }

    /**
     * @param PHPUnit_Extensions_Database_DB_IDatabaseConnection $connection
     * @param PHPUnit_Extensions_Database_DataSet_IDataSet       $dataSet
     */
    public function execute(PHPUnit_Extensions_Database_DB_IDatabaseConnection $connection, PHPUnit_Extensions_Database_DataSet_IDataSet $dataSet)
    {
        $databaseDataSet = $connection->createDataSet();

        $dsIterator = $this->iteratorDirection == self::ITERATOR_TYPE_REVERSE ? $dataSet->getReverseIterator() : $dataSet->getIterator();

        foreach ($dsIterator as $table) {
            $rowCount = $table->getRowCount();

            if($rowCount == 0) continue;

            /* @var $table PHPUnit_Extensions_Database_DataSet_ITable */
            $databaseTableMetaData = $databaseDataSet->getTableMetaData($table->getTableMetaData()->getTableName());
            $query                 = $this->buildOperationQuery($databaseTableMetaData, $table, $connection);
            $disablePrimaryKeys    = $this->disablePrimaryKeys($databaseTableMetaData, $table, $connection);

            if ($query === FALSE) {
                if ($table->getRowCount() > 0) {
                    throw new PHPUnit_Extensions_Database_Operation_Exception($this->operationName, '', [], $table, 'Rows requested for insert, but no columns provided!');
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
                }

                catch (Exception $e) {
                    throw new PHPUnit_Extensions_Database_Operation_Exception(
                      $this->operationName, $query, $args, $table, $e->getMessage()
                    );
                }
            }

            if ($disablePrimaryKeys) {
                $connection->enablePrimaryKeys($databaseTableMetaData->getTableName());
            }
        }
    }

    protected function buildPreparedColumnArray($columns, PHPUnit_Extensions_Database_DB_IDatabaseConnection $connection)
    {
        $columnArray = [];

        foreach ($columns as $columnName) {
            $columnArray[] = "{$connection->quoteSchemaObject($columnName)} = ?";
        }

        return $columnArray;
    }
}
