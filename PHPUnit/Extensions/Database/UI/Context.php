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
 * Holds the context of a particular database extension ui call.
 *
 * @package    DbUnit
 * @author     Mike Lively <m@digitalsandwich.com>
 * @copyright  2010-2014 Mike Lively <m@digitalsandwich.com>
 * @license    http://www.opensource.org/licenses/BSD-3-Clause  The BSD 3-Clause License
 * @version    Release: @package_version@
 * @link       http://www.phpunit.de//**
 * @since      Class available since Release 1.0.0
 */
class PHPUnit_Extensions_Database_UI_Context
{
    /**
     * @var string
     */
    protected $mode;

    /**
     * @var array
     */
    protected $modeArguments;

    /**
     * @param string $mode
     */
    public function setMode($mode)
    {
        $this->mode = $mode;
    }

    /**
     * @return string
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * @param array $arguments
     */
    public function setModeArguments(array $arguments)
    {
        $this->mode_arguments = $arguments;
    }

    /**
     * @return array
     */
    public function getModeArguments()
    {
        return $this->mode_arguments;
    }
}

