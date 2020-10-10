<?php

namespace androidMarker\Test;

use App\Calculator\Division;
use PHPUnit\Framework\TestCase;
use PHPUnit\DbUnit\TestCaseTrait;
use PHPUnit\Framework\Error\Error;
use androidMarker\DatabaseHelper;

include __DIR__ . DIRECTORY_SEPARATOR . "../config.php";

class DatabaseHelperTest extends TestCase
{
    use TestCaseTrait;

    // Instantiate pdo once for test clean-up / fixture loda
    static private $pdo = null;

    // Instantiate once per test
    private $conn = null;

    public function setUp(): void
    {
        global $CFG;
        $this->dbhost = $CFG->dbhost;
        $this->dbuser = $CFG->dbuser;
        $this->dbpass = $CFG->dbpass;
        $this->dbname = $CFG->dbname;
        $this->tableName = "ams_emulators";
        $this->fixtures = __DIR__ . DIRECTORY_SEPARATOR . "fixtures" . DIRECTORY_SEPARATOR . "ams_emulators_fixture.xml";
        $this->expected = __DIR__ . DIRECTORY_SEPARATOR . "fixtures" . DIRECTORY_SEPARATOR . "ams_emulators_expected.xml";
        $this->databaseValue = array('id' => 3, 'emulator_id' => "ABCA4C15903451915", 'state' => 'device', 'in_use' => 'false');
    }


    final public function getConnection()
    {
        // TODO: Implement getConnection() method.
        if ($this->conn === null) {
            if (self::$pdo == null) {
                self::$pdo = new \PDO($GLOBALS['DB_DSN'], $GLOBALS['DB_USER'], $GLOBALS['DB_PASSWD']);
            }
            $this->conn = $this->createDefaultDBConnection(self::$pdo, $GLOBALS['DB_DBNAME']);
        }
        return $this->conn;
    }

    protected function getDataSet()
    {
        // TODO: Implement getDataSet() method.
        return $this->createFlatXMLDataSet($this->fixtures);
    }

    public function testConnectionMessageReturns()
    {

        $database = new \androidMarker\DatabaseHelper($this->dbhost, $this->dbuser, $this->dbpass, $this->dbname);
        $this->assertEquals("Success", $database->getConnectionMessage());
        $this->assertNotEquals("Failure", $database->getConnectionMessage());
    }

    public function testException()
    {
        $this->expectException(Error::class);
        $database2 = new DatabaseHelper($this->dbhost, $this->dbuser, "123", $this->dbname);
        $message = $database2->getConnectionMessage();
        echo $message;
    }

    public function testInsertRecordWorks()
    {
        $database = new DatabaseHelper($this->dbhost, $this->dbuser, $this->dbpass, $this->dbname);
        $result = $database->insert_record('ams_emulators', $this->databaseValue);
        $queryTable = $this->getConnection()->createQueryTable($this->tableName, "SELECT * FROM ams_emulators");
        $expectedTable = $this->createFlatXMLDataSet($this->expected)->getTable($this->tableName);
        $countResultDatabase = $database->count_records($this->tableName, null);
        $countExpectedTable = $this->getConnection()->getRowCount($this->tableName);
        $this->assertTablesEqual($expectedTable, $queryTable);
        $this->assertEquals($countExpectedTable, $countResultDatabase);
        $database->delete_records($this->tableName, array("id" => 3));

    }

    public function testGetRecordWorks()
    {
        $database = new DatabaseHelper($this->dbhost, $this->dbuser, $this->dbpass, $this->dbname);
        $records = $database->get_record("ams_emulators");
        $table = $this->createFlatXMLDataSet($this->expected)->getTable("ams_emulators");
        $this->assertEquals($table->getValue(0, 'emulator_id'), $records[0]['emulator_id']);
        $this->assertEquals($table->getValue(1, 'emulator_id'), $records[1]['emulator_id']);
    }


}