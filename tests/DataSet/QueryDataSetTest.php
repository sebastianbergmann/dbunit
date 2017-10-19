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
use PHPUnit\DbUnit\DataSet\DefaultTable;
use PHPUnit\DbUnit\DataSet\DefaultTableMetadata;
use PHPUnit\DbUnit\DataSet\ITable;
use PHPUnit\DbUnit\DataSet\QueryDataSet;
use PHPUnit\DbUnit\TestCase;

class Extensions_Database_DataSet_QueryDataSetTest extends TestCase
{
    /**
     * @var QueryDataSet
     */
    protected $dataSet;

    protected $pdo;

    /**
     * @return DefaultConnection
     */
    protected function getConnection()
    {
        return $this->createDefaultDBConnection($this->pdo, 'test');
    }

    protected function getDataSet()
    {
        return $this->createFlatXMLDataSet(\dirname(__FILE__) . '/../_files/XmlDataSets/QueryDataSetTest.xml');
    }

    public function setUp()
    {
        $this->pdo = DBUnitTestUtility::getSQLiteMemoryDB();
        parent::setUp();
        $this->dataSet = new QueryDataSet($this->getConnection());
        $this->dataSet->addTable('table1');
        $this->dataSet->addTable('query1', '
            SELECT
                t1.column1 tc1, t2.column5 tc2
            FROM
                table1 t1
                JOIN table2 t2 ON t1.table1_id = t2.table2_id
        ');
    }

    public function testGetTable()
    {
        $expectedTable1 = $this->getConnection()->createDataSet(['table1'])->getTable('table1');

        $expectedTable2 = new DefaultTable(
            new DefaultTableMetadata('query1', ['tc1', 'tc2'])
        );

        $expectedTable2->addRow(['tc1' => 'bar', 'tc2' => 'blah']);

        $this->assertTablesEqual($expectedTable1, $this->dataSet->getTable('table1'));
        $this->assertTablesEqual($expectedTable2, $this->dataSet->getTable('query1'));
    }

    public function testGetTableNames()
    {
        $this->assertEquals(['table1', 'query1'], $this->dataSet->getTableNames());
    }

    public function testCreateIterator()
    {
        $expectedTable1 = $this->getConnection()->createDataSet(['table1'])->getTable('table1');

        $expectedTable2 = new DefaultTable(
            new DefaultTableMetadata('query1', ['tc1', 'tc2'])
        );

        $expectedTable2->addRow(['tc1' => 'bar', 'tc2' => 'blah']);

        foreach ($this->dataSet as $i => $table) {
            /* @var $table ITable */
            switch ($table->getTableMetaData()->getTableName()) {
                case 'table1':
                    $this->assertTablesEqual($expectedTable1, $table);
                    break;
                case 'query1':
                    $this->assertTablesEqual($expectedTable2, $table);
                    break;
                default:
                    $this->fail('Proper keys not present from the iterator');
            }
        }
    }
}
