<?php
/*
 * This file is part of DbUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPUnit\DbUnit\Operation;

use PHPUnit\DbUnit\Database\Connection;
use PHPUnit\DbUnit\DataSet\IDataSet;

/**
 * This class represents a null database operation.
 */
class None implements Operation
{
    public function execute(Connection $connection, IDataSet $dataSet): void
    {
        /* do nothing */
    }
}
