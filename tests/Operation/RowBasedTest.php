<?php
/*
 * This file is part of DBUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'DatabaseTestUtility.php';

/**
 * @since      File available since Release 1.0.0
 */
class Extensions_Database_Operation_RowBasedTest extends PHPUnit_Extensions_Database_TestCase
{
    protected function setUp()
    {
        if (!extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('PDO/SQLite is required to run this test.');
        }

        parent::setUp();
    }

    public function getConnection()
    {
        return new PHPUnit_Extensions_Database_DB_DefaultDatabaseConnection(DBUnitTestUtility::getSQLiteMemoryDB(), 'sqlite');
    }

    public function getDataSet()
    {
        $tables = [
            new PHPUnit_Extensions_Database_DataSet_DefaultTable(
                new PHPUnit_Extensions_Database_DataSet_DefaultTableMetaData('table1',
                    ['table1_id', 'column1', 'column2', 'column3', 'column4'])
            ),
            new PHPUnit_Extensions_Database_DataSet_DefaultTable(
                new PHPUnit_Extensions_Database_DataSet_DefaultTableMetaData('table2',
                    ['table2_id', 'column5', 'column6', 'column7', 'column8'])
            ),
            new PHPUnit_Extensions_Database_DataSet_DefaultTable(
                new PHPUnit_Extensions_Database_DataSet_DefaultTableMetaData('table3',
                    ['table3_id', 'column9', 'column10', 'column11', 'column12'])
            ),
        ];

        return new PHPUnit_Extensions_Database_DataSet_DefaultDataSet($tables);
    }

    public function testExecute()
    {
        $connection = $this->getConnection();
        /* @var $connection PHPUnit_Extensions_Database_DB_DefaultDatabaseConnection */
        $table1 = new PHPUnit_Extensions_Database_DataSet_DefaultTable(
            new PHPUnit_Extensions_Database_DataSet_DefaultTableMetaData('table1', ['table1_id', 'column1', 'column2', 'column3', 'column4'])
        );

        $table1->addRow([
            'table1_id' => 1,
            'column1'   => 'foo',
            'column2'   => 42,
            'column3'   => 4.2,
            'column4'   => 'bar'
        ]);

        $table1->addRow([
            'table1_id' => 2,
            'column1'   => 'qwerty',
            'column2'   => 23,
            'column3'   => 2.3,
            'column4'   => 'dvorak'
        ]);

        $table2 = new PHPUnit_Extensions_Database_DataSet_DefaultTable(
            new PHPUnit_Extensions_Database_DataSet_DefaultTableMetaData('table2', ['table2_id', 'column5', 'column6', 'column7', 'column8'])
        );

        $table2->addRow([
            'table2_id' => 1,
            'column5'   => 'fdyhkn',
            'column6'   => 64,
            'column7'   => 4568.64,
            'column8'   => 'hkladfg'
        ]);

        $dataSet = new PHPUnit_Extensions_Database_DataSet_DefaultDataSet([$table1, $table2]);

        $mockOperation = $this->getMock('PHPUnit_Extensions_Database_Operation_RowBased',
                ['buildOperationQuery', 'buildOperationArguments']);

        /* @var $mockOperation PHPUnit_Framework_MockObject_MockObject */
        $mockOperation->expects($this->at(0))
                ->method('buildOperationQuery')
                ->with($connection->createDataSet()->getTableMetaData('table1'), $table1)
                ->will(
                    $this->returnValue('INSERT INTO table1 (table1_id, column1, column2, column3, column4) VALUES (?, ?, ?, ?, ?)')
                );

        $mockOperation->expects($this->at(1))
                ->method('buildOperationArguments')
                ->with($connection->createDataSet()->getTableMetaData('table1'), $table1, 0)
                ->will(
                    $this->returnValue([1, 'foo', 42, 4.2, 'bar'])
                );

        $mockOperation->expects($this->at(2))
                ->method('buildOperationArguments')
                ->with($connection->createDataSet()->getTableMetaData('table1'), $table1, 1)
                ->will(
                    $this->returnValue([2, 'qwerty', 23, 2.3, 'dvorak'])
                );

        $mockOperation->expects($this->at(3))
                ->method('buildOperationQuery')
                ->with($connection->createDataSet()->getTableMetaData('table2'), $table2)
                ->will(
                    $this->returnValue('INSERT INTO table2 (table2_id, column5, column6, column7, column8) VALUES (?, ?, ?, ?, ?)')
                );

        $mockOperation->expects($this->at(4))
                ->method('buildOperationArguments')
                ->with($connection->createDataSet()->getTableMetaData('table2'), $table2, 0)
                ->will(
                    $this->returnValue([1, 'fdyhkn', 64, 4568.64, 'hkladfg'])
                );

        /* @var $mockOperation PHPUnit_Extensions_Database_Operation_RowBased */
        $mockOperation->execute($connection, $dataSet);

        $this->assertDataSetsEqual(new PHPUnit_Extensions_Database_DataSet_FlatXmlDataSet(dirname(__FILE__) . '/../_files/XmlDataSets/RowBasedExecute.xml'), $connection->createDataSet(['table1', 'table2']));
    }

    public function testExecuteWithBadQuery()
    {
        $mockDatabaseDataSet = $this->getMock('PHPUnit_Extensions_Database_DataSet_DefaultDataSet');
        $mockDatabaseDataSet->expects($this->never())->method('getTableMetaData');

        $mockConnection = $this->getMock('PHPUnit_Extensions_Database_DB_IDatabaseConnection');
        $mockConnection->expects($this->once())->method('createDataSet')->will($this->returnValue($mockDatabaseDataSet));
        foreach (['getConnection', 'disablePrimaryKeys', 'enablePrimaryKeys'] as $method) {
            $mockConnection->expects($this->never())->method($method);
        }

        $mockTableMetaData = $this->getMock('PHPUnit_Extensions_Database_DataSet_ITableMetaData');
        $mockTableMetaData->expects($this->any())->method('getTableName')->will($this->returnValue('table'));
        $mockTable = $this->getMock('PHPUnit_Extensions_Database_DataSet_ITable');
        $mockTable->expects($this->any())->method('getTableMetaData')->will($this->returnValue($mockTableMetaData));
        $mockTable->expects($this->once())->method('getRowCount')->will($this->returnValue(0));

        $mockDataSet = $this->getMock('PHPUnit_Extensions_Database_DataSet_DefaultDataSet');
        $mockDataSet->expects($this->once())->method('getIterator')->will($this->returnValue(new ArrayIterator([$mockTable])));

        $mockOperation = $this->getMock('PHPUnit_Extensions_Database_Operation_RowBased', ['buildOperationQuery', 'buildOperationArguments']);
        $mockOperation->expects($this->never())->method('buildOperationArguments');
        $mockOperation->expects($this->never())->method('buildOperationQuery');

        $mockOperation->execute($mockConnection, $mockDataSet);
    }
}
