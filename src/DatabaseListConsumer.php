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

/**
 * An interface for classes that require a list of databases to operate.
 */
interface DatabaseListConsumer
{
    /**
     * Sets the database for the spec
     *
     * @param array $databases
     */
    public function setDatabases(array $databases);
}
