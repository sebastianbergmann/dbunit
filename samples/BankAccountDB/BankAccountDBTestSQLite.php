<?php
/*
 * This file is part of DBUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once dirname(__FILE__) . '/BankAccount.php';
require_once dirname(__FILE__) . '/BankAccountDBTest.php';

/**
 * Tests for the BankAccount class.
 *
 * @since      Class available since Release 1.0.0
 */
class BankAccountDBTestSQLite extends BankAccountDBTest
{
    protected function getPdo()
    {
        return new PDO('sqlite::memory:');
    }
}
