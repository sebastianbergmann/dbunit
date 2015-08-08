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
 * @since      File available since Release 1.0.0
 */
class Extensions_Database_Constraint_TableRowCountTest extends PHPUnit_Framework_TestCase
{
    public function testConstraint()
    {
        $constraint = new PHPUnit_Extensions_Database_Constraint_TableRowCount('name', 42);

        $this->assertTrue($constraint->evaluate(42, '', true));
        $this->assertFalse($constraint->evaluate(24, '', true));
        $this->assertEquals('is equal to expected row count 42', $constraint->toString());

        try {
            $this->assertThat(24, $constraint, '');
        } catch (PHPUnit_Framework_ExpectationFailedException $e) {
            $this->assertEquals(
                'Failed asserting that 24 is equal to expected row count 42.',
                $e->getMessage()
            );
        }
    }
}
