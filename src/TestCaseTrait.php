<?php
/*
 * This file is part of DBUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPUnit\DbUnit;

use PDO;
use PHPUnit\DbUnit\Constraint\DataSetIsEqual;
use PHPUnit\DbUnit\Constraint\TableIsEqual;
use PHPUnit\DbUnit\Constraint\TableRowCount;
use PHPUnit\DbUnit\DataSet\ArrayDataSet;
use PHPUnit\DbUnit\DataSet\FlatXmlDataSet;
use PHPUnit\DbUnit\DataSet\IDataSet;
use PHPUnit_Extensions_Database_DataSet_ITable;
use PHPUnit_Extensions_Database_DataSet_MysqlXmlDataSet;
use PHPUnit_Extensions_Database_DataSet_XmlDataSet;
use PHPUnit\DbUnit\Database\DefaultConnection;
use PHPUnit\DbUnit\Database\IConnection;
use PHPUnit_Extensions_Database_Operation_Factory;
use PHPUnit_Extensions_Database_Operation_IDatabaseOperation;

trait TestCaseTrait
{
    /**
     * @var ITester
     */
    protected $databaseTester;

    /**
     * Closes the specified connection.
     *
     * @param IConnection $connection
     */
    protected function closeConnection(IConnection $connection)
    {
        $this->getDatabaseTester()->closeConnection($connection);
    }

    /**
     * Returns the test database connection.
     *
     * @return IConnection
     */
    protected abstract function getConnection();

    /**
     * Gets the IDatabaseTester for this testCase. If the IDatabaseTester is
     * not set yet, this method calls newDatabaseTester() to obtain a new
     * instance.
     *
     * @return ITester
     */
    protected function getDatabaseTester()
    {
        if (empty($this->databaseTester)) {
            $this->databaseTester = $this->newDatabaseTester();
        }

        return $this->databaseTester;
    }

    /**
     * Returns the test dataset.
     *
     * @return IDataSet
     */
    protected abstract function getDataSet();

    /**
     * Returns the database operation executed in test setup.
     *
     * @return PHPUnit_Extensions_Database_Operation_IDatabaseOperation
     */
    protected function getSetUpOperation()
    {
        return PHPUnit_Extensions_Database_Operation_Factory::CLEAN_INSERT();
    }

    /**
     * Returns the database operation executed in test cleanup.
     *
     * @return PHPUnit_Extensions_Database_Operation_IDatabaseOperation
     */
    protected function getTearDownOperation()
    {
        return PHPUnit_Extensions_Database_Operation_Factory::NONE();
    }

    /**
     * Creates a IDatabaseTester for this testCase.
     *
     * @return ITester
     */
    protected function newDatabaseTester()
    {
        return new DefaultTester($this->getConnection());
    }

    /**
     * Creates a new DefaultDatabaseConnection using the given PDO connection
     * and database schema name.
     *
     * @param  PDO $connection
     * @param  string $schema
     * @return DefaultConnection
     */
    protected function createDefaultDBConnection(PDO $connection, $schema = '')
    {
        return new DefaultConnection($connection, $schema);
    }

    /**
     * Creates a new ArrayDataSet with the given array.
     * The array parameter is an associative array of tables where the key is
     * the table name and the value an array of rows. Each row is an associative
     * array by itself with keys representing the field names and the values the
     * actual data.
     * For example:
     * array(
     *     "addressbook" => array(
     *         array("id" => 1, "name" => "...", "address" => "..."),
     *         array("id" => 2, "name" => "...", "address" => "...")
     *     )
     * )
     *
     * @param  array $data
     * @return ArrayDataSet
     */
    protected function createArrayDataSet(array $data)
    {
        return new ArrayDataSet($data);
    }

    /**
     * Creates a new FlatXmlDataSet with the given $xmlFile. (absolute path.)
     *
     * @param  string $xmlFile
     * @return FlatXmlDataSet
     */
    protected function createFlatXMLDataSet($xmlFile)
    {
        return new FlatXmlDataSet($xmlFile);
    }

    /**
     * Creates a new XMLDataSet with the given $xmlFile. (absolute path.)
     *
     * @param  string $xmlFile
     * @return PHPUnit_Extensions_Database_DataSet_XmlDataSet
     */
    protected function createXMLDataSet($xmlFile)
    {
        return new PHPUnit_Extensions_Database_DataSet_XmlDataSet($xmlFile);
    }

    /**
     * Create a a new MysqlXmlDataSet with the given $xmlFile. (absolute path.)
     *
     * @param  string $xmlFile
     * @return PHPUnit_Extensions_Database_DataSet_MysqlXmlDataSet
     */
    protected function createMySQLXMLDataSet($xmlFile)
    {
        return new PHPUnit_Extensions_Database_DataSet_MysqlXmlDataSet($xmlFile);
    }

    /**
     * Returns an operation factory instance that can be used to instantiate
     * new operations.
     *
     * @return PHPUnit_Extensions_Database_Operation_Factory
     */
    protected function getOperations()
    {
        return new PHPUnit_Extensions_Database_Operation_Factory();
    }

    /**
     * Performs operation returned by getSetUpOperation().
     */
    protected function setUp()
    {
        parent::setUp();

        $this->databaseTester = null;

        $this->getDatabaseTester()->setSetUpOperation($this->getSetUpOperation());
        $this->getDatabaseTester()->setDataSet($this->getDataSet());
        $this->getDatabaseTester()->onSetUp();
    }

    /**
     * Performs operation returned by getTearDownOperation().
     */
    protected function tearDown()
    {
        $this->getDatabaseTester()->setTearDownOperation($this->getTearDownOperation());
        $this->getDatabaseTester()->setDataSet($this->getDataSet());
        $this->getDatabaseTester()->onTearDown();

        /*
         * Destroy the tester after the test is run to keep DB connections
         * from piling up.
         */
        $this->databaseTester = null;
    }

    /**
     * Asserts that two given tables are equal.
     *
     * @param PHPUnit_Extensions_Database_DataSet_ITable $expected
     * @param PHPUnit_Extensions_Database_DataSet_ITable $actual
     * @param string $message
     */
    public static function assertTablesEqual(PHPUnit_Extensions_Database_DataSet_ITable $expected, PHPUnit_Extensions_Database_DataSet_ITable $actual, $message = '')
    {
        $constraint = new TableIsEqual($expected);

        self::assertThat($actual, $constraint, $message);
    }

    /**
     * Asserts that two given datasets are equal.
     *
     * @param PHPUnit_Extensions_Database_DataSet_ITable $expected
     * @param PHPUnit_Extensions_Database_DataSet_ITable $actual
     * @param string $message
     */
    public static function assertDataSetsEqual(IDataSet $expected, IDataSet $actual, $message = '')
    {
        $constraint = new DataSetIsEqual($expected);

        self::assertThat($actual, $constraint, $message);
    }

    /**
     * Assert that a given table has a given amount of rows
     *
     * @param string $tableName Name of the table
     * @param int $expected Expected amount of rows in the table
     * @param string $message Optional message
     */
    public function assertTableRowCount($tableName, $expected, $message = '')
    {
        $constraint = new TableRowCount($tableName, $expected);
        $actual = $this->getConnection()->getRowCount($tableName);

        self::assertThat($actual, $constraint, $message);
    }

    /**
     * Asserts that a given table contains a given row
     *
     * @param array $expectedRow Row expected to find
     * @param PHPUnit_Extensions_Database_DataSet_ITable $table Table to look into
     * @param string $message Optional message
     */
    public function assertTableContains(array $expectedRow, PHPUnit_Extensions_Database_DataSet_ITable $table, $message = '')
    {
        self::assertThat($table->assertContainsRow($expectedRow), self::isTrue(), $message);
    }
}
