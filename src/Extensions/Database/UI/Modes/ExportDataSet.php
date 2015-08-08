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
 * The class for the export-dataset command.
 *
 * This command is used to convert existing data sets or data in the database
 * into a valid data set format.
 *
 * @since      Class available since Release 1.0.0
 */
class PHPUnit_Extensions_Database_UI_Modes_ExportDataSet implements PHPUnit_Extensions_Database_UI_IMode
{
    /**
     * Executes the export dataset command.
     *
     * @param array                                         $modeArguments
     * @param PHPUnit_Extensions_Database_UI_IMediumPrinter $medium
     */
    public function execute(array $modeArguments, PHPUnit_Extensions_Database_UI_IMediumPrinter $medium)
    {
        $arguments = new PHPUnit_Extensions_Database_UI_Modes_ExportDataSet_Arguments($modeArguments);

        if (FALSE && !$arguments->areValid()) {
            throw new InvalidArgumentException('The arguments for this command are incorrect.');
        }

        $datasets = [];
        foreach ($arguments->getArgumentArray('dataset') as $argString) {
            $datasets[] = $this->getDataSetFromArgument($argString, $arguments->getDatabases());
        }

        $finalDataset = new PHPUnit_Extensions_Database_DataSet_CompositeDataSet($datasets);

        $outputDataset = $this->getPersistorFromArgument($arguments->getSingleArgument('output'));
        $outputDataset->write($finalDataset);
    }

    /**
     * Returns the correct dataset given an argument containing a dataset spec.
     *
     * @param  string                                       $argString
     * @param  array                                        $databaseList
     * @return PHPUnit_Extensions_Database_DataSet_IDataSet
     */
    protected function getDataSetFromArgument($argString, $databaseList)
    {
        $dataSetSpecFactory          = new PHPUnit_Extensions_Database_DataSet_Specs_Factory();
        list($type, $dataSetSpecStr) = explode(':', $argString, 2);
        $dataSetSpec                 = $dataSetSpecFactory->getDataSetSpecByType($type);

        if ($dataSetSpec instanceof PHPUnit_Extensions_Database_IDatabaseListConsumer) {
            $dataSetSpec->setDatabases($databaseList);
        }

        return $dataSetSpec->getDataSet($dataSetSpecStr);
    }

    /**
     * Returns the correct persistor given an argument containing a persistor spec.
     *
     * @param  string                                           $argString
     * @return PHPUnit_Extensions_Database_DataSet_IPersistable
     */
    protected function getPersistorFromArgument($argString)
    {
        $persistorFactory  = new PHPUnit_Extensions_Database_DataSet_Persistors_Factory();
        list($type, $spec) = explode(':', $argString, 2);

        return $persistorFactory->getPersistorBySpec($type, $spec);
    }
}

