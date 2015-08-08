<?php
/*
 * This file is part of DBUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'DatabaseTestUtility.php';

/**
 * @version    SVN: $Id$
 * @since      File available since Release 1.0.0
 */
class Extensions_Database_Operation_OperationsMySQLTest extends PHPUnit_Extensions_Database_TestCase
{
    protected function setUp()
    {
        if (!extension_loaded('pdo_mysql')) {
            $this->markTestSkipped('pdo_mysql is required to run this test.');
        }

        if (!defined('PHPUNIT_TESTSUITE_EXTENSION_DATABASE_MYSQL_DSN')) {
            $this->markTestSkipped('No MySQL server configured for this test.');
        }

        parent::setUp();
    }

    public function getConnection()
    {
        return new PHPUnit_Extensions_Database_DB_DefaultDatabaseConnection(DBUnitTestUtility::getMySQLDB(), 'mysql');
    }

    public function getDataSet()
    {
        return new PHPUnit_Extensions_Database_DataSet_FlatXmlDataSet(dirname(__FILE__) . '/../_files/XmlDataSets/OperationsMySQLTestFixture.xml');
    }

    /**
     * @covers PHPUnit_Extensions_Database_Operation_Truncate::execute
     */
    public function testTruncate()
    {
        $truncateOperation = new PHPUnit_Extensions_Database_Operation_Truncate();
        $truncateOperation->execute($this->getConnection(), $this->getDataSet());

        $expectedDataSet = new PHPUnit_Extensions_Database_DataSet_DefaultDataSet([
            new PHPUnit_Extensions_Database_DataSet_DefaultTable(
                new PHPUnit_Extensions_Database_DataSet_DefaultTableMetaData('table1',
                    ['table1_id', 'column1', 'column2', 'column3', 'column4'])
            ),
            new PHPUnit_Extensions_Database_DataSet_DefaultTable(
                new PHPUnit_Extensions_Database_DataSet_DefaultTableMetaData('table2',
                    ['table2_id', 'table1_id', 'column5', 'column6', 'column7', 'column8'])
            ),
            new PHPUnit_Extensions_Database_DataSet_DefaultTable(
                new PHPUnit_Extensions_Database_DataSet_DefaultTableMetaData('table3',
                    ['table3_id', 'table2_id', 'column9', 'column10', 'column11', 'column12'])
            ),
        ]);

        $this->assertDataSetsEqual($expectedDataSet, $this->getConnection()->createDataSet());
    }

    public function getCompositeDataSet()
    {
        $compositeDataset = new PHPUnit_Extensions_Database_DataSet_CompositeDataSet();

        $dataset = $this->createXMLDataSet(dirname(__FILE__) . '/../_files/XmlDataSets/TruncateCompositeTest.xml');
        $compositeDataset->addDataSet($dataset);

        return $compositeDataset;
    }

    public function testTruncateComposite()
    {
        $truncateOperation = new PHPUnit_Extensions_Database_Operation_Truncate();
        $truncateOperation->execute($this->getConnection(), $this->getCompositeDataSet());

        $expectedDataSet = new PHPUnit_Extensions_Database_DataSet_DefaultDataSet([
            new PHPUnit_Extensions_Database_DataSet_DefaultTable(
                new PHPUnit_Extensions_Database_DataSet_DefaultTableMetaData('table1',
                    ['table1_id', 'column1', 'column2', 'column3', 'column4'])
            ),
            new PHPUnit_Extensions_Database_DataSet_DefaultTable(
                new PHPUnit_Extensions_Database_DataSet_DefaultTableMetaData('table2',
                    ['table2_id', 'table1_id', 'column5', 'column6', 'column7', 'column8'])
            ),
            new PHPUnit_Extensions_Database_DataSet_DefaultTable(
                new PHPUnit_Extensions_Database_DataSet_DefaultTableMetaData('table3',
                    ['table3_id', 'table2_id', 'column9', 'column10', 'column11', 'column12'])
            ),
        ]);

        $this->assertDataSetsEqual($expectedDataSet, $this->getConnection()->createDataSet());
    }
}
