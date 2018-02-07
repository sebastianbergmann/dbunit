<?php
/*
 * This file is part of DbUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use PHPUnit\DbUnit\DataSet\DefaultDataSet;
use PHPUnit\DbUnit\DataSet\DefaultTable;
use PHPUnit\DbUnit\DataSet\DefaultTableMetadata;
use PHPUnit\DbUnit\DataSet\ReplacementDataSet;
use PHPUnit\DbUnit\TestCase;

class Extensions_Database_DataSet_ReplacementDataSetTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var DefaultDataSet
     */
    protected $startingDataSet;

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
            'column1'   => 'My name is %%%name%%%',
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
            'column4'   => '[NULL]'
        ]);

        $table2->addRow([
            'table2_id' => 1,
            'column5'   => 'fhah',
            'column6'   => 456,
            'column7'   => 46.5,
            'column8'   => 'My name is %%%name%%%'
        ]);
        $table2->addRow([
            'table2_id' => 2,
            'column5'   => 'asdhfoih',
            'column6'   => 654,
            'column7'   => '[NULL]',
            'column8'   => '43asdfhgj'
        ]);
        $table2->addRow([
            'table2_id' => 3,
            'column5'   => 'ajsdlkfguitah',
            'column6'   => 654,
            'column7'   => '[NULL]',
            'column8'   => '[NULL] not really'
        ]);

        $this->startingDataSet = new DefaultDataSet([$table1, $table2]);
    }

    public function testNoReplacement(): void
    {
        TestCase::assertDataSetsEqual(
            $this->startingDataSet,
            new ReplacementDataSet($this->startingDataSet)
        );
    }

    public function testFullReplacement(): void
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
            'column1'   => 'My name is %%%name%%%',
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
            'column4'   => null
        ]);

        $table2->addRow([
            'table2_id' => 1,
            'column5'   => 'fhah',
            'column6'   => 456,
            'column7'   => 46.5,
            'column8'   => 'My name is %%%name%%%'
        ]);
        $table2->addRow([
            'table2_id' => 2,
            'column5'   => 'asdhfoih',
            'column6'   => 654,
            'column7'   => null,
            'column8'   => '43asdfhgj'
        ]);
        $table2->addRow([
            'table2_id' => 3,
            'column5'   => 'ajsdlkfguitah',
            'column6'   => 654,
            'column7'   => null,
            'column8'   => '[NULL] not really'
        ]);

        $expected = new DefaultDataSet([$table1, $table2]);
        $actual   = new ReplacementDataSet($this->startingDataSet);
        $actual->addFullReplacement('[NULL]', null);

        TestCase::assertDataSetsEqual($expected, $actual);
    }

    public function testSubStrReplacement(): void
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
            'column1'   => 'My name is Mike Lively',
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
            'column4'   => '[NULL]'
        ]);

        $table2->addRow([
            'table2_id' => 1,
            'column5'   => 'fhah',
            'column6'   => 456,
            'column7'   => 46.5,
            'column8'   => 'My name is Mike Lively'
        ]);
        $table2->addRow([
            'table2_id' => 2,
            'column5'   => 'asdhfoih',
            'column6'   => 654,
            'column7'   => '[NULL]',
            'column8'   => '43asdfhgj'
        ]);
        $table2->addRow([
            'table2_id' => 3,
            'column5'   => 'ajsdlkfguitah',
            'column6'   => 654,
            'column7'   => '[NULL]',
            'column8'   => '[NULL] not really'
        ]);

        $expected = new DefaultDataSet([$table1, $table2]);
        $actual   = new ReplacementDataSet($this->startingDataSet);
        $actual->addSubStrReplacement('%%%name%%%', 'Mike Lively');

        TestCase::assertDataSetsEqual($expected, $actual);
    }

    public function testConstructorReplacements(): void
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
            'column1'   => 'My name is Mike Lively',
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
            'column4'   => null
        ]);

        $table2->addRow([
            'table2_id' => 1,
            'column5'   => 'fhah',
            'column6'   => 456,
            'column7'   => 46.5,
            'column8'   => 'My name is Mike Lively'
        ]);
        $table2->addRow([
            'table2_id' => 2,
            'column5'   => 'asdhfoih',
            'column6'   => 654,
            'column7'   => null,
            'column8'   => '43asdfhgj'
        ]);
        $table2->addRow([
            'table2_id' => 3,
            'column5'   => 'ajsdlkfguitah',
            'column6'   => 654,
            'column7'   => null,
            'column8'   => '[NULL] not really'
        ]);

        $expected = new DefaultDataSet([$table1, $table2]);
        $actual   = new ReplacementDataSet(
            $this->startingDataSet,
            ['[NULL]'     => null],
            ['%%%name%%%' => 'Mike Lively']
        );

        TestCase::assertDataSetsEqual($expected, $actual);
    }
}
