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
 * @since      File available since Release 1.0.0
 */
class Extensions_Database_DataSet_QueryTableTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Extensions_Database_DataSet_QueryTable
     */
    protected $table;

    public function setUp()
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
        $this->table = new PHPUnit_Extensions_Database_DataSet_QueryTable(
            'table1',
            $query,
            new PHPUnit_Extensions_Database_DB_DefaultDatabaseConnection(new PDO('sqlite::memory:'), 'test')
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

    public function testGetTableMetaData()
    {
        $metaData = new PHPUnit_Extensions_Database_DataSet_DefaultTableMetaData('table1', ['col1', 'col2', 'col3']);

        $this->assertEquals($metaData, $this->table->getTableMetaData());
    }

    public function testGetRowCount()
    {
        $this->assertEquals(2, $this->table->getRowCount());
    }

    /**
     * @dataProvider providerTestGetValue
     */
    public function testGetValue($row, $column, $value)
    {
        $this->assertEquals($value, $this->table->getValue($row, $column));
    }

    public function testGetRow()
    {
        $this->assertEquals(['col1' => 'value1', 'col2' => 'value2', 'col3' => 'value3'], $this->table->getRow(0));
    }

    public function testAssertEquals()
    {
        $expected_table = new PHPUnit_Extensions_Database_DataSet_DefaultTable(new PHPUnit_Extensions_Database_DataSet_DefaultTableMetaData('table1', ['col1', 'col2', 'col3']));
        $expected_table->addRow(['col1' => 'value1', 'col2' => 'value2', 'col3' => 'value3']);
        $expected_table->addRow(['col1' => 'value4', 'col2' => 'value5', 'col3' => 'value6']);
        $this->assertTrue($this->table->matches($expected_table));
    }

    public function testAssertEqualsFails()
    {
        $expected_table = new PHPUnit_Extensions_Database_DataSet_DefaultTable(new PHPUnit_Extensions_Database_DataSet_DefaultTableMetaData('table1', ['col1', 'col2', 'col3']));
        $expected_table->addRow(['col1' => 'value1', 'col2' => 'value2', 'col3' => 'value3']);
        $expected_table->addRow(['col1' => 'value4', 'col2' => 'value5', 'col3' => 'value6']);
        $expected_table->addRow(['col1' => 'value7', 'col2' => 'value8', 'col3' => 'value9']);
        $this->assertFalse($this->table->matches($expected_table));
    }

    public function testAssertRowContains()
    {
        $this->assertTrue($this->table->assertContainsRow(
            ['col1' => 'value1', 'col2' => 'value2', 'col3' => 'value3']
        ));
    }
}
