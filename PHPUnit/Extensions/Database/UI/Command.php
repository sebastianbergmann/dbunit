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
 * Delegates database extension commands to the appropriate mode classes.
 *
 * @package    DbUnit
 * @author     Mike Lively <m@digitalsandwich.com>
 * @copyright  2010-2014 Mike Lively <m@digitalsandwich.com>
 * @license    http://www.opensource.org/licenses/BSD-3-Clause  The BSD 3-Clause License
 * @version    Release: @package_version@
 * @link       http://www.phpunit.de//**
 * @since      Class available since Release 1.0.0
 */
class PHPUnit_Extensions_Database_UI_Command
{
    /**
     * @var PHPUnit_Extensions_Database_UI_IModeFactory
     */
    protected $modeFactory;

    /**
     * @param PHPUnit_Extensions_Database_UI_IModeFactory $modeFactory
     */
    public function __construct(PHPUnit_Extensions_Database_UI_IModeFactory $modeFactory)
    {
        $this->modeFactory = $modeFactory;
    }

    /**
     * Executes the database extension ui.
     *
     * @param PHPUnit_Extensions_Database_UI_IMedium $medium
     * @param PHPUnit_Extensions_Database_UI_Context $context
     */
    public function main(PHPUnit_Extensions_Database_UI_IMedium $medium, PHPUnit_Extensions_Database_UI_Context $context)
    {
        try {
            $medium->buildContext($context);
            $mode = $this->modeFactory->getMode($context->getMode());
            $mode->execute($context->getModeArguments(), $medium);

        } catch (Exception $e) {
            $medium->handleException($e);
        }
    }
}

