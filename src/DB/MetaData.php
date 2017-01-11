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
 * Provides a basic constructor for all meta data classes and a factory for
 * generating the appropriate meta data class.
 *
 * @since      Class available since Release 1.0.0
 */
abstract class PHPUnit_Extensions_Database_DB_MetaData implements PHPUnit_Extensions_Database_DB_IMetaData
{
    protected static $metaDataClassMap = [
        'pgsql'    => 'PHPUnit_Extensions_Database_DB_MetaData_PgSQL',
        'mysql'    => 'PHPUnit_Extensions_Database_DB_MetaData_MySQL',
        'oci'      => 'PHPUnit_Extensions_Database_DB_MetaData_Oci',
        'sqlite'   => 'PHPUnit_Extensions_Database_DB_MetaData_Sqlite',
        'sqlite2'  => 'PHPUnit_Extensions_Database_DB_MetaData_Sqlite',
        'sqlsrv'   => 'PHPUnit_Extensions_Database_DB_MetaData_SqlSrv',
        'firebird' => 'PHPUnit_Extensions_Database_DB_MetaData_Firebird',
        'dblib'    => 'PHPUnit_Extensions_Database_DB_MetaData_Dblib'
    ];

    /**
     * The PDO connection used to retreive database meta data
     *
     * @var PDO
     */
    protected $pdo;

    /**
     * The default schema name for the meta data object.
     *
     * @var string
     */
    protected $schema;

    /**
     * The character used to quote schema objects.
     */
    protected $schemaObjectQuoteChar = '"';

    /**
     * The command used to perform a TRUNCATE operation.
     */
    protected $truncateCommand = 'TRUNCATE';

    /**
     * Creates a new database meta data object using the given pdo connection
     * and schema name.
     *
     * @param PDO    $pdo
     * @param string $schema
     */
    public final function __construct(PDO $pdo, $schema = '')
    {
        $this->pdo    = $pdo;
        $this->schema = $schema;
    }

    /**
     * Creates a meta data object based on the driver of given $pdo object and
     * $schema name.
     *
     * @param  PDO                                     $pdo
     * @param  string                                  $schema
     * @return PHPUnit_Extensions_Database_DB_MetaData
     */
    public static function createMetaData(PDO $pdo, $schema = '')
    {
        $driverName = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
        if (isset(self::$metaDataClassMap[$driverName])) {
            $className = self::$metaDataClassMap[$driverName];

            if ($className instanceof ReflectionClass) {
                return $className->newInstance($pdo, $schema);
            } else {
                return self::registerClassWithDriver($className, $driverName)->newInstance($pdo, $schema);
            }
        } else {
            throw new PHPUnit_Extensions_Database_Exception("Could not find a meta data driver for {$driverName} pdo driver.");
        }
    }

    /**
     * Validates and registers the given $className with the given $pdoDriver.
     * It should be noted that this function will not attempt to include /
     * require the file. The $pdoDriver can be determined by the value of the
     * PDO::ATTR_DRIVER_NAME attribute for a pdo object.
     *
     * A reflection of the $className is returned.
     *
     * @param  string          $className
     * @param  string          $pdoDriver
     * @return ReflectionClass
     */
    public static function registerClassWithDriver($className, $pdoDriver)
    {
        if (!class_exists($className)) {
            throw new PHPUnit_Extensions_Database_Exception("Specified class for {$pdoDriver} driver ({$className}) does not exist.");
        }

        $reflection = new ReflectionClass($className);
        if ($reflection->isSubclassOf('PHPUnit_Extensions_Database_DB_MetaData')) {
            return self::$metaDataClassMap[$pdoDriver] = $reflection;
        } else {
            throw new PHPUnit_Extensions_Database_Exception("Specified class for {$pdoDriver} driver ({$className}) does not extend PHPUnit_Extensions_Database_DB_MetaData.");
        }
    }

    /**
     * Returns the schema for the connection.
     *
     * @return string
     */
    public function getSchema()
    {
        return $this->schema;
    }

    /**
     * Returns a quoted schema object. (table name, column name, etc)
     *
     * @param  string $object
     * @return string
     */
    public function quoteSchemaObject($object)
    {
        $parts       = explode('.', $object);
        $quotedParts = [];

        foreach ($parts as $part) {
            $quotedParts[] = $this->schemaObjectQuoteChar .
                str_replace($this->schemaObjectQuoteChar, $this->schemaObjectQuoteChar . $this->schemaObjectQuoteChar, $part) .
                $this->schemaObjectQuoteChar;
        }

        return implode('.', $quotedParts);
    }

    /**
     * Seperates the schema and the table from a fully qualified table name.
     *
     * Returns an associative array containing the 'schema' and the 'table'.
     *
     * @param  string $fullTableName
     * @return array
     */
    public function splitTableName($fullTableName)
    {
        if (($dot = strpos($fullTableName, '.')) !== FALSE) {
            return [
                'schema' => substr($fullTableName, 0, $dot),
                'table'  => substr($fullTableName, $dot + 1)
            ];
        } else {
            return [
                'schema' => NULL,
                'table'  => $fullTableName
            ];
        }
    }

    /**
     * Returns the command for the database to truncate a table.
     *
     * @return string
     */
    public function getTruncateCommand()
    {
        return $this->truncateCommand;
    }

    /**
     * Returns true if the rdbms allows cascading
     *
     * @return bool
     */
    public function allowsCascading()
    {
        return FALSE;
    }

    /**
     * Disables primary keys if the rdbms does not allow setting them otherwise
     *
     * @param string $tableName
     */
    public function disablePrimaryKeys($tableName)
    {
        return;
    }

    /**
     * Reenables primary keys after they have been disabled
     *
     * @param string $tableName
     */
    public function enablePrimaryKeys($tableName)
    {
        return;
    }
}
