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
 * Defines the interface necessary to create new medium printers.
 *
 * @since      Class available since Release 1.0.0
 */
interface PHPUnit_Extensions_Database_UI_IMediumPrinter
{
    /**
     * Prints standard output messages.
     *
     * @param string $message
     */
    public function output($message);

    /**
     * Prints standard error messages.
     *
     * @param string $message
     */
    public function error($message);
}

