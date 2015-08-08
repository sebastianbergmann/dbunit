<?php
/*
 * This file is part of DBUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class BankAccountException extends RuntimeException {}

/**
 * A bank account.
 *
 * @since      Class available since Release 1.0.0
 */
class BankAccount
{
    /**
     * The bank account's balance.
     *
     * @var float
     */
    protected $balance = 0.00;

    /**
     * The bank account's number.
     *
     * @var string
     */
    protected $accountNumber = '';

    /**
     * The PDO connection used to store and retrieve bank account information.
     *
     * @var PDO
     */
    protected $pdo;

    /**
     * Initializes the bank account object.
     *
     * @param string $accountNumber
     * @param \PDO   $pdo
     */
    public function __construct($accountNumber, PDO $pdo)
    {
        $this->accountNumber = $accountNumber;
        $this->pdo           = $pdo;

        $this->loadAccount();
    }

    /**
     * Returns the bank account's balance.
     *
     * @return float
     */
    public function getBalance()
    {
        return $this->balance;
    }

    /**
     * Sets the bank account's balance.
     *
     * @param  float                $balance
     * @throws BankAccountException
     */
    protected function setBalance($balance)
    {
        if ($balance >= 0) {
            $this->balance = $balance;
            $this->updateAccount();
        } else {
            throw new BankAccountException;
        }
    }

    /**
     * Returns the bank account's number.
     *
     * @return float
     */
    public function getAccountNumber()
    {
        return $this->accountNumber;
    }

    /**
     * Deposits an amount of money to the bank account.
     *
     * @param  float                $balance
     * @throws BankAccountException
     */
    public function depositMoney($balance)
    {
        $this->setBalance($this->getBalance() + $balance);

        return $this->getBalance();
    }

    /**
     * Withdraws an amount of money from the bank account.
     *
     * @param  float                $balance
     * @throws BankAccountException
     */
    public function withdrawMoney($balance)
    {
        $this->setBalance($this->getBalance() - $balance);

        return $this->getBalance();
    }

    /**
     * Loads account information from the database.
     */
    protected function loadAccount()
    {
        $query = 'SELECT * FROM bank_account WHERE account_number = ?';

        $statement = $this->pdo->prepare($query);

        $statement->execute([$this->accountNumber]);

        if ($bankAccountInfo = $statement->fetch(PDO::FETCH_ASSOC))
        {
            $this->balance = $bankAccountInfo['balance'];
        }
        else
        {
            $this->balance = 0;
            $this->addAccount();
        }
    }

    /**
     * Saves account information to the database.
     */
    protected function updateAccount()
    {
        $query = 'UPDATE bank_account SET balance = ? WHERE account_number = ?';

        $statement = $this->pdo->prepare($query);
        $statement->execute([$this->balance, $this->accountNumber]);
    }

    /**
     * Adds account information to the database.
     */
    protected function addAccount()
    {
        $query = 'INSERT INTO bank_account (balance, account_number) VALUES(?, ?)';

        $statement = $this->pdo->prepare($query);
        $statement->execute([$this->balance, $this->accountNumber]);
    }

    static public function createTable(PDO $pdo)
    {
        $query = '
            CREATE TABLE bank_account (
                account_number VARCHAR(17) PRIMARY KEY,
                balance DECIMAL(9,2) NOT NULL DEFAULT 0
            );
        ';

        $pdo->query($query);
    }
}
