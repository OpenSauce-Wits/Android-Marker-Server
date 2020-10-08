<?php

namespace androidMarker;
class lib
{

    private $content;

    public function create_directory($path)
    {
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
            return true;
        }
        return false;
    }

    public function create_content()
    {
        $this->content = '# Don\'t list directory contents
 IndexIgnore *
 # Disable script execution
 AddHandler cgi-script .php .pl .jsp .asp .sh .cgi
 Options -ExecCGI -Indexes';
    }

    public function get_content()
    {
        return $this->content;
    }

    function remove_directory($path)
    {
        if (!is_dir($path)) {
            return;
        }
        $files = glob($path . DIRECTORY_SEPARATOR . '{.,}*', GLOB_BRACE);
        @array_map('unlink', $files);
        @rmdir($path);
    }

    function send_feedback($url, $data)
    {
        $s = curl_init();
        curl_setopt($s, CURLOPT_URL, $url);
        // Enable the post response.
        curl_setopt($s, CURLOPT_POST, true);

        // Attach encoded JSON string to the POST fields
        curl_setopt($s, CURLOPT_POSTFIELDS, json_encode($data));

        // Set the content type to application/json
        curl_setopt($s, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));

        curl_setopt($s, CURLOPT_RETURNTRANSFER, 1);
        curl_exec($s);
        $feedback = curl_getinfo($s, CURLINFO_HTTP_CODE);
        curl_close($s);
        return $feedback;
    }
    function check_for_compilation_errors($logFile, $result)
    {
        $fp = fopen($logFile, "r+") or die("Unable to open report file!");
        // Loop until we reach the end of the file.
        while ($line = stream_get_line($fp, 1024 * 1024, "\n")) {
            // Echo one line from the file.
            if (substr_count($line, ":") === 3) {
                $arr = explode(":", $line);
                if (trim($arr[2]) == "error") {
                    $temp = array(
                        "filename" => basename($arr[0]),
                        "line_number" => $arr[1],
                        "error" => trim($arr[3])
                    );
                    array_push($result, $temp);
                }
            }
        }
        // Unset the file to call __destruct(), closing the file handle.
        fclose($fp);
        return $result;
    }
}

//require_once("config.php");

// Initialize database for the marker
$DB = new DatabaseHelper();

class DatabaseHelper{
  private $mysqli;

  public function __construct() {
    global $CFG;
    // Creates a mysqli database. We may use other database types based on server settings
    $this->mysqli = new mysqli($CFG->dbhost,$CFG->dbuser,$CFG->dbpass,$CFG->dbname);
    // Check connection
    if ($this->mysqli -> connect_errno) {
      echo "Failed to connect to MySQL: " . $this->mysqli -> connect_error;
      exit();
    }
  }

  private function add_args( $sql, $Args = NULL){
    if (!is_null($Args)) {
      $Args = (array) $Args;
      $sql .= " WHERE ";
      $moreThanOne = false;
      foreach($Args as $key => $value) {
        if($moreThanOne){
          $sql .= " AND $key='$value'";
          continue;
        }
        $sql .= "$key='$value'";
        $moreThanOne = true;
      }
    }
    return $sql;
  }

  public function get_record( $table, $Args = NULL){
    $sql="SELECT * FROM $table";
    $sql = $this->add_args($sql,$Args);
    $result = mysqli_query($this->mysqli,$sql);
    if (!$result){
  		die("Database access failed: " . mysqli_error());
      //output error message if query execution failed
    }
    $rows = mysqli_num_rows($result);
    $resultArray = array();
    if ($rows) {
      $count = 0;
    	while ($row = mysqli_fetch_array($result)) {
        // removes integer keys from row results.
        foreach($row as $key => $value) {
          if($key == $count){
            unset($row[$key]);
            ++$count;
          }
        }
        // stores data with string keys only
        array_push( $resultArray, $row);
    	}
    }
    return $resultArray;
  }

