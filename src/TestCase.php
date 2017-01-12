<?php
/*
 * This file is part of DbUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPUnit\DbUnit;

/**
 * A TestCase extension that provides functionality for testing and asserting
 * against a real database.
 */
abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    use TestCaseTrait;
}
