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
 * This class facilitates combining database operations. To create a composite
 * operation pass an array of classes that implement
 * PHPUnit_Extensions_Database_Operation_IDatabaseOperation and they will be
 * executed in that order against all data sets.
 *
 * @since      Class available since Release 1.0.0
 */
class PHPUnit_Extensions_Database_Operation_Composite implements PHPUnit_Extensions_Database_Operation_IDatabaseOperation
{
    /**
     * @var array
     */
    protected $operations = [];

    /**
     * Creates a composite operation.
     *
     * @param array $operations
     */
    public function __construct(Array $operations)
    {
        foreach ($operations as $operation) {
            if ($operation instanceof PHPUnit_Extensions_Database_Operation_IDatabaseOperation) {
                $this->operations[] = $operation;
            } else {
                throw new InvalidArgumentException('Only database operation instances can be passed to a composite database operation.');
            }
        }
    }

    public function execute(PHPUnit_Extensions_Database_DB_IDatabaseConnection $connection, PHPUnit_Extensions_Database_DataSet_IDataSet $dataSet)
    {
        try {
            foreach ($this->operations as $operation) {
                /* @var $operation PHPUnit_Extensions_Database_Operation_IDatabaseOperation */
                $operation->execute($connection, $dataSet);
            }
        } catch (PHPUnit_Extensions_Database_Operation_Exception $e) {
            throw new PHPUnit_Extensions_Database_Operation_Exception("COMPOSITE[{$e->getOperation()}]", $e->getQuery(), $e->getArgs(), $e->getTable(), $e->getError());
        }
    }
}