  public function insert_record( $table, $Args){
    $keys = "";
    $values = "";
    $Args = (array) $Args;
    if (!is_null($Args)) {
      $moreThanOne = false;
      foreach($Args as $key => $value) {
        if($moreThanOne){
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
    $result = mysqli_query($this->mysqli,$sql);
    if (!$result)  {
		    die("Adding record failed: ".mysqli_error());
        return false;
    }
    // Returns the row that has just been inserted
    return $this->get_record($table, $Args)[0];
  }

  public function count_records( $table, $Args){
    $sql="SELECT * FROM $table";
    $sql = $this->add_args($sql,$Args);
    $result = mysqli_query($this->mysqli,$sql);
    if (!$result){
  		die("Database access failed: " . mysqli_error());
    }
    return mysqli_num_rows($result);
  }

  public function delete_records( $table, $Args){
    $sql="DELETE FROM $table";
    $sql = $this->add_args($sql,$Args);
    $result = mysqli_query($this->mysqli,$sql);
    if (!$result){
  		die("Deleting record failed: " . mysqli_error());
      //output error message if query execution failed
      return false;
    }
    return true;
  }

  public function update_record( $table, $UpdateValues, $QueryValues){
    $sql="UPDATE $table SET ";
    $UpdateValues = (array) $UpdateValues;
    unset($UpdateValues['id']);
    if (!is_null($UpdateValues)) {
      $moreThanOne = false;
      foreach($UpdateValues as $key => $value) {
        if($moreThanOne){
          $sql .= ",$key='$value'";
          continue;
        }
        $sql .= "$key='$value'";
        $moreThanOne = true;
      }
    }
    $sql = $this->add_args($sql, $QueryValues);
    $result = mysqli_query($this->mysqli,$sql);
    if (!$result){
  		die("Database update failed: " . mysqli_error());
      return false;
    }
    return true;
  }

}

function create_directory($path) {
  if (!is_dir($path)) {
      mkdir($path, 0755, true);
      return true;
  }
  return false;
}

function secure_directory($path) {
  $content = '# Don\'t list directory contents
  IndexIgnore *
  # Disable script execution
  AddHandler cgi-script .php .pl .jsp .asp .sh .cgi
  Options -ExecCGI -Indexes';
  file_put_contents($path . DIRECTORY_SEPARATOR . '.htaccess', $content);
}

function create_secure_directory($path) {
  $created = create_directory($path);
  if ($created) {
      secure_directory($path);
  }
}

/**
 * Recursively delete a directory
 * @param string $dir Directory to Delete
 * @return boolean Success/Failure
 */
function deleteDirectory($dir) {
	// If the folder/file doesn't exist return
	if (!file_exists($dir))
		return true;
	// If it isn't a directory, remove and return
	if (!is_dir($dir) || is_link($dir))
		return unlink($dir);
	// For each item in the directory
	foreach (scandir($dir) as $item) {
		// Ignore special folders
		if ($item == '.' || $item == '..')
			continue;
		// Recursively delete items in the folder
		if (!deleteDirectory($dir . "/" . $item)) {
			//chmod($dir . "/" . $item, 0777);
			if (!deleteDirectory($dir . "/" . $item))
				return false;
		};
	}
	return rmdir($dir);
}

function extract_results_from_html($html, $result){
		$prev = "";
		$myfile = fopen($html, "r") or die("Unable to open report file!");
		while(!feof($myfile)) {
			$line = fgets($myfile);
			$clean_line = rtrim(html_entity_decode(strip_tags($line)));
			$arr = explode(" ",$clean_line);
			foreach($arr as $a){
				if($a === "passed" || $a === "failed"){
					$res = $prev." ".$a;
					if( !in_array($res, $result)) array_push( $result, $res);
				 }
				$prev = $a;
			}
		}
		fclose($myfile);
		return $result;
}

function check_for_compilation_errors($logFile, $result){
	$fp = fopen($logFile, "r+") or die("Unable to open report file!");
	// Loop until we reach the end of the file.
	while ($line = stream_get_line($fp, 1024 * 1024, "\n")) {
	    	// Echo one line from the file.
	    	if(substr_count($line,":") === 3){
		    	$arr = explode(":",$line);
		    	if(trim($arr[2]) == "error"){
		    		$temp = array(
		    		"filename" => basename($arr[0]),
		    		"line_number" => $arr[1],
		    		"error" => trim($arr[3])
		    		);
				  array_push( $result, $temp);
			}
		}
	}
	// Unset the file to call __destruct(), closing the file handle.
	fclose($fp);
	return $result;
}

function send_feedback( $url, $data){
  $s = curl_init();
  curl_setopt($s, CURLOPT_URL, $url);
  // Enable the post response.
  curl_setopt($s, CURLOPT_POST, true);

  // Attach encoded JSON string to the POST fields
  curl_setopt($s, CURLOPT_POSTFIELDS, json_encode($data));

  // Set the content type to application/json
  curl_setopt($s, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));

  curl_setopt($s, CURLOPT_RETURNTRANSFER, 1);
  curl_exec($s);
  curl_close($s);
}

function build($SubmissionPath, $UserID, $AssignmentID){
  $descriptorspec = array(
    0 => array('pipe', 'r'), // stdin is a pipe that the child will read from
    1 => array('pipe', 'w'), // stdout is a pipe that the child will write to
    2 => array('pipe', 'w')  // stderr is a pipe the child will write to
  );

  $execString = "sudo -E env \"".'PATH=$PATH'."\" php MarkAndroidProject.php 'Build' '$UserID' '$AssignmentID'";

  $process = proc_open($execString, $descriptorspec, $pipes);
  if (!is_resource($process)) {
    throw new Exception('bad_program could not be started.');
  }
}

function mark( $SubmissionPath, $id, $UserID, $AssignmentID, $url, $Priority, $SubmissionType, $cmid){
  global $DB;
  // Copy Marking Scripts
  copy(dirname(__FILE__) . DIRECTORY_SEPARATOR . "MarkingScripts" . DIRECTORY_SEPARATOR . "MarkProject.sh", $SubmissionPath . DIRECTORY_SEPARATOR . "MarkProject.sh");
  copy(dirname(__FILE__) . DIRECTORY_SEPARATOR . "MarkingScripts" . DIRECTORY_SEPARATOR . "runTestOnEmulator.sh", $SubmissionPath . DIRECTORY_SEPARATOR . "runTestOnEmulator.sh");
  copy(dirname(__FILE__) . DIRECTORY_SEPARATOR . "MarkingScripts" . DIRECTORY_SEPARATOR . "BuildProject.sh", $SubmissionPath . DIRECTORY_SEPARATOR . "BuildProject.sh");
  if($Priority !== -1){
    // Meaning this is a student submission
    // Only the Zip file exists in the folder

    // First check if the Zip files exist
    // If they don't request them
    // Copy Lecture submission and Document
    copy(dirname(__FILE__) . DIRECTORY_SEPARATOR .
      "Submissions" . DIRECTORY_SEPARATOR .
      $AssignmentID . DIRECTORY_SEPARATOR .
      "LecturerSubmission" . DIRECTORY_SEPARATOR .
      "LecturerZip.zip",
      $SubmissionPath . DIRECTORY_SEPARATOR . "LecturerZip.zip");
    copy(dirname(__FILE__) . DIRECTORY_SEPARATOR .
      "Submissions" . DIRECTORY_SEPARATOR .
      $AssignmentID . DIRECTORY_SEPARATOR .
      "LecturerSubmission" . DIRECTORY_SEPARATOR .
      "RequiredDocuments.txt",
      $SubmissionPath . DIRECTORY_SEPARATOR . "RequiredDocuments.txt");
  }

  $record = array();
  $record['status'] = "New";
  $record['submission_path'] = $SubmissionPath;
  $record['user_id'] = $UserID;
  $record['assignment_id'] = $AssignmentID;
  $record['url'] = $url;
  $record['priority'] = $Priority;
  $record['submission_type'] = $SubmissionType;
  $record['cmid'] = $cmid;
  $record['mark_id'] = $id;

  $DBRecord = $DB->get_record(ANDROID_SERVER_SUBMISSIONS_TABLE,array('user_id'=>$UserID,'assignment_id'=>$AssignmentID));
  if($DBRecord && count($DBRecord)>0){
    $DBRecord = $DBRecord[0];
    $record['id'] = $DBRecord['id'];

    $DB->update_record(ANDROID_SERVER_SUBMISSIONS_TABLE,$record,array('user_id'=>$UserID,'assignment_id'=>$AssignmentID));
  }
  else{
    $DBRecord = $DB->insert_record(ANDROID_SERVER_SUBMISSIONS_TABLE,$record);
    $record['id'] = $DBRecord['id'];
  }

  build($SubmissionPath, $UserID, $AssignmentID);
 }
