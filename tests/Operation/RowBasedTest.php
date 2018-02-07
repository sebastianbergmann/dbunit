<?php
/*
 * This file is part of DbUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use PHPUnit\DbUnit\Database\Connection;
use PHPUnit\DbUnit\Database\DefaultConnection;
use PHPUnit\DbUnit\DataSet\DefaultDataSet;
use PHPUnit\DbUnit\DataSet\DefaultTable;
use PHPUnit\DbUnit\DataSet\DefaultTableMetadata;
use PHPUnit\DbUnit\DataSet\FlatXmlDataSet;
use PHPUnit\DbUnit\DataSet\ITable;
use PHPUnit\DbUnit\DataSet\ITableMetadata;
use PHPUnit\DbUnit\Operation\Exception as OperationException;
use PHPUnit\DbUnit\Operation\RowBased;
use PHPUnit\DbUnit\TestCase;

require_once \dirname(__DIR__) . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'DatabaseTestUtility.php';

class Extensions_Database_Operation_RowBasedTest extends TestCase
{
    protected function setUp(): void
    {
        if (!\extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('PDO/SQLite is required to run this test.');
        }

        parent::setUp();
    }

    public function getConnection()
    {
        return new DefaultConnection(DBUnitTestUtility::getSQLiteMemoryDB(), 'sqlite');
    }

    public function getDataSet()
    {
        $tables = [
            new DefaultTable(
                new DefaultTableMetadata(
                    'table1',
                    ['table1_id', 'column1', 'column2', 'column3', 'column4']
                )
            ),
            new DefaultTable(
                new DefaultTableMetadata(
                    'table2',
                    ['table2_id', 'column5', 'column6', 'column7', 'column8']
                )
            ),
            new DefaultTable(
                new DefaultTableMetadata(
                    'table3',
                    ['table3_id', 'column9', 'column10', 'column11', 'column12']
                )
            ),
        ];

        return new DefaultDataSet($tables);
    }

    public function testExecute(): void
    {
        $connection = $this->getConnection();
        /* @var $connection DefaultConnection */
        $table1 = new DefaultTable(
            new DefaultTableMetadata('table1', ['table1_id', 'column1', 'column2', 'column3', 'column4'])
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

        $table2 = new DefaultTable(
            new DefaultTableMetadata('table2', ['table2_id', 'column5', 'column6', 'column7', 'column8'])
        );

        $table2->addRow([
            'table2_id' => 1,
            'column5'   => 'fdyhkn',
            'column6'   => 64,
            'column7'   => 4568.64,
            'column8'   => 'hkladfg'
        ]);

        $dataSet = new DefaultDataSet([$table1, $table2]);

        $mockOperation = $this->createPartialMock(
            RowBased::class,
                ['buildOperationQuery', 'buildOperationArguments']
        );

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

        /* @var $mockOperation RowBased */
        $mockOperation->execute($connection, $dataSet);

        $this->assertDataSetsEqual(new FlatXmlDataSet(__DIR__ . '/../_files/XmlDataSets/RowBasedExecute.xml'), $connection->createDataSet(['table1', 'table2']));
    }

    public function testExecuteWithBadQuery(): void
    {
        $mockDatabaseDataSet = $this->createMock(DefaultDataSet::class);
        $mockDatabaseDataSet->expects($this->never())->method('getTableMetaData');

        $mockConnection = $this->createMock(Connection::class);
        $mockConnection->expects($this->once())->method('createDataSet')->will($this->returnValue($mockDatabaseDataSet));
        foreach (['getConnection', 'disablePrimaryKeys', 'enablePrimaryKeys'] as $method) {
            $mockConnection->expects($this->never())->method($method);
        }

        $mockTableMetaData = $this->createMock(ITableMetadata::class);
        $mockTableMetaData->expects($this->any())->method('getTableName')->will($this->returnValue('table'));
        $mockTable = $this->createMock(ITable::class);
        $mockTable->expects($this->any())->method('getTableMetaData')->will($this->returnValue($mockTableMetaData));
        $mockTable->expects($this->once())->method('getRowCount')->will($this->returnValue(0));

        $mockDataSet = $this->createMock(DefaultDataSet::class);
        $mockDataSet->expects($this->once())->method('getIterator')->will($this->returnValue(new ArrayIterator([$mockTable])));

        $mockOperation = $this->createPartialMock(
            RowBased::class,
            ['buildOperationQuery', 'buildOperationArguments']
        );
        $mockOperation->expects($this->never())->method('buildOperationArguments');
        $mockOperation->expects($this->never())->method('buildOperationQuery');

        $mockOperation->execute($mockConnection, $mockDataSet);
    }

    public function testExecuteHandlesException(): void
    {
        $this->expectException(OperationException::class);

        $rowCount          = 1;
        $mockTableMetaData = $this->createMock(ITableMetadata::class);
        $mockTableMetaData->expects($this->any())->method('getTableName')->will($this->returnValue('table'));
        $mockTable = $this->createMock(ITable::class);
        $mockTable->expects($this->any())->method('getTableMetaData')->will($this->returnValue($mockTableMetaData));
        $mockTable->expects($this->once())->method('getRowCount')->will($this->returnValue($rowCount));

        $mockDatabaseDataSet = $this->createMock(DefaultDataSet::class);
        $mockDatabaseDataSet->expects($this->once())->method('getTableMetaData')->will($this->returnValue($mockTableMetaData));

        $mockPdoStatement = $this->createMock(PDOStatement::class);
        $mockPdoStatement->expects($this->once())->method('execute')->will($this->throwException(new Exception()));
        $mockPdoConnection = $this->createMock(PDO::class);
        $mockPdoConnection->expects($this->once())->method('prepare')->will($this->returnValue($mockPdoStatement));

        $mockConnection = $this->createMock(Connection::class);
        $mockConnection->expects($this->once())->method('createDataSet')->will($this->returnValue($mockDatabaseDataSet));
        $mockConnection->expects($this->once())->method('getConnection')->will($this->returnValue($mockPdoConnection));
        $mockConnection->expects($this->never())->method('disablePrimaryKeys');
        $mockConnection->expects($this->never())->method('enablePrimaryKeys');

        $mockDataSet = $this->createMock(DefaultDataSet::class);
        $mockDataSet->expects($this->once())->method('getIterator')->will($this->returnValue(new ArrayIterator([$mockTable])));

        $mockOperation = $this->createPartialMock(
            RowBased::class,
            ['buildOperationQuery', 'buildOperationArguments']
        );
        $mockOperation->expects($this->once())->method('buildOperationQuery')->will($this->returnValue(''));
        $mockOperation->expects($this->exactly($rowCount))->method('buildOperationArguments')->will($this->returnValue([]));

        $mockOperation->execute($mockConnection, $mockDataSet);
    }
}
