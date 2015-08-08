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
 * An interface for data set spec factories.
 *
 * @since      Class available since Release 1.0.0
 */
interface PHPUnit_Extensions_Database_DataSet_Specs_IFactory
{
    /**
     * Returns the data set
     *
     * @param  string                                    $type
     * @return PHPUnit_Extensions_Database_DataSet_ISpec
     */
    public function getDataSetSpecByType($type);
}
