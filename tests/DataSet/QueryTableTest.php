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
use PHPUnit\DbUnit\DataSet\QueryTable;
use PHPUnit\Framework\TestCase;

class Extensions_Database_DataSet_QueryTableTest extends TestCase
{
    /**
     * @var QueryTable
     */
    protected $table;

    public function setUp(): void
    {
        $query = "
            SELECT
                'value1' as col1,
                'value2' as col2,
                'value3' as col3
            UNION SELECT
                'value4' as col1,
                'value5' as col2,
                'value6' as col3
        ";
        $this->table = new QueryTable(
            'table1',
            $query,
            new DefaultConnection(new PDO('sqlite::memory:'), 'test')
        );
    }

    public static function providerTestGetValue()
    {
        return [
            [0, 'col1', 'value1'],
            [0, 'col2', 'value2'],
            [0, 'col3', 'value3'],
            [1, 'col1', 'value4'],
            [1, 'col2', 'value5'],
            [1, 'col3', 'value6'],
        ];
    }

    public function testGetEmptyTableMetaData(): void
    {
        $metaData = new DefaultTableMetadata('table1', ['table1_id', 'column1', 'column2', 'column3', 'column4']);

        $conn = new PDO('sqlite::memory:');
        $conn->exec(
          'CREATE TABLE IF NOT EXISTS table1 (
            table1_id INTEGER PRIMARY KEY AUTOINCREMENT,
            column1 VARCHAR(20),
            column2 INT(10),
            column3 DECIMAL(6,2),
            column4 TEXT
          )'
        );

        $query = '
            SELECT *
            FROM table1
        ';

        $empty_table = new QueryTable(
            'table1',
            $query,
            new DefaultConnection($conn)
        );

        $this->assertEquals($metaData, $empty_table->getTableMetaData());
    }

    public function testGetTableMetaData(): void
    {
        $metaData = new DefaultTableMetadata('table1', ['col1', 'col2', 'col3']);

        $this->assertEquals($metaData, $this->table->getTableMetaData());
    }

    public function testGetRowCount(): void
    {
        $this->assertEquals(2, $this->table->getRowCount());
    }

    /**
     * @dataProvider providerTestGetValue
     *
     * @param mixed $row
     * @param mixed $column
     * @param mixed $value
     */
    public function testGetValue($row, $column, $value): void
    {
        $this->assertEquals($value, $this->table->getValue($row, $column));
    }

    public function testGetRow(): void
    {
        $this->assertEquals(['col1' => 'value1', 'col2' => 'value2', 'col3' => 'value3'], $this->table->getRow(0));
    }

    public function testAssertEquals(): void
    {
        $expected_table = new DefaultTable(new DefaultTableMetadata('table1', ['col1', 'col2', 'col3']));
        $expected_table->addRow(['col1' => 'value1', 'col2' => 'value2', 'col3' => 'value3']);
        $expected_table->addRow(['col1' => 'value4', 'col2' => 'value5', 'col3' => 'value6']);
        $this->assertTrue($this->table->matches($expected_table));
    }

    public function testAssertEqualsFails(): void
    {
        $expected_table = new DefaultTable(new DefaultTableMetadata('table1', ['col1', 'col2', 'col3']));
        $expected_table->addRow(['col1' => 'value1', 'col2' => 'value2', 'col3' => 'value3']);
        $expected_table->addRow(['col1' => 'value4', 'col2' => 'value5', 'col3' => 'value6']);
        $expected_table->addRow(['col1' => 'value7', 'col2' => 'value8', 'col3' => 'value9']);
        $this->assertFalse($this->table->matches($expected_table));
    }

    public function testAssertRowContains(): void
    {
        $this->assertTrue($this->table->assertContainsRow(
            ['col1' => 'value1', 'col2' => 'value2', 'col3' => 'value3']
        ));
    }
}
