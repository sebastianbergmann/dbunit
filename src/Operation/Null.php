<?php
/*
 * This file is part of DBUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
use PHPUnit\DbUnit\Database\IConnection;
use PHPUnit\DbUnit\DataSet\IDataSet;
use PHPUnit\DbUnit\Operation\Operation;

/**
 * This class represents a null database operation.
 */
class PHPUnit_Extensions_Database_Operation_Null implements Operation
{
    public function execute(IConnection $connection, IDataSet $dataSet)
    {
        /* do nothing */
    }
}
