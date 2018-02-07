<?php
/*
 * This file is part of DbUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPUnit\DbUnit\Constraint;

use PHPUnit\Framework\Constraint\Constraint;

/**
 * Asserts the row count in a table
 */
class TableRowCount extends Constraint
{
    /**
     * @var int
     */
    protected $value;

    /**
     * @var string
     */
    protected $tableName;

    /**
     * Creates a new constraint.
     *
     * @param $tableName
     * @param $value
     */
    public function __construct($tableName, $value)
    {
        parent::__construct();
        $this->tableName = $tableName;
        $this->value     = $value;
    }

    /**
     * Returns a string representation of the constraint.
     *
     * @return string
     */
    public function toString(): string
    {
        return \sprintf('is equal to expected row count %d', $this->value);
    }

    /**
     * Evaluates the constraint for parameter $other. Returns TRUE if the
     * constraint is met, FALSE otherwise.
     *
     * This method can be overridden to implement the evaluation algorithm.
     *
     * @param mixed $other value or object to evaluate
     *
     * @return bool
     */
    protected function matches($other): bool
    {
        return $other == $this->value;
    }
}
