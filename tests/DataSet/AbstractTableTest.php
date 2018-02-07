<?php
/*
 * This file is part of DbUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use PHPUnit\DbUnit\DataSet\DefaultTable;
use PHPUnit\DbUnit\DataSet\DefaultTableMetadata;
use PHPUnit\DbUnit\DataSet\ITable;
use PHPUnit\DbUnit\DataSet\ITableMetadata;
use PHPUnit\DbUnit\DataSet\QueryTable;
use PHPUnit\Framework\TestCase;

class Extensions_Database_DataSet_AbstractTableTest extends TestCase
{
    /**
     * @var QueryTable
     */
    protected $table;

    public function setUp(): void
    {
        $tableMetaData = new DefaultTableMetadata(
            'table',
            ['id', 'column1']
        );

        $this->table = new DefaultTable($tableMetaData);

        $this->table->addRow([
            'id'      => 1,
            'column1' => 'randomValue'
        ]);
    }

    /**
     * @param array $row
     * @param bool  $exists
     * @dataProvider providerTableContainsRow
     */
    public function testTableContainsRow($row, $exists): void
    {
        $result = $this->table->assertContainsRow($row);
        $this->assertEquals($exists, $result);
    }

    public function providerTableContainsRow()
    {
        return [
            [['id' => 1, 'column1' => 'randomValue'], true],
            [['id' => 1, 'column1' => 'notExistingValue'], false]
        ];
    }

    public function testMatchesWithNonMatchingMetaData(): void
    {
        $tableMetaData = $this->createMock(ITableMetadata::class);
        $otherMetaData = $this->createMock(ITableMetadata::class);

        $otherTable = $this->createMock(ITable::class);
        $otherTable->expects($this->once())
            ->method('getTableMetaData')
            ->will($this->returnValue($otherMetaData));

        $tableMetaData->expects($this->once())
            ->method('matches')
            ->with($otherMetaData)
            ->will($this->returnValue(false));

        $table = new DefaultTable($tableMetaData);
        $this->assertFalse($table->matches($otherTable));
    }

    public function testMatchesWithNonMatchingRowCount(): void
    {
        $tableMetaData = $this->createMock(ITableMetadata::class);
        $otherMetaData = $this->createMock(ITableMetadata::class);
        $otherTable    = $this->createMock(ITable::class);

        $table = $this->getMockBuilder(DefaultTable::class)
                      ->setConstructorArgs([$tableMetaData])
                      ->setMethods(['getRowCount'])
                      ->getMock();

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

        $table->expects($this->once())
            ->method('getRowCount')
            ->will($this->returnValue(1));
        $this->assertFalse($table->matches($otherTable));
    }

    /**
     * @param array $tableColumnValues
     * @param array $otherColumnValues
     * @param bool  $matches
     * @dataProvider providerMatchesWithColumnValueComparisons
     */
    public function testMatchesWithColumnValueComparisons($tableColumnValues, $otherColumnValues, $matches): void
    {
        $tableMetaData = $this->createMock(ITableMetadata::class);
        $otherMetaData = $this->createMock(ITableMetadata::class);
        $otherTable    = $this->createMock(ITable::class);

        $table = $this->getMockBuilder(DefaultTable::class)
                      ->setConstructorArgs([$tableMetaData])
                      ->setMethods(['getRowCount', 'getValue'])
                      ->getMock();

        $otherTable->expects($this->once())
            ->method('getTableMetaData')
            ->will($this->returnValue($otherMetaData));
        $otherTable->expects($this->once())
            ->method('getRowCount')
            ->will($this->returnValue(\count($otherColumnValues)));

        $tableMetaData->expects($this->once())
            ->method('getColumns')
            ->will($this->returnValue(\array_keys(\reset($tableColumnValues))));
        $tableMetaData->expects($this->once())
            ->method('matches')
            ->with($otherMetaData)
            ->will($this->returnValue(true));

        $table->expects($this->any())
            ->method('getRowCount')
            ->will($this->returnValue(\count($tableColumnValues)));

        $tableMap = [];
        $otherMap = [];
        foreach ($tableColumnValues as $rowIndex => $rowData) {
            foreach ($rowData as $columnName => $columnValue) {
                $tableMap[] = [$rowIndex, $columnName, $columnValue];
                $otherMap[] = [$rowIndex, $columnName, $otherColumnValues[$rowIndex][$columnName]];
            }
        }
        $table->expects($this->any())
            ->method('getValue')
            ->will($this->returnValueMap($tableMap));
        $otherTable->expects($this->any())
            ->method('getValue')
            ->will($this->returnValueMap($otherMap));

        $this->assertSame($matches, $table->matches($otherTable));
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
