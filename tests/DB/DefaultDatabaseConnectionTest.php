<?php
/*
 * This file is part of DbUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
use PHPUnit\DbUnit\Database\DefaultConnection;
use PHPUnit\Framework\TestCase;

class DefaultDatabaseConnectionTest extends TestCase
{
    protected $db;

    protected function setUp(): void
    {
        $this->db = new PDO('sqlite::memory:');
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->db->exec('CREATE TABLE test (field1 VARCHAR(100))');
    }

    public function testRowCountForEmptyTableReturnsZero(): void
    {
        $conn = new DefaultConnection($this->db);
        $this->assertEquals(0, $conn->getRowCount('test'));
    }

    public function testRowCountForTableWithTwoRowsReturnsTwo(): void
    {
        $this->db->exec('INSERT INTO test (field1) VALUES (\'foobar\')');
        $this->db->exec('INSERT INTO test (field1) VALUES (\'foobarbaz\')');

        $conn = new DefaultConnection($this->db);
        $this->assertEquals(2, $conn->getRowCount('test'));
    }
}
