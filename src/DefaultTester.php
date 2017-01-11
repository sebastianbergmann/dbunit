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

use PHPUnit\DbUnit\Database\IConnection;

/**
 * This is the default implementation of the database tester. It receives its
 * connection object from the constructor.
 */
class DefaultTester extends AbstractTester
{
    /**
     * @var IConnection
     */
    protected $connection;

    /**
     * Creates a new default database tester using the given connection.
     *
     * @param IConnection $connection
     */
    public function __construct(IConnection $connection)
    {
        parent::__construct();

        $this->connection = $connection;
    }

    /**
     * Returns the test database connection.
     *
     * @return IConnection
     */
    public function getConnection()
    {
        return $this->connection;
    }
}
