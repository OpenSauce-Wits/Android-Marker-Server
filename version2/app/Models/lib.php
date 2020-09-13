<?php

namespace App\Models;
class lib{
  public $path;
  public $content;
  public $feedback_status;

  public function create_directory($path){
    $this->path = $path;
    if(!is_dir($path)){
      mkdir($path,0755,true);
    }
    return false;
  }

  public function get_directory(){
    return $path;
  }

  public function create_content(){
    $this->content =  '# Don\'t list directory contents
 IndexIgnore *
 # Disable script execution
 AddHandler cgi-script .php .pl .jsp .asp .sh .cgi
 Options -ExecCGI -Indexes';

  }

  public function get_content(){
    return $this->content;
  }

  public function secure_directory(){
      file_put_contents($this->path . DIRECTORY_SEPARATOR . '.htaccess', $this->get_content());
  }
  public function get_secure_directoty(){
    return $this->path.DIRECTORY_SEPARATOR.'.htaccess';
  }
  function remove_directory($path) {
    if (!is_dir($path)) {return;}
    $files = glob($path . DIRECTORY_SEPARATOR . '{.,}*', GLOB_BRACE);
    @array_map('unlink', $files);
    @rmdir($path);
  }


  public function send_feedback( $url, $data){
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
  $this->feedback_status = curl_getinfo($s,CURLINFO_HTTP_CODE);
  curl_close($s);
}


public function check_for_compilation_errors($logFile, $result){
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

public function mark( $SubmissionPath, $id, $UserID, $AssignmentID, $url, $Priority, $SubmissionType, $cmid){
  // Copy Marking Scripts
  copy(dirname(__FILE__) . DIRECTORY_SEPARATOR . "MarkingScripts" . DIRECTORY_SEPARATOR . "MarkProject.sh", $SubmissionPath . DIRECTORY_SEPARATOR . "MarkProject.sh");
  copy(dirname(__FILE__) . DIRECTORY_SEPARATOR . "MarkingScripts" . DIRECTORY_SEPARATOR . "runTestOnEmulator.sh", $SubmissionPath . DIRECTORY_SEPARATOR . "runTestOnEmulator.sh");
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

  $olddir = getcwd();
  chdir($SubmissionPath);

  // Update the Assignment Submission record
  $data = array("feedbacktype" => "UpdateStatus",
  "id" => $id,
  "grade" => $cmid,
  "userid" => $UserID,
  "assignment" => $AssignmentID,
  "status" => "marking");
  send_feedback( $url, $data);

  // Make a view that shows build log
  // For some reason the sdk root is not passed into the script. So we need to pass it ourselves
  shell_exec('bash MarkProject.sh > /dev/null 2>&1');
  $errors = check_for_compilation_errors("log.txt", array());
  if(sizeof($errors) !== 0){
    // Update the Assignment Submission record
    $data = array("feedbacktype" => "UpdateMark",
    "submissiontype" => $SubmissionType,
    "id" => $id,
    "grade" => $cmid,
    "userid" => $UserID,
    "assignment" => $AssignmentID,
    "results" => $errors,
    "resulttype" => "errors"
    );
  }
  else{
    // Stores the results from all the shards in an array
    $shardResults = array();
    $shardCount = 0;
    while(is_dir("$shardCount")){
      $shardResults = extract_results_from_html( "$shardCount/report.html", $shardResults);
      remove_directory("$shardCount");
      $shardCount+=1;
    }

    // Update the Assignment Submission record
    $data = array("feedbacktype" => "UpdateMark",
    "submissiontype" => $SubmissionType,
    "id" => $id,
    "grade" => $cmid,
    "userid" => $UserID,
    "assignment" => $AssignmentID,
    "results" => $shardResults,
    "resulttype" => "tests"
    );
  }
  send_feedback( $url, $data);

  chdir('..');
  remove_directory($UserID);
  chdir($olddir);
 }

}

?>
