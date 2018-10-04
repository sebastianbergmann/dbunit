<?php

use PHPUnit\DbUnit\Constraint\DataSetIsEqual;
use PHPUnit\DbUnit\DataSet\ArrayDataSet;
use PHPUnit\DbUnit\DataSet\DefaultDataSet;
use PHPUnit\DbUnit\DataSet\DefaultTable;
use PHPUnit\DbUnit\DataSet\DefaultTableMetadata;
use PHPUnit\DbUnit\InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class Extensions_Database_DataSet_ArrayDataSetTest extends TestCase
{

    protected $expectedDataSet;

    public function setUp(): void
    {
        $table1MetaData = new DefaultTableMetadata(
            'table1',
            ['table1_id', 'column1', 'column2', 'column3', 'column4']
        );
        $table2MetaData = new DefaultTableMetadata(
            'table2',
            ['table2_id', 'column5', 'column6', 'column7', 'column8']
        );

        $table1 = new DefaultTable($table1MetaData);
        $table2 = new DefaultTable($table2MetaData);

        $table1->addRow([
            'table1_id' => 1,
            'column1' => 'tgfahgasdf',
            'column2' => 200,
            'column3' => 34.64,
            'column4' => 'yghkf;a  hahfg8ja h;'
        ]);
        $table1->addRow([
            'table1_id' => 2,
            'column1' => 'hk;afg',
            'column2' => 654,
            'column3' => 46.54,
            'column4' => '24rwehhads'
        ]);
        $table1->addRow([
            'table1_id' => 3,
            'column1' => 'ha;gyt',
            'column2' => 462,
            'column3' => 1654.4,
            'column4' => 'asfgklg'
        ]);

        $table2->addRow([
            'table2_id' => 1,
            'column5' => 'fhah',
            'column6' => 456,
            'column7' => 46.5,
            'column8' => 'fsdbghfdas'
        ]);
        $table2->addRow([
            'table2_id' => 2,
            'column5' => 'asdhfoih',
            'column6' => 654,
            'column7' => null,
            'column8' => '43asdfhgj'
        ]);
        $table2->addRow([
            'table2_id' => 3,
            'column5' => 'ajsdlkfguitah',
            'column6' => 654,
            'column7' => null,
            'column8' => null
        ]);

        $this->expectedDataSet = new DefaultDataSet([$table1, $table2]);
    }

    public function testNumericArrayDataSet(): void
    {
        $constraint = new DataSetIsEqual($this->expectedDataSet);
        $numericArrayDataSet = new ArrayDataSet($this->getArrayData());

        self::assertThat($numericArrayDataSet, $constraint);
    }

    public function testAssociativeArrayDataSet(): void
    {
        $constraint = new DataSetIsEqual($this->expectedDataSet);
        $associativeArrayDataSet = new ArrayDataSet($this->getArrayData([['one', 'two', 'three'], ['four', 'five', 'six']]));

        self::assertThat($associativeArrayDataSet, $constraint);
    }

    public function testNonZeroIndexArrayDataSet(): void
    {
        $constraint = new DataSetIsEqual($this->expectedDataSet);
        $nonZeroArrayDataSet = new ArrayDataSet($this->getArrayData([[1, 2, 3], [0, 1, 2]]));

        self::assertThat($nonZeroArrayDataSet, $constraint);
    }
    
    public function testExtraColumns(): void
    {
        $rows = $this->getArrayData();
        $rows['table1'][1]['extra_column'] = 0;

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Unexpected column 'extra_column' for table1");

        new ArrayDataSet($rows);
    }

    public function testUnexpectedColumn(): void
    {
        $rows = $this->getArrayData();
        unset($rows['table2'][0]['column2']);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Unexpected column 'column2' for table1");

        new ArrayDataSet($rows);
    }

    private function getArrayData(array $rowKeys = [[0, 1, 2], [0, 1, 2]]): array
    {
        return [
            'table1' => [
                $rowKeys[0][0] => [
                    'table1_id' => 1,
                    'column1' => 'tgfahgasdf',
                    'column2' => 200,
                    'column3' => 34.64,
                    'column4' => 'yghkf;a  hahfg8ja h;'
                ],
                $rowKeys[0][1] => [
                    'table1_id' => 2,
                    'column1' => 'hk;afg',
                    'column2' => 654,
                    'column3' => 46.54,
                    'column4' => '24rwehhads'
                ],
                $rowKeys[0][2] => [
                    'table1_id' => 3,
                    'column1' => 'ha;gyt',
                    'column2' => 462,
                    'column3' => 1654.4,
                    'column4' => 'asfgklg'
                ],
            ],
            'table2' => [
                $rowKeys[1][0] => ['table2_id' => 1,
                    'column5' => 'fhah',
                    'column6' => 456,
                    'column7' => 46.5,
                    'column8' => 'fsdbghfdas'
                ],
                $rowKeys[1][1] => [
                    'table2_id' => 2,
                    'column5' => 'asdhfoih',
                    'column6' => 654,
                    'column7' => null,
                    'column8' => '43asdfhgj'
                ],
                $rowKeys[1][2] => [
                    'table2_id' => 3,
                    'column5' => 'ajsdlkfguitah',
                    'column6' => 654,
                    'column7' => null,
                    'column8' => null
                ],
            ],
        ];
    }

}