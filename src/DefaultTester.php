<?php
/*
 * This file is part of DbUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPUnit\DbUnit;

use PHPUnit\DbUnit\Database\Connection;

/**
 * This is the default implementation of the database tester. It receives its
 * connection object from the constructor.
 */
class DefaultTester extends AbstractTester
{
    /**
     * @var Connection
     */
    protected $connection;

    /**
     * Creates a new default database tester using the given connection.
     *
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        parent::__construct();

        $this->connection = $connection;
    }

    /**
     * Returns the test database connection.
     *
     * @return Connection
     */
    public function getConnection()
    {
        return $this->connection;
    }
}
