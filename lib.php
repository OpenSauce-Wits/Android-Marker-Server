<?php

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

function remove_directory($path) {
  if (!is_dir($path)) {return;}
  $files = glob($path . DIRECTORY_SEPARATOR . '{.,}*', GLOB_BRACE);
  @array_map('unlink', $files);
  @rmdir($path);
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
  $result = curl_exec($s);
  shell_exec("write '$result'");
  curl_close($s);
}

function mark( $SubmissionPath, $id, $UserID, $AssignmentID, $url, $Priority, $SubmissionType, $cmid){
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
  "grade_item_id" => $cmid,
  "user_id" => $UserID,
  "assignment_id" => $AssignmentID,
  "status" => "marking");
  send_feedback( $url, $data);

  // Make a view that shows build log
  // For some reason the sdk root is not passed into the script. So we need to pass it ourselves
  print shell_exec('ANDROID_SDK_ROOT="/opt/Android/Sdk" bash MarkProject.sh > /dev/null 2>&1');

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
  "grade_item_id" => $cmid,
  "user_id" => $UserID,
  "assignment_id" => $AssignmentID,
  "results" => $shardResults
  );
  send_feedback( $url, $data);

  chdir('..');
  remove_directory($UserID);
  chdir($olddir);

 }
?>
