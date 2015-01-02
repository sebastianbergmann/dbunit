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
 * @package    DbUnit
 * @author     Mike Lively <m@digitalsandwich.com>
 * @copyright  Sebastian Bergmann <sebastian@phpunit.de>
 * @license    http://www.opensource.org/licenses/BSD-3-Clause  The BSD 3-Clause License
 * @link       http://www.phpunit.de/
 * @since      File available since Release 1.0.0
 */
class Extensions_Database_DataSet_FilterTest extends PHPUnit_Framework_TestCase
{
    protected $expectedDataSet;

    public function setUp()
    {
        $this->expectedDataSet = new PHPUnit_Extensions_Database_DataSet_FlatXmlDataSet(
            dirname(__FILE__).'/../_files/XmlDataSets/FilteredTestFixture.xml'
        );
    }

    public function testDeprecatedFilteredDataSetConstructor()
    {
        $constraint = new PHPUnit_Extensions_Database_Constraint_DataSetIsEqual($this->expectedDataSet);
        $dataSet = new PHPUnit_Extensions_Database_DataSet_FlatXmlDataSet(
            dirname(__FILE__).'/../_files/XmlDataSets/FilteredTestComparison.xml'
        );

        $filteredDataSet = new PHPUnit_Extensions_Database_DataSet_DataSetFilter($dataSet, array(
            'table1' => array('table1_id'),
            'table2' => '*',
            'table3' => 'table3_id'
        ));

        self::assertThat($filteredDataSet, $constraint);
    }

    public function testExcludeFilteredDataSet()
    {
        $constraint = new PHPUnit_Extensions_Database_Constraint_DataSetIsEqual($this->expectedDataSet);
        $dataSet = new PHPUnit_Extensions_Database_DataSet_FlatXmlDataSet(
            dirname(__FILE__).'/../_files/XmlDataSets/FilteredTestComparison.xml'
        );

        $filteredDataSet = new PHPUnit_Extensions_Database_DataSet_DataSetFilter($dataSet);

        $filteredDataSet->addExcludeTables(array('table2'));
        $filteredDataSet->setExcludeColumnsForTable('table1', array('table1_id'));
        $filteredDataSet->setExcludeColumnsForTable('table3', array('table3_id'));

        self::assertThat($filteredDataSet, $constraint);
    }

    public function testIncludeFilteredDataSet()
    {
        $constraint = new PHPUnit_Extensions_Database_Constraint_DataSetIsEqual($this->expectedDataSet);
        $dataSet = new PHPUnit_Extensions_Database_DataSet_FlatXmlDataSet(
            dirname(__FILE__).'/../_files/XmlDataSets/FilteredTestComparison.xml'
        );

        $filteredDataSet = new PHPUnit_Extensions_Database_DataSet_DataSetFilter($dataSet);

        $filteredDataSet->addIncludeTables(array('table1', 'table3'));
        $filteredDataSet->setIncludeColumnsForTable('table1', array('column1', 'column2', 'column3', 'column4'));
        $filteredDataSet->setIncludeColumnsForTable('table3', array('column9', 'column10', 'column11', 'column12'));

        self::assertThat($filteredDataSet, $constraint);
    }

    public function testIncludeExcludeMixedDataSet()
    {
        $constraint = new PHPUnit_Extensions_Database_Constraint_DataSetIsEqual($this->expectedDataSet);
        $dataSet = new PHPUnit_Extensions_Database_DataSet_FlatXmlDataSet(
            dirname(__FILE__).'/../_files/XmlDataSets/FilteredTestComparison.xml'
        );

        $filteredDataSet = new PHPUnit_Extensions_Database_DataSet_DataSetFilter($dataSet);

        $filteredDataSet->addIncludeTables(array('table1', 'table3'));
        $filteredDataSet->setExcludeColumnsForTable('table1', array('table1_id'));
        $filteredDataSet->setIncludeColumnsForTable('table3', array('column9', 'column10', 'column11', 'column12'));

        self::assertThat($filteredDataSet, $constraint);
    }
}
