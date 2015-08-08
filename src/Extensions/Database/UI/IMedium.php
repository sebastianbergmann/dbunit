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
 * Defines the interface necessary to create new mediums.
 *
 * @since      Class available since Release 1.0.0
 */
interface PHPUnit_Extensions_Database_UI_IMedium extends PHPUnit_Extensions_Database_UI_IMediumPrinter
{
    /**
     * Builds the context for the application.
     *
     * @param PHPUnit_Extensions_Database_UI_Context $context
     */
    public function buildContext(PHPUnit_Extensions_Database_UI_Context $context);

    /**
     * Handles the displaying of exceptions received from the application.
     *
     * @param Exception $e
     */
    public function handleException(Exception $e);
}

