<?php

//namespace App\Database;
require_once(__DIR__ . "/../../db/config.php");

class DatabaseHelper
{
    private $mysqli;
    private $connection_message;

    public function __construct()
    {
        global $CFG;
        $this->mysqli = new mysqli($CFG->dbhost, $CFG->dbuser, $CFG->dbpass, $CFG->dbname);
        if ($this->mysqli->connect_errno) {
            echo "Failed to connect to MySQL: " . $this->mysqli->connect_error;
            $this->connection_message = "Failure";
            exit();
        } else {
            $this->connection_message = "Success";
        }
    }

    private function add_args($sql, $Args = NULL)
    {
        if (!is_null($Args)) {
            $Args = (array)$Args;
            $sql .= " WHERE ";
            $moreThanOne = false;
            foreach ($Args as $key => $value) {
                if ($moreThanOne) {
                    $sql .= " AND $key='$value'";
                    continue;
                }
                $sql .= "$key='$value'";
                $moreThanOne = true;
            }
        }
        return $sql;
    }

    public function get_record($table, $Args = NULL)
    {
        $sql = "SELECT * FROM $table";
        $sql = $this->add_args($sql, $Args);
        $result = mysqli_query($this->mysqli, $sql);
        if (!$result) {
            die("Database access failed: " . mysqli_error());
            //output error message if query execution failed
        }
        $rows = mysqli_num_rows($result);
        $resultArray = array();
        if ($rows) {
            $count = 0;
            while ($row = mysqli_fetch_array($result)) {
                // removes integer keys from row results.
                foreach ($row as $key => $value) {
                    if ($key == $count) {
                        unset($row[$key]);
                        ++$count;
                    }
                }
                // stores data with string keys only
                array_push($resultArray, $row);
            }
        }
        return $resultArray;
    }

    public function insert_record($table, $Args)
    {
        $keys = "";
        $values = "";
        $Args = (array)$Args;
        if (!is_null($Args)) {
            $moreThanOne = false;
            foreach ($Args as $key => $value) {
                if ($moreThanOne) {
                    $keys .= ",$key";
                    $values .= ",'$value'";
                    continue;
                }
                $keys .= "$key";
                $values .= "'$value'";
                $moreThanOne = true;
            }
        }
        echo "\n";
        print_r($keys);
        echo "\n";
        echo "\n";
        print_r($values);
        echo "\n";
        echo "\n";
        $sql = "INSERT INTO $table ($keys) VALUES($values)";
        $result = mysqli_query($this->mysqli, $sql);
        if (!$result) {
//            die("Adding record failed: " . mysqli_error());
            echo "There was a problem \n";
            return false;
        }
        // Returns the row that has just been inserted
        return $this->get_record($table, $Args)[0];
    }


    public function get_connection_message()
    {
        return $this->connection_message;
    }
}

$db = new DatabaseHelper();
$result = $db->insert_record("ams_emulators", "1 JHSDAFAN4154252 device false");
echo $result;
