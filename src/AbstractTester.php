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

use PHPUnit\DbUnit\DataSet\IDataSet;
use PHPUnit\DbUnit\Database\IConnection;
use PHPUnit_Extensions_Database_Operation_Factory;
use PHPUnit_Extensions_Database_Operation_IDatabaseOperation;

/**
 * Can be used as a foundation for new DatabaseTesters.
 */
abstract class AbstractTester implements ITester
{
    /**
     * @var PHPUnit_Extensions_Database_Operation_IDatabaseOperation
     */
    protected $setUpOperation;

    /**
     * @var PHPUnit_Extensions_Database_Operation_IDatabaseOperation
     */
    protected $tearDownOperation;

    /**
     * @var IDataSet
     */
    protected $dataSet;

    /**
     * @var string
     */
    protected $schema;

    /**
     * Creates a new database tester.
     */
    public function __construct()
    {
        $this->setUpOperation = PHPUnit_Extensions_Database_Operation_Factory::CLEAN_INSERT();
        $this->tearDownOperation = PHPUnit_Extensions_Database_Operation_Factory::NONE();
    }

    /**
     * Closes the specified connection.
     *
     * @param IConnection $connection
     */
    public function closeConnection(IConnection $connection)
    {
        $connection->close();
    }

    /**
     * Returns the test dataset.
     *
     * @return IDataSet
     */
    public function getDataSet()
    {
        return $this->dataSet;
    }

    /**
     * TestCases must call this method inside setUp().
     */
    public function onSetUp()
    {
        $this->getSetUpOperation()->execute($this->getConnection(), $this->getDataSet());
    }

    /**
     * TestCases must call this method inside tearDown().
     */
    public function onTearDown()
    {
        $this->getTearDownOperation()->execute($this->getConnection(), $this->getDataSet());
    }

    /**
     * Sets the test dataset to use.
     *
     * @param IDataSet $dataSet
     */
    public function setDataSet(IDataSet $dataSet)
    {
        $this->dataSet = $dataSet;
    }

    /**
     * Sets the schema value.
     *
     * @param string $schema
     */
    public function setSchema($schema)
    {
        $this->schema = $schema;
    }

    /**
     * Sets the DatabaseOperation to call when starting the test.
     *
     * @param PHPUnit_Extensions_Database_Operation_IDatabaseOperation $setUpOperation
     */
    public function setSetUpOperation(PHPUnit_Extensions_Database_Operation_IDatabaseOperation $setUpOperation)
    {
        $this->setUpOperation = $setUpOperation;
    }

    /**
     * Sets the DatabaseOperation to call when ending the test.
     *
     * @param PHPUnit_Extensions_Database_Operation_IDatabaseOperation $tearDownOperation
     */
    public function setTearDownOperation(PHPUnit_Extensions_Database_Operation_IDatabaseOperation $tearDownOperation)
    {
        $this->tearDownOperation = $tearDownOperation;
    }

    /**
     * Returns the schema value
     *
     * @return string
     */
    protected function getSchema()
    {
        return $this->schema;
    }

    /**
     * Returns the database operation that will be called when starting the test.
     *
     * @return PHPUnit_Extensions_Database_Operation_IDatabaseOperation
     */
    protected function getSetUpOperation()
    {
        return $this->setUpOperation;
    }

    /**
     * Returns the database operation that will be called when ending the test.
     *
     * @return PHPUnit_Extensions_Database_Operation_IDatabaseOperation
     */
    protected function getTearDownOperation()
    {
        return $this->tearDownOperation;
    }
}
