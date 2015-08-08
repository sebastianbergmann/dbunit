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
 * A yaml dataset persistor
 *
 * @since      Class available since Release 1.0.0
 */
class PHPUnit_Extensions_Database_DataSet_Persistors_Yaml implements PHPUnit_Extensions_Database_DataSet_IPersistable
{
    /**
     * @var string
     */
    protected $filename;

    /**
     * Sets the filename that this persistor will save to.
     *
     * @param string $filename
     */
    public function setFileName($filename)
    {
        $this->filename = $filename;
    }

    /**
     * Writes the dataset to a yaml file
     *
     * @param PHPUnit_Extensions_Database_DataSet_IDataSet $dataset
     */
    public function write(PHPUnit_Extensions_Database_DataSet_IDataSet $dataset)
    {
        $phpArr      = [];
        $emptyTables = [];

        foreach ($dataset as $table) {
            $tableName          = $table->getTableMetaData()->getTableName();
            $rowCount           = $table->getRowCount();

            if (!$rowCount) {
                $emptyTables[] = $tableName;
                continue;
            }

            $phpArr[$tableName] = [];

            for ($i = 0; $i < $rowCount; $i++) {
                $phpArr[$tableName][] = $table->getRow($i);
            }
        }

        $emptyTablesAsString = '';

        if (count($emptyTables)) {
            $emptyTablesAsString = implode(":\n", $emptyTables) . ":\n\n";
        }

        file_put_contents(
          $this->filename,
          Symfony\Component\Yaml\Yaml::dump($phpArr, 3) . $emptyTablesAsString
        );
    }
}
