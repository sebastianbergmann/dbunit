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

/**
 * Base class for tests for the BankAccount class.
 *
 * @since      Class available since Release 1.0.0
 */
abstract class BankAccountDBTest extends PHPUnit_Extensions_Database_TestCase
{
    protected $pdo;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->pdo = $this->getPdo();
        BankAccount::createTable($this->pdo);
    }

    /**
     * Custom method to obtain a configured PDO instance.
     *
     * @return \PDO
     */
    abstract protected function getPdo();

    /**
     * Returns the test database connection.
     *
     * @return PHPUnit_Extensions_Database_DB_IDatabaseConnection
     */
    protected function getConnection()
    {
        return $this->createDefaultDBConnection($this->pdo);
    }

    protected function getDataSet()
    {
        return $this->createFlatXMLDataSet(dirname(__FILE__) . '/_files/bank-account-seed.xml');
    }

    public function testNewAccountBalanceIsInitiallyZero()
    {
        $bank_account = new BankAccount('12345678912345678', $this->pdo);
        $this->assertEquals(0, $bank_account->getBalance());
    }

    public function testOldAccountInfoInitiallySet()
    {
        $bank_account = new BankAccount('15934903649620486', $this->pdo);
        $this->assertEquals(100, $bank_account->getBalance());
        $this->assertEquals('15934903649620486', $bank_account->getAccountNumber());

        $bank_account = new BankAccount('15936487230215067', $this->pdo);
        $this->assertEquals(1216, $bank_account->getBalance());
        $this->assertEquals('15936487230215067', $bank_account->getAccountNumber());

        $bank_account = new BankAccount('12348612357236185', $this->pdo);
        $this->assertEquals(89, $bank_account->getBalance());
        $this->assertEquals('12348612357236185', $bank_account->getAccountNumber());
    }

    public function testAccountBalanceDeposits()
    {
        $bank_account = new BankAccount('15934903649620486', $this->pdo);
        $bank_account->depositMoney(100);

        $bank_account = new BankAccount('15936487230215067', $this->pdo);
        $bank_account->depositMoney(230);

        $bank_account = new BankAccount('12348612357236185', $this->pdo);
        $bank_account->depositMoney(24);

        $xml_dataset = $this->createFlatXMLDataSet(dirname(__FILE__) . '/_files/bank-account-after-deposits.xml');
        $this->assertDataSetsEqual($xml_dataset, $this->getConnection()->createDataSet());
    }

    public function testAccountBalanceWithdrawals()
    {
        $bank_account = new BankAccount('15934903649620486', $this->pdo);
        $bank_account->withdrawMoney(100);

        $bank_account = new BankAccount('15936487230215067', $this->pdo);
        $bank_account->withdrawMoney(230);

        $bank_account = new BankAccount('12348612357236185', $this->pdo);
        $bank_account->withdrawMoney(24);

        $xml_dataset = $this->createFlatXMLDataSet(dirname(__FILE__) . '/_files/bank-account-after-withdrawals.xml');
        $this->assertDataSetsEqual($xml_dataset, $this->getConnection()->createDataSet());
    }

    public function testNewAccountCreation()
    {
        $bank_account = new BankAccount('12345678912345678', $this->pdo);

        $xml_dataset = $this->createFlatXMLDataSet(dirname(__FILE__) . '/_files/bank-account-after-new-account.xml');
        $this->assertDataSetsEqual($xml_dataset, $this->getConnection()->createDataSet());
    }
}
