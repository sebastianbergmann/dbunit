<?php
/*
 * This file is part of DbUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use PHPUnit\DbUnit\Database\DefaultConnection;
use PHPUnit\DbUnit\DataSet\DefaultDataSet;
use PHPUnit\DbUnit\DataSet\DefaultTable;
use PHPUnit\DbUnit\DataSet\DefaultTableMetadata;
use PHPUnit\DbUnit\DataSet\FlatXmlDataSet;
use PHPUnit\DbUnit\Operation\Delete;
use PHPUnit\DbUnit\Operation\DeleteAll;
use PHPUnit\DbUnit\Operation\Insert;
use PHPUnit\DbUnit\Operation\Replace;
use PHPUnit\DbUnit\Operation\Truncate;
use PHPUnit\DbUnit\Operation\Update;
use PHPUnit\DbUnit\TestCase;

require_once \dirname(__DIR__) . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'DatabaseTestUtility.php';

class Extensions_Database_Operation_OperationsTest extends TestCase
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
        return new FlatXmlDataSet(__DIR__ . '/../_files/XmlDataSets/OperationsTestFixture.xml');
    }

    public function testDelete(): void
    {
        $deleteOperation = new Delete();

        $deleteOperation->execute($this->getConnection(), new FlatXmlDataSet(__DIR__ . '/../_files/XmlDataSets/DeleteOperationTest.xml'));

        $this->assertDataSetsEqual(new FlatXmlDataSet(__DIR__ . '/../_files/XmlDataSets/DeleteOperationResult.xml'), $this->getConnection()->createDataSet());
    }

    public function testDeleteAll(): void
    {
        $deleteAllOperation = new DeleteAll();

        $deleteAllOperation->execute($this->getConnection(), new FlatXmlDataSet(__DIR__ . '/../_files/XmlDataSets/DeleteAllOperationTest.xml'));

        $expectedDataSet = new DefaultDataSet([
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
        ]);

        $this->assertDataSetsEqual($expectedDataSet, $this->getConnection()->createDataSet());
    }

    public function testTruncate(): void
    {
        $truncateOperation = new Truncate();

        $truncateOperation->execute($this->getConnection(), new FlatXmlDataSet(__DIR__ . '/../_files/XmlDataSets/DeleteAllOperationTest.xml'));

        $expectedDataSet = new DefaultDataSet([
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
        ]);

        $this->assertDataSetsEqual($expectedDataSet, $this->getConnection()->createDataSet());
    }

    public function testInsert(): void
    {
        $insertOperation = new Insert();

        $insertOperation->execute($this->getConnection(), new FlatXmlDataSet(__DIR__ . '/../_files/XmlDataSets/InsertOperationTest.xml'));

        $this->assertDataSetsEqual(new FlatXmlDataSet(__DIR__ . '/../_files/XmlDataSets/InsertOperationResult.xml'), $this->getConnection()->createDataSet());
    }

    public function testUpdate(): void
    {
        $updateOperation = new Update();

        $updateOperation->execute($this->getConnection(), new FlatXmlDataSet(__DIR__ . '/../_files/XmlDataSets/UpdateOperationTest.xml'));

        $this->assertDataSetsEqual(new FlatXmlDataSet(__DIR__ . '/../_files/XmlDataSets/UpdateOperationResult.xml'), $this->getConnection()->createDataSet());
    }

    public function testReplace(): void
    {
        $replaceOperation = new Replace();

        $replaceOperation->execute($this->getConnection(), new FlatXmlDataSet(__DIR__ . '/../_files/XmlDataSets/ReplaceOperationTest.xml'));

        $this->assertDataSetsEqual(new FlatXmlDataSet(__DIR__ . '/../_files/XmlDataSets/ReplaceOperationResult.xml'), $this->getConnection()->createDataSet());
    }

    public function testInsertEmptyTable(): void
    {
        $insertOperation = new Insert();

        $insertOperation->execute($this->getConnection(), new FlatXmlDataSet(__DIR__ . '/../_files/XmlDataSets/EmptyTableInsertTest.xml'));

        $this->assertDataSetsEqual(new FlatXmlDataSet(__DIR__ . '/../_files/XmlDataSets/EmptyTableInsertResult.xml'), $this->getConnection()->createDataSet());
    }

    public function testInsertAllEmptyTables(): void
    {
        $insertOperation = new Insert();

        $insertOperation->execute($this->getConnection(), new FlatXmlDataSet(__DIR__ . '/../_files/XmlDataSets/AllEmptyTableInsertTest.xml'));

        $this->assertDataSetsEqual(new FlatXmlDataSet(__DIR__ . '/../_files/XmlDataSets/AllEmptyTableInsertResult.xml'), $this->getConnection()->createDataSet());
    }
}
