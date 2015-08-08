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
 * A TestCase extension that provides functionality for testing and asserting
 * against a real database.
 *
 * @since      Class available since Release 1.0.0
 */
abstract class PHPUnit_Extensions_Database_TestCase extends PHPUnit_Framework_TestCase
{
    use PHPUnit_Extensions_Database_TestCase_Trait;
}
