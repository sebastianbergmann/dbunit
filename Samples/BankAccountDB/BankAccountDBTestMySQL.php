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
 * @package    DbUnit
 * @author     Mike Lively <m@digitalsandwich.com>
 * @copyright  Sebastian Bergmann <sebastian@phpunit.de>
 * @license    http://www.opensource.org/licenses/BSD-3-Clause  The BSD 3-Clause License
 * @version    Release: @package_version@
 * @link       http://www.phpunit.de/
 * @since      Class available since Release 1.0.0
 */
class BankAccountDBTestMySQL extends BankAccountDBTest
{
    protected function getPdo()
    {
        return new PDO('mysql:host=localhost;dbname=test', 'root');
    }
}
