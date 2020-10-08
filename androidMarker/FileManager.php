<?php

namespace androidMarker;
include 'config.php';
/**
 * @code
 */
include_once('DatabaseHelper.php');

class FileManager
{
    private $database;
    private $mysqli;
    private $connectionMessage;
    private $table;
    private $jsonName;

    public function __construct($dbhost, $dbuser, $dbpass, $dbname,$dbtable,$jsonName)
    {
        $this->database = new DatabaseHelper($dbhost, $dbuser, $dbpass, $dbname);
        $this->mysqli = new \mysqli($dbhost, $dbuser, $dbpass, $dbname);
        $this->table = $dbtable;
        $this->jsonName = $jsonName;
        $this->connectionMessage = $this->database->getConnectionMessage();
    }

    public function getConnectionMessage()
    {
        return $this->connectionMessage;
    }

    public function getJSONName()
    {
        return $this->jsonName;
    }
    public function getTable(){
        return $this->table;
    }

    public function generateEncodedJSON()
    {
        $statement = $this->mysqli->prepare("SELECT * FROM {$this->getTable()}");
        $statement->execute();
        $result = $statement->get_result();
        $json_array = array();
        while ($r = $result->fetch_assoc()) {
            $json_array[] = $r;
        }
        return json_encode($json_array);
    }

    public function createJSONFile($path=null)
    {
        $pathname = ($path==null) ? $this->getJSONName() : dirname(__FILE__).DIRECTORY_SEPARATOR.$path.DIRECTORY_SEPARATOR.$this->getJSONName();
        if(file_exists($pathname)){
            unlink($pathname);
        }
        $fp = fopen("{$pathname}", 'w');
        fwrite($fp, $this->generateEncodedJSON());
        fclose($fp);
    }

}

?>

