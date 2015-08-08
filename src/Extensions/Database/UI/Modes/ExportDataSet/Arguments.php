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
 * Represents arguments received from a medium.
 *
 * @since      Class available since Release 1.0.0
 */
class PHPUnit_Extensions_Database_UI_Modes_ExportDataSet_Arguments
{
    /**
     * @var array
     */
    protected $arguments = [];

    /**
     * @param array $arguments
     */
    public function __construct(array $arguments)
    {
        foreach ($arguments as $argument) {
            list($argName, $argValue) = explode('=', $argument, 2);

            $argName = trim($argName, '-');

            if (!isset($this->arguments[$argName])) {
                $this->arguments[$argName] = [];
            }

            $this->arguments[$argName][] = $argValue;
        }
    }

    /**
     * Returns an array of arguments matching the given $argName
     *
     * @param  string $argName
     * @return array
     */
    public function getArgumentArray($argName)
    {
        if ($this->argumentIsSet($argName)) {
            return $this->arguments[$argName];
        } else {
            return NULL;
        }
    }

    /**
     * Returns a single argument value.
     *
     * If $argName points to an array the first argument will be returned.
     *
     * @param  string $argName
     * @return mixed
     */
    public function getSingleArgument($argName)
    {
        if ($this->argumentIsSet($argName)) {
            return reset($this->arguments[$argName]);
        } else {
            return NULL;
        }
    }

    /**
     * Returns whether an argument is set.
     *
     * @param  string $argName
     * @return bool
     */
    public function argumentIsSet($argName)
    {
        return array_key_exists($argName, $this->arguments);
    }

    /**
     * Returns an array containing the names of all arguments provided.
     *
     * @return array
     */
    public function getArgumentNames()
    {
        return array_keys($this->arguments);
    }

    /**
     * Returns an array of database arguments keyed by name.
     *
     * @todo this should be moved.
     * @return array
     */
    public function getDatabases()
    {
        $databases = $this->getArgumentArray('database');

        $retDb = [];
        foreach ($databases as $db) {
            list($name, $arg) = explode(':', $db, 2);
            $retDb[$name]     = $arg;
        }

        return $retDb;
    }
}

