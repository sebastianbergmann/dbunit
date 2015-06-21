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
 * An interface for classes that require a list of databases to operate.
 *
 * @since      Class available since Release 1.0.0
 */
interface PHPUnit_Extensions_Database_IDatabaseListConsumer
{
    /**
     * Sets the database for the spec
     *
     * @param array $databases
     */
    public function setDatabases(array $databases);
}
