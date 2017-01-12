<?php
/*
 * This file is part of DbUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class DBUnitTestUtility
{
    protected static $connection;
    protected static $mySQLConnection;

    public static function getSQLiteMemoryDB()
    {
        if (self::$connection === null) {
            self::$connection = new PDO('sqlite::memory:');
            self::setUpDatabase(self::$connection);
        }

        return self::$connection;
    }

    /**
     * Creates connection to test MySQL database
     *
     * MySQL server must be installed locally, with root access
     * and empty password and listening on unix socket
     *
     * @return PDO
     *
     * @see    DBUnitTestUtility::setUpMySqlDatabase()
     */
    public static function getMySQLDB()
    {
        if (self::$mySQLConnection === null) {
            self::$mySQLConnection = new PDO(PHPUNIT_TESTSUITE_EXTENSION_DATABASE_MYSQL_DSN, PHPUNIT_TESTSUITE_EXTENSION_DATABASE_MYSQL_USERNAME, PHPUNIT_TESTSUITE_EXTENSION_DATABASE_MYSQL_PASSWORD);

            self::setUpMySQLDatabase(self::$mySQLConnection);
        }

        return self::$mySQLConnection;
    }

    protected static function setUpDatabase(PDO $connection)
    {
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $connection->exec(
          'CREATE TABLE IF NOT EXISTS table1 (
            table1_id INTEGER PRIMARY KEY AUTOINCREMENT,
            column1 VARCHAR(20),
            column2 INT(10),
            column3 DECIMAL(6,2),
            column4 TEXT
          )'
        );

        $connection->exec(
          'CREATE TABLE IF NOT EXISTS table2 (
            table2_id INTEGER PRIMARY KEY AUTOINCREMENT,
            column5 VARCHAR(20),
            column6 INT(10),
            column7 DECIMAL(6,2),
            column8 TEXT
          )'
        );

        $connection->exec(
          'CREATE TABLE IF NOT EXISTS table3 (
            table3_id INTEGER PRIMARY KEY AUTOINCREMENT,
            column9 VARCHAR(20),
            column10 INT(10),
            column11 DECIMAL(6,2),
            column12 TEXT
          )'
        );
    }

    /**
     * Creates default testing schema for MySQL database
     *
     * Tables must containt foreign keys and use InnoDb storage engine
     * for constraint tests to be executed properly
     *
     * @param PDO $connection PDO instance representing connection to MySQL database
     *
     * @see   DBUnitTestUtility::getMySQLDB()
     */
    protected static function setUpMySqlDatabase(PDO $connection)
    {
        $connection->exec(
          'CREATE TABLE IF NOT EXISTS table1 (
            table1_id INTEGER AUTO_INCREMENT,
            column1 VARCHAR(20),
            column2 INT(10),
            column3 DECIMAL(6,2),
            column4 TEXT,
            PRIMARY KEY (table1_id)
          ) ENGINE=INNODB;
        ');

        $connection->exec(
          'CREATE TABLE IF NOT EXISTS table2 (
            table2_id INTEGER AUTO_INCREMENT,
            table1_id INTEGER,
            column5 VARCHAR(20),
            column6 INT(10),
            column7 DECIMAL(6,2),
            column8 TEXT,
            PRIMARY KEY (table2_id),
            FOREIGN KEY (table1_id) REFERENCES table1(table1_id)
          ) ENGINE=INNODB;
        ');

        $connection->exec(
          'CREATE TABLE IF NOT EXISTS table3 (
            table3_id INTEGER AUTO_INCREMENT,
            table2_id INTEGER,
            column9 VARCHAR(20),
            column10 INT(10),
            column11 DECIMAL(6,2),
            column12 TEXT,
            PRIMARY KEY (table3_id),
            FOREIGN KEY (table2_id) REFERENCES table2(table2_id)
          ) ENGINE=INNODB;
        ');
    }
}
