<?php

use PHPUnit\Framework\TestCase;
use PHPUnit\DbUnit\TestCaseTrait;

require_once(__DIR__ . "/../../app/Database/DatabaseHelper.php");

class developerHelperTest extends TestCase
{

    use TestCaseTrait;

    // Instantiate pdo once for test clean-up / fixture loda
    static private $pdo = null;

    // Instantiate once per test
    private $conn = null;

    public function setUp(): void
    {
        parent::setUp();
        $this->table_name = "ams_emulators";
        $this->xml_file = "./tests/fixtures/ams_emulators_fixture.xml";
    }

    final public function getConnection()
    {
        // TODO: Implement getConnection() method.
        if ($this->conn === null) {
            if (self::$pdo == null) {
                self::$pdo = new PDO($GLOBALS['DB_DSN'], $GLOBALS['DB_USER'], $GLOBALS['DB_PASSWD']);
            }
            $this->conn = $this->createDefaultDBConnection(self::$pdo, $GLOBALS['DB_DBNAME']);
        }
        return $this->conn;
    }

    protected function getDataSet()
    {
        // TODO: Implement getDataSet() method.
        return $this->createFlatXMLDataSet('./tests/fixtures/ams_emulators_fixture.xml');
    }

//    public function testRowCount()
//    {
//
//        $this->assertSame(3, $this->getConnection()->getRowCount('ams_emulators'), 'Pre-Condition');
//    }

    /** @test */
    public function check_that_adding_emulators_functions()
    {
        $database = new DatabaseHelper();
        $this->assertEquals("Success", $database->get_connection_message());
    }

    /** @test */
    public function check_that_get_record_functions()
    {
        $database = new DatabaseHelper();
        $records = $database->get_record($this->table_name);
        $table = $this->createFlatXMLDataSet($this->xml_file)->getTable($this->table_name);
        $this->assertEquals($table->getValue(0, 'emulator_id'), $records[0]['emulator_id']);
        $this->assertEquals($table->getValue(1, 'emulator_id'), $records[1]['emulator_id']);

    }

    /** @test */
    public function check_that_insert_record_functions()
    {
        $database = new DatabaseHelper();
        $result = $database->insert_record('ams_emulators', array('id' => 3, 'emulator_id' => "ABCA4C15903451915", 'state' => 'device', 'in_use' => 'false'));
        $queryTable = $this->getConnection()->createQueryTable('ams_emulators', "SELECT * FROM ams_emulators");
        $expectedTable = $this->createFlatXMLDataSet("./tests/fixtures/ams_emulators_expected.xml")->getTable('ams_emulators');
        $this->assertTablesEqual($expectedTable, $queryTable);

    }

    /** @test */
    public function check_that_record_deleted()
    {
        $database = new DatabaseHelper();
        $result = $database->delete_records('ams_emulators', array('id' => 3));
        $queryTable = $this->getConnection()->createQueryTable("ams_emulators", "SELECT * FROM ams_emulators");
        $expectedTable = $this->createFlatXMLDataSet("./tests/fixtures/ams_emulators_fixture.xml")->getTable('ams_emulators');
        $this->assertTablesEqual($expectedTable, $queryTable);
    }

    /** @test */
    public function check_that_record_updated()
    {
        $database = new DatabaseHelper();
        $result = $database->delete_records('ams_emulators', array('id' => 3));
        $queryTable = $this->getConnection()->createQueryTable("ams_emulators", "SELECT * FROM ams_emulators");
        $expectedTable = $this->createFlatXMLDataSet("./tests/fixtures/ams_emulators_fixture.xml")->getTable('ams_emulators');
        $this->assertTablesEqual($expectedTable, $queryTable);
    }
    /**
     * Tip
     * Travis and Coverage
     * https://github.com/codecov/example-php/blob/master/.travis.yml
     */
}