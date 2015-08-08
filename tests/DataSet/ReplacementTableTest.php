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
class Extensions_Database_DataSet_ReplacementTableTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Extensions_Database_DataSet_DefaultTable
     */
    protected $startingTable;

    public function setUp()
    {
        $tableMetaData = new PHPUnit_Extensions_Database_DataSet_DefaultTableMetaData(
            'table1', ['table1_id', 'column1', 'column2', 'column3', 'column4']
        );

        $table = new PHPUnit_Extensions_Database_DataSet_DefaultTable($tableMetaData);

        $table->addRow([
            'table1_id' => 1,
            'column1'   => 'My name is %%%name%%%',
            'column2'   => 200,
            'column3'   => 34.64,
            'column4'   => 'yghkf;a  hahfg8ja h;'
        ]);
        $table->addRow([
            'table1_id' => 2,
            'column1'   => 'hk;afg',
            'column2'   => 654,
            'column3'   => 46.54,
            'column4'   => '24rwehhads'
        ]);
        $table->addRow([
            'table1_id' => 3,
            'column1'   => 'ha;gyt',
            'column2'   => 462,
            'column3'   => '[NULL] not really',
            'column4'   => '[NULL]'
        ]);

        $this->startingTable = $table;
    }

    public function testNoReplacement()
    {
        PHPUnit_Extensions_Database_TestCase::assertTablesEqual(
            $this->startingTable,
            new PHPUnit_Extensions_Database_DataSet_ReplacementTable($this->startingTable)
        );
    }

    public function testFullReplacement()
    {
        $tableMetaData = new PHPUnit_Extensions_Database_DataSet_DefaultTableMetaData(
            'table1', ['table1_id', 'column1', 'column2', 'column3', 'column4']
        );

        $table = new PHPUnit_Extensions_Database_DataSet_DefaultTable($tableMetaData);

        $table->addRow([
            'table1_id' => 1,
            'column1'   => 'My name is %%%name%%%',
            'column2'   => 200,
            'column3'   => 34.64,
            'column4'   => 'yghkf;a  hahfg8ja h;'
        ]);
        $table->addRow([
            'table1_id' => 2,
            'column1'   => 'hk;afg',
            'column2'   => 654,
            'column3'   => 46.54,
            'column4'   => '24rwehhads'
        ]);
        $table->addRow([
            'table1_id' => 3,
            'column1'   => 'ha;gyt',
            'column2'   => 462,
            'column3'   => '[NULL] not really',
            'column4'   => NULL
        ]);

        $actual = new PHPUnit_Extensions_Database_DataSet_ReplacementTable($this->startingTable);
        $actual->addFullReplacement('[NULL]', NULL);

        PHPUnit_Extensions_Database_TestCase::assertTablesEqual($table, $actual);
    }

    public function testSubStrReplacement()
    {
        $tableMetaData = new PHPUnit_Extensions_Database_DataSet_DefaultTableMetaData(
            'table1', ['table1_id', 'column1', 'column2', 'column3', 'column4']
        );

        $table = new PHPUnit_Extensions_Database_DataSet_DefaultTable($tableMetaData);

        $table->addRow([
            'table1_id' => 1,
            'column1'   => 'My name is Mike Lively',
            'column2'   => 200,
            'column3'   => 34.64,
            'column4'   => 'yghkf;a  hahfg8ja h;'
        ]);
        $table->addRow([
            'table1_id' => 2,
            'column1'   => 'hk;afg',
            'column2'   => 654,
            'column3'   => 46.54,
            'column4'   => '24rwehhads'
        ]);
        $table->addRow([
            'table1_id' => 3,
            'column1'   => 'ha;gyt',
            'column2'   => 462,
            'column3'   => '[NULL] not really',
            'column4'   => '[NULL]'
        ]);

        $actual = new PHPUnit_Extensions_Database_DataSet_ReplacementTable($this->startingTable);
        $actual->addSubStrReplacement('%%%name%%%', 'Mike Lively');

        PHPUnit_Extensions_Database_TestCase::assertTablesEqual($table, $actual);
    }

    public function testConstructorReplacements()
    {
        $tableMetaData = new PHPUnit_Extensions_Database_DataSet_DefaultTableMetaData(
            'table1', ['table1_id', 'column1', 'column2', 'column3', 'column4']
        );

        $table = new PHPUnit_Extensions_Database_DataSet_DefaultTable($tableMetaData);

        $table->addRow([
            'table1_id' => 1,
            'column1'   => 'My name is Mike Lively',
            'column2'   => 200,
            'column3'   => 34.64,
            'column4'   => 'yghkf;a  hahfg8ja h;'
        ]);
        $table->addRow([
            'table1_id' => 2,
            'column1'   => 'hk;afg',
            'column2'   => 654,
            'column3'   => 46.54,
            'column4'   => '24rwehhads'
        ]);
        $table->addRow([
            'table1_id' => 3,
            'column1'   => 'ha;gyt',
            'column2'   => 462,
            'column3'   => '[NULL] not really',
            'column4'   => NULL
        ]);

        $actual = new PHPUnit_Extensions_Database_DataSet_ReplacementTable(
            $this->startingTable,
            ['[NULL]'     => NULL],
            ['%%%name%%%' => 'Mike Lively']
        );

        PHPUnit_Extensions_Database_TestCase::assertTablesEqual($table, $actual);
    }

    public function testGetRow()
    {
        $actual = new PHPUnit_Extensions_Database_DataSet_ReplacementTable(
            $this->startingTable,
            ['[NULL]'     => NULL],
            ['%%%name%%%' => 'Mike Lively']
        );

        $this->assertEquals(
            [
                'table1_id' => 1,
                'column1'   => 'My name is Mike Lively',
                'column2'   => 200,
                'column3'   => 34.64,
                'column4'   => 'yghkf;a  hahfg8ja h;'
            ],
            $actual->getRow(0)
        );

        $this->assertEquals(
            [
                'table1_id' => 3,
                'column1'   => 'ha;gyt',
                'column2'   => 462,
                'column3'   => '[NULL] not really',
                'column4'   => NULL
            ],
            $actual->getRow(2)
        );
    }

    public function testGetValue()
    {
        $actual = new PHPUnit_Extensions_Database_DataSet_ReplacementTable(
            $this->startingTable,
            ['[NULL]'     => NULL],
            ['%%%name%%%' => 'Mike Lively']
        );

        $this->assertNull($actual->getValue(2, 'column4'));
        $this->assertEquals('My name is Mike Lively', $actual->getValue(0, 'column1'));
    }

    public function testMatchesWithNonMatchingMetaData()
    {
        $tableMetaData = $this->getMock('PHPUnit_Extensions_Database_DataSet_ITableMetaData');
        $otherMetaData = $this->getMock('PHPUnit_Extensions_Database_DataSet_ITableMetaData');

        $table = $this->getMock('PHPUnit_Extensions_Database_DataSet_ITable');
        $table->expects($this->once())
            ->method('getTableMetaData')
            ->will($this->returnValue($tableMetaData));

        $otherTable = $this->getMock('PHPUnit_Extensions_Database_DataSet_ITable');
        $otherTable->expects($this->once())
            ->method('getTableMetaData')
            ->will($this->returnValue($otherMetaData));

        $tableMetaData->expects($this->once())
            ->method('matches')
            ->with($otherMetaData)
            ->will($this->returnValue(false));

        $replacementTable = new PHPUnit_Extensions_Database_DataSet_ReplacementTable($table);
        $this->assertFalse($replacementTable->matches($otherTable));
    }

    public function testMatchesWithNonMatchingRowCount()
    {
        $tableMetaData = $this->getMock('PHPUnit_Extensions_Database_DataSet_ITableMetaData');
        $otherMetaData = $this->getMock('PHPUnit_Extensions_Database_DataSet_ITableMetaData');

        $table = $this->getMock('PHPUnit_Extensions_Database_DataSet_ITable');
        $table->expects($this->once())
            ->method('getTableMetaData')
            ->will($this->returnValue($tableMetaData));

        $otherTable = $this->getMock('PHPUnit_Extensions_Database_DataSet_ITable');
        $otherTable->expects($this->once())
            ->method('getTableMetaData')
            ->will($this->returnValue($otherMetaData));
        $otherTable->expects($this->once())
            ->method('getRowCount')
            ->will($this->returnValue(0));

        $tableMetaData->expects($this->once())
            ->method('matches')
            ->with($otherMetaData)
            ->will($this->returnValue(true));

        $replacementTable = $this->getMock('PHPUnit_Extensions_Database_DataSet_ReplacementTable', ['getRowCount'], [$table]);
        $replacementTable->expects($this->once())
            ->method('getRowCount')
            ->will($this->returnValue(1));
        $this->assertFalse($replacementTable->matches($otherTable));
    }

    /**
     * @param array $tableColumnValues
     * @param array $otherColumnValues
     * @param bool  $matches
     * @dataProvider providerMatchesWithColumnValueComparisons
     */
    public function testMatchesWithColumnValueComparisons($tableColumnValues, $otherColumnValues, $matches)
    {
        $tableMetaData = $this->getMock('PHPUnit_Extensions_Database_DataSet_ITableMetaData');
        $otherMetaData = $this->getMock('PHPUnit_Extensions_Database_DataSet_ITableMetaData');

        $table = $this->getMock('PHPUnit_Extensions_Database_DataSet_ITable');
        $table->expects($this->once())
            ->method('getTableMetaData')
            ->will($this->returnValue($tableMetaData));

        $otherTable = $this->getMock('PHPUnit_Extensions_Database_DataSet_ITable');
        $otherTable->expects($this->once())
            ->method('getTableMetaData')
            ->will($this->returnValue($otherMetaData));
        $otherTable->expects($this->once())
            ->method('getRowCount')
            ->will($this->returnValue(count($otherColumnValues)));

        $tableMetaData->expects($this->once())
            ->method('getColumns')
            ->will($this->returnValue(array_keys(reset($tableColumnValues))));
        $tableMetaData->expects($this->once())
            ->method('matches')
            ->with($otherMetaData)
            ->will($this->returnValue(true));

        $replacementTable = $this->getMock('PHPUnit_Extensions_Database_DataSet_ReplacementTable', ['getRowCount', 'getValue'], [$table]);
        $replacementTable->expects($this->any())
            ->method('getRowCount')
            ->will($this->returnValue(count($tableColumnValues)));

        $tableMap = [];
        $otherMap = [];
        foreach ($tableColumnValues as $rowIndex => $rowData) {
            foreach ($rowData as $columnName => $columnValue) {
                $tableMap[] = [$rowIndex, $columnName, $columnValue];
                $otherMap[] = [$rowIndex, $columnName, $otherColumnValues[$rowIndex][$columnName]];
            }
        }
        $replacementTable->expects($this->any())
            ->method('getValue')
            ->will($this->returnValueMap($tableMap));
        $otherTable->expects($this->any())
            ->method('getValue')
            ->will($this->returnValueMap($otherMap));

        $this->assertSame($matches, $replacementTable->matches($otherTable));
    }

    public function providerMatchesWithColumnValueComparisons()
    {
        return [

            // One row, one column, matches
            [
                [
                    ['id' => 1],
                ],
                [
                    ['id' => 1],
                ],
                true,
            ],

            // One row, one column, does not match
            [
                [
                    ['id' => 1],
                ],
                [
                    ['id' => 2],
                ],
                false,
            ],

            // Multiple rows, one column, matches
            [
                [
                    ['id' => 1],
                    ['id' => 2],
                ],
                [
                    ['id' => 1],
                    ['id' => 2],
                ],
                true,
            ],

            // Multiple rows, one column, do not match
            [
                [
                    ['id' => 1],
                    ['id' => 2],
                ],
                [
                    ['id' => 1],
                    ['id' => 3],
                ],
                false,
            ],

            // Multiple rows, multiple columns, matches
            [
                [
                    ['id' => 1, 'name' => 'foo'],
                    ['id' => 2, 'name' => 'bar'],
                ],
                [
                    ['id' => 1, 'name' => 'foo'],
                    ['id' => 2, 'name' => 'bar'],
                ],
                true,
            ],

            // Multiple rows, multiple columns, do not match
            [
                [
                    ['id' => 1, 'name' => 'foo'],
                    ['id' => 2, 'name' => 'bar'],
                ],
                [
                    ['id' => 1, 'name' => 'foo'],
                    ['id' => 2, 'name' => 'baz'],
                ],
                false,
            ],

            // Int and int as string must match
            [
                [
                    ['id' => 42],
                ],
                [
                    ['id' => '42'],
                ],
                true,
            ],

            // Float and float as string must match
            [
                [
                    ['id' => 15.3],
                ],
                [
                    ['id' => '15.3'],
                ],
                true,
            ],

            // Int and float must match
            [
                [
                    ['id' => 18.00],
                ],
                [
                    ['id' => 18],
                ],
                true,
            ],

            // 0 and empty string must not match
            [
                [
                    ['id' => 0],
                ],
                [
                    ['id' => ''],
                ],
                false,
            ],

            // 0 and null must not match
            [
                [
                    ['id' => 0],
                ],
                [
                    ['id' => null],
                ],
                false,
            ],

            // empty string and null must not match
            [
                [
                    ['id' => ''],
                ],
                [
                    ['id' => null],
                ],
                false,
            ],
        ];
    }

}
