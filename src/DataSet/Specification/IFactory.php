<?php
/*
 * This file is part of DbUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPUnit\DbUnit\DataSet\Specification;

/**
 * An interface for data set spec factories.
 */
interface IFactory
{
    /**
     * Returns the data set
     *
     * @param string $type
     *
     * @return Specification
     */
    public function getDataSetSpecByType($type);
}
