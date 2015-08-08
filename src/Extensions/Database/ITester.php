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
 * This is the interface for DatabaseTester objects. These objects are used to
 * add database testing to existing test cases using composition instead of
 * extension.
 *
 * @since      Class available since Release 1.0.0
 */
interface PHPUnit_Extensions_Database_ITester
{
    /**
     * Closes the specified connection.
     *
     * @param PHPUnit_Extensions_Database_DB_IDatabaseConnection $connection
     */
    public function closeConnection(PHPUnit_Extensions_Database_DB_IDatabaseConnection $connection);

    /**
     * Returns the test database connection.
     *
     * @return PHPUnit_Extensions_Database_DB_IDatabaseConnection
     */
    public function getConnection();

    /**
     * Returns the test dataset.
     *
     * @return PHPUnit_Extensions_Database_DataSet_IDataSet
     */
    public function getDataSet();

    /**
     * TestCases must call this method inside setUp().
     */
    public function onSetUp();

    /**
     * TestCases must call this method inside tearDown().
     */
    public function onTearDown();

    /**
     * Sets the test dataset to use.
     *
     * @param PHPUnit_Extensions_Database_DataSet_IDataSet $dataSet
     */
    public function setDataSet(PHPUnit_Extensions_Database_DataSet_IDataSet $dataSet);

    /**
     * Sets the schema value.
     *
     * @param string $schema
     */
    public function setSchema($schema);

    /**
     * Sets the DatabaseOperation to call when starting the test.
     *
     * @param PHPUnit_Extensions_Database_Operation_DatabaseOperation $setUpOperation
     */
    public function setSetUpOperation(PHPUnit_Extensions_Database_Operation_IDatabaseOperation $setUpOperation);

    /**
     * Sets the DatabaseOperation to call when stopping the test.
     *
     * @param PHPUnit_Extensions_Database_Operation_DatabaseOperation $tearDownOperation
     */
    public function setTearDownOperation(PHPUnit_Extensions_Database_Operation_IDatabaseOperation $tearDownOperation);
}
