<?php

namespace androidMarker;
class DatabaseHelper
{
    private $connection_message;
    private $mysqli;

    public function __construct($dbhost, $dbuser, $dbpass, $dbname)
    {
        try{
            $this->mysqli = new \mysqli($dbhost, $dbuser, $dbpass, $dbname);
            $this->connection_message = "Success";
        }catch(Exception $e){
            $this->connection_message = "Failure";
            throwException(Error::class);
        }

    }

    /**
     * @param $sql
     * @param null $Args
     * @return string
     * @codeCoverageIgnore
     */
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

    /**
     * @param $table
     * @param $Args
     * @return false|mixed
     */
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
        $sql = "INSERT INTO $table ($keys) VALUES($values)";
        $result = mysqli_query($this->mysqli, $sql);
        if (!$result) {
//            die("Adding record failed: " . mysqli_error());
            return false;
        }
        // Returns the row that has just been inserted
        return $this->get_record($table, $Args)[0];
    }

    /**
     * @param $table
     * @param null $Args
     * @return array
     */
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


    public function count_records($table, $Args)
    {
        $sql = "SELECT * FROM $table";
        $sql = $this->add_args($sql, $Args);
        $result = mysqli_query($this->mysqli, $sql);
        if (!$result) {
//            die("Database access failed: " . mysqli_error());
        }
        return mysqli_num_rows($result);
    }

    /**
     * @param $table
     * @param $Args
     * @return bool
     */
    public function delete_records($table, $Args)
    {
        $sql = "DELETE FROM $table";
        $sql = $this->add_args($sql, $Args);
        $result = mysqli_query($this->mysqli, $sql);
        if (!$result) {
//            die("Deleting record failed: " . mysqli_error());
            //output error message if query execution failed
            echo "Deletion Failed";
            return false;
        }
        return true;
    }

    public function getConnectionMessage()
    {
        return $this->connection_message;
    }
}






