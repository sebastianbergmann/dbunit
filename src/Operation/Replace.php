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
use PHPUnit\DbUnit\DataSet\IDataSet;
use PHPUnit\DbUnit\DataSet\ITable;
use PHPUnit\DbUnit\DataSet\ITableMetadata;
use PHPUnit\DbUnit\Operation\Exception;

/**
 * Updates the rows in a given dataset using primary key columns.
 */
class PHPUnit_Extensions_Database_Operation_Replace extends PHPUnit_Extensions_Database_Operation_RowBased
{
    protected $operationName = 'REPLACE';

    protected function buildOperationQuery(ITableMetadata $databaseTableMetaData, ITable $table, IConnection $connection)
    {
        $keys = $databaseTableMetaData->getPrimaryKeys();

        $whereStatement = 'WHERE ' . implode(' AND ', $this->buildPreparedColumnArray($keys, $connection));

        $query = "
            SELECT COUNT(*)
            FROM {$connection->quoteSchemaObject($table->getTableMetaData()->getTableName())}
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

    /**
     * @param IConnection $connection
     * @param IDataSet       $dataSet
     */
    public function execute(IConnection $connection, IDataSet $dataSet)
    {
        $insertOperation = new PHPUnit_Extensions_Database_Operation_Insert;
        $updateOperation = new PHPUnit_Extensions_Database_Operation_Update;
        $databaseDataSet = $connection->createDataSet();

        foreach ($dataSet as $table) {
            /* @var $table ITable */
            $databaseTableMetaData = $databaseDataSet->getTableMetaData($table->getTableMetaData()->getTableName());

            $insertQuery = $insertOperation->buildOperationQuery($databaseTableMetaData, $table, $connection);
            $updateQuery = $updateOperation->buildOperationQuery($databaseTableMetaData, $table, $connection);
            $selectQuery = $this->buildOperationQuery($databaseTableMetaData, $table, $connection);

            $insertStatement = $connection->getConnection()->prepare($insertQuery);
            $updateStatement = $connection->getConnection()->prepare($updateQuery);
            $selectStatement = $connection->getConnection()->prepare($selectQuery);

            $rowCount = $table->getRowCount();

            for ($i = 0; $i < $rowCount; $i++) {
                $selectArgs = $this->buildOperationArguments($databaseTableMetaData, $table, $i);
                $query      = $selectQuery;
                $args       = $selectArgs;

                try {
                    $selectStatement->execute($selectArgs);

                    if ($selectStatement->fetchColumn(0) > 0) {
                        $updateArgs = $updateOperation->buildOperationArguments($databaseTableMetaData, $table, $i);
                        $query      = $updateQuery;
                        $args       = $updateArgs;

                        $updateStatement->execute($updateArgs);
                    } else {
                        $insertArgs = $insertOperation->buildOperationArguments($databaseTableMetaData, $table, $i);
                        $query      = $insertQuery;
                        $args       = $insertArgs;

                        $insertStatement->execute($insertArgs);
                    }
                }

                catch (Exception $e) {
                    throw new Exception(
                      $this->operationName, $query, $args, $table, $e->getMessage()
                    );
                }
            }
        }
    }
}
