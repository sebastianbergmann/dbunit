<?php
/*
 * This file is part of DbUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPUnit\DbUnit\DataSet;

/**
 * Allows for replacing arbitrary values or portions of values with new data.
 *
 * A usage for this is replacing all values == '[NULL'] with a true NULL value
 */
class ReplacementDataSet extends AbstractDataSet
{
    /**
     * @var IDataSet
     */
    protected $dataSet;

    /**
     * @var array
     */
    protected $fullReplacements;

    /**
     * @var array
     */
    protected $subStrReplacements;

    /**
     * Creates a new replacement dataset
     *
     * You can pass in any data set that implements PHPUnit_Extensions_Database_DataSet_IDataSet
     *
     * @param string $delimiter
     * @param string $enclosure
     * @param string $escape
     */
    public function __construct(IDataSet $dataSet, array $fullReplacements = [], array $subStrReplacements = [])
    {
        $this->dataSet            = $dataSet;
        $this->fullReplacements   = $fullReplacements;
        $this->subStrReplacements = $subStrReplacements;
    }

    /**
     * Adds a new full replacement
     *
     * Full replacements will only replace values if the FULL value is a match
     *
     * @param string $value
     * @param string $replacement
     */
    public function addFullReplacement($value, $replacement): void
    {
        $this->fullReplacements[$value] = $replacement;
    }

    /**
     * Adds a new substr replacement
     *
     * Substr replacements will replace all occurances of the substr in every column
     *
     * @param string $value
     * @param string $replacement
     */
    public function addSubStrReplacement($value, $replacement): void
    {
        $this->subStrReplacements[$value] = $replacement;
    }

    /**
     * Creates an iterator over the tables in the data set. If $reverse is
     * true a reverse iterator will be returned.
     *
     * @param bool $reverse
     *
     * @return ITableIterator
     */
    protected function createIterator($reverse = false)
    {
        $innerIterator = $reverse ? $this->dataSet->getReverseIterator() : $this->dataSet->getIterator();

        return new ReplacementTableIterator($innerIterator, $this->fullReplacements, $this->subStrReplacements);
    }
}
