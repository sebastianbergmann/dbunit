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
 * Defines the interface necessary to create new mode factories
 *
 * @since      Class available since Release 1.0.0
 */
interface PHPUnit_Extensions_Database_UI_IModeFactory
{
    /**
     * Generates a new mode based on a given name.
     *
     * @param  string                               $mode
     * @return PHPUnit_Extensions_Database_UI_IMode
     */
    public function getMode($mode);

    /**
     * Returns the names of valid modes this factory can create.
     *
     * @return array
     */
    public function getModeList();
}

