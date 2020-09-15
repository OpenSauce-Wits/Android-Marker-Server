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

    public function testRowCount()
    {

        $this->assertSame(2, $this->getConnection()->getRowCount('ams_emulators'), 'Pre-Condition');
    }

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
        $row_count = $this->getConnection()->getRowCount($this->table_name);
        for ($x = 0; $x < $row_count; $x++) {
            $this->assertEquals($table->getValue($x,"emulator_id"),$records[$x]['emulator_id']);
        }


    }
    /**
     * Tip
     * Travis and Coverage
     * https://github.com/codecov/example-php/blob/master/.travis.yml
     */
}