<?php
/*
 * This file is part of DbUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use PHPUnit\DbUnit\DataSet\CompositeDataSet;
use PHPUnit\DbUnit\DataSet\DefaultDataSet;
use PHPUnit\DbUnit\DataSet\DefaultTable;
use PHPUnit\DbUnit\DataSet\DefaultTableMetadata;
use PHPUnit\DbUnit\TestCase;

class Extensions_Database_DataSet_CompositeDataSetTest extends \PHPUnit\Framework\TestCase
{
    protected $expectedDataSet1;
    protected $expectedDataSet2;
    protected $expectedDataSet3;

    public function setUp()
    {
        $table1MetaData = new DefaultTableMetadata(
            'table1', ['table1_id', 'column1', 'column2', 'column3', 'column4']
        );
        $table2MetaData = new DefaultTableMetadata(
            'table2', ['table2_id', 'column5', 'column6', 'column7', 'column8']
        );

        $table3MetaData = new DefaultTableMetadata(
            'table3', ['table3_id', 'column9', 'column10', 'column11', 'column12']
        );

        $table1 = new DefaultTable($table1MetaData);
        $table2 = new DefaultTable($table2MetaData);
        $table3 = new DefaultTable($table3MetaData);

        $table1->addRow([
            'table1_id' => 1,
            'column1'   => 'tgfahgasdf',
            'column2'   => 200,
            'column3'   => 34.64,
            'column4'   => 'yghkf;a  hahfg8ja h;'
        ]);
        $table1->addRow([
            'table1_id' => 2,
            'column1'   => 'hk;afg',
            'column2'   => 654,
            'column3'   => 46.54,
            'column4'   => '24rwehhads'
        ]);
        $table1->addRow([
            'table1_id' => 3,
            'column1'   => 'ha;gyt',
            'column2'   => 462,
            'column3'   => 1654.4,
            'column4'   => 'asfgklg'
        ]);

        $table2->addRow([
            'table2_id' => 1,
            'column5'   => 'fhah',
            'column6'   => 456,
            'column7'   => 46.5,
            'column8'   => 'fsdb, ghfdas'
        ]);
        $table2->addRow([
            'table2_id' => 2,
            'column5'   => 'asdhfoih',
            'column6'   => 654,
            'column7'   => 'blah',
            'column8'   => '43asd "fhgj" sfadh'
        ]);
        $table2->addRow([
            'table2_id' => 3,
            'column5'   => 'ajsdlkfguitah',
            'column6'   => 654,
            'column7'   => 'blah',
            'column8'   => 'thesethasdl
asdflkjsadf asdfsadfhl "adsf, halsdf" sadfhlasdf'
        ]);

        $table3->addRow([
            'table3_id' => 1,
            'column9'   => 'sfgsda',
            'column10'  => 16,
            'column11'  => 45.57,
            'column12'  => 'sdfh .ds,ajfas asdf h'
        ]);
        $table3->addRow([
            'table3_id' => 2,
            'column9'   => 'afdstgb',
            'column10'  => 41,
            'column11'  => 46.645,
            'column12'  => '87yhasdf sadf yah;/a '
        ]);
        $table3->addRow([
            'table3_id' => 3,
            'column9'   => 'gldsf',
            'column10'  => 46,
            'column11'  => 123.456,
            'column12'  => '0y8hosnd a/df7y olgbjs da'
        ]);

        $this->expectedDataSet1 = new DefaultDataSet([$table1, $table2]);
        $this->expectedDataSet2 = new DefaultDataSet([$table3]);
        $this->expectedDataSet3 = new DefaultDataSet([$table1, $table2, $table3]);
    }

    public function testCompositeDataSet()
    {
        $actual = new CompositeDataSet([$this->expectedDataSet1, $this->expectedDataSet2]);

        TestCase::assertDataSetsEqual($this->expectedDataSet3, $actual);
    }

    public function testCompatibleTablesInDifferentDataSetsNonDuplicateRows()
    {
        $compatibleTable = new DefaultTable(
            $this->expectedDataSet3->getTable('table3')->getTableMetaData()
        );

        $compatibleTable->addRow([
            'table3_id' => 4,
            'column9'   => 'asdasd',
            'column10'  => 17,
            'column11'  => 42.57,
            'column12'  => 'askldja'
        ]);

        $compositeDataSet = new CompositeDataSet([
            new DefaultDataSet([$compatibleTable]),
            $this->expectedDataSet2
        ]);

        $this->assertEquals(4, $compositeDataSet->getTable('table3')->getRowCount());
    }

    /**
     * @expectedException           InvalidArgumentException
     * @expectedExceptionMessage    There is already a table named table3 with different table definition
     */
    public function testExceptionOnIncompatibleTablesSameTableNames()
    {
        $inCompatibleTableMetaData = new DefaultTableMetadata(
            'table3', ['table3_id', 'column13', 'column14', 'column15', 'column16']
        );

        $inCompatibleTable = new DefaultTable($inCompatibleTableMetaData);
        $inCompatibleTable->addRow([
            'column13' => 'asdasda asdasd',
            'column14' => 'aiafsjas asd',
            'column15' => 'asdasdasd',
            'column16' => 2141
        ]);

        $compositeDataSet = new CompositeDataSet([
            $this->expectedDataSet2,
            new DefaultDataSet([$inCompatibleTable])
        ]);
    }

    /**
     * @expectedException           InvalidArgumentException
     * @expectedExceptionMessage    There is already a table named table3 with different table definition
     */
    public function testExceptionOnIncompatibleTablesSameTableNames2()
    {
        $inCompatibleTableMetaData = new DefaultTableMetadata(
            'table3', ['table3_id', 'column13', 'column14', 'column15', 'column16']
        );

        $inCompatibleTable = new DefaultTable($inCompatibleTableMetaData);
        $inCompatibleTable->addRow([
            'column13' => 'asdasda asdasd',
            'column14' => 'aiafsjas asd',
            'column15' => 'asdasdasd',
            'column16' => 2141
        ]);

        $compositeDataSet = new CompositeDataSet([
            new DefaultDataSet([$inCompatibleTable]),
            $this->expectedDataSet2
        ]);
    }
}
