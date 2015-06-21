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
 * This is the default implementation of the database tester. It receives its
 * connection object from the constructor.
 *
 * @since      Class available since Release 1.0.0
 */
class PHPUnit_Extensions_Database_DefaultTester extends PHPUnit_Extensions_Database_AbstractTester
{
    /**
     * @var PHPUnit_Extensions_Database_DB_IDatabaseConnection
     */
    protected $connection;

    /**
     * Creates a new default database tester using the given connection.
     *
     * @param PHPUnit_Extensions_Database_DB_IDatabaseConnection $connection
     */
    public function __construct(PHPUnit_Extensions_Database_DB_IDatabaseConnection $connection)
    {
        parent::__construct();

        $this->connection = $connection;
    }

    /**
     * Returns the test database connection.
     *
     * @return PHPUnit_Extensions_Database_DB_IDatabaseConnection
     */
    public function getConnection()
    {
        return $this->connection;
    }
}
