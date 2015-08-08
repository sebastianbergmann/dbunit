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
class Extensions_Database_DataSet_CsvDataSetTest extends PHPUnit_Framework_TestCase
{
    protected $expectedDataSet;

    public function testCSVDataSet()
    {
        $table1MetaData = new PHPUnit_Extensions_Database_DataSet_DefaultTableMetaData(
            'table1', ['table1_id', 'column1', 'column2', 'column3', 'column4']
        );
        $table2MetaData = new PHPUnit_Extensions_Database_DataSet_DefaultTableMetaData(
            'table2', ['table2_id', 'column5', 'column6', 'column7', 'column8']
        );

        $table1 = new PHPUnit_Extensions_Database_DataSet_DefaultTable($table1MetaData);
        $table2 = new PHPUnit_Extensions_Database_DataSet_DefaultTable($table2MetaData);

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

        $expectedDataSet = new PHPUnit_Extensions_Database_DataSet_DefaultDataSet([$table1, $table2]);

        $csvDataSet = new PHPUnit_Extensions_Database_DataSet_CsvDataSet();
        $csvDataSet->addTable('table1', dirname(__FILE__) . '/../_files/CsvDataSets/table1.csv');
        $csvDataSet->addTable('table2', dirname(__FILE__) . '/../_files/CsvDataSets/table2.csv');

        PHPUnit_Extensions_Database_TestCase::assertDataSetsEqual($expectedDataSet, $csvDataSet);
    }
}
