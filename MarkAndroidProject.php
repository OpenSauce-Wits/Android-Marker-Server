<?php
  require_once("lib.php");    // Include Library Functions

  $MarkType = $argv[1];
  $UserID = $argv[2];
  $AssignmentID = $argv[3];

  $record = $DB->get_record(ANDROID_SERVER_SUBMISSIONS_TABLE,array('user_id'=>$UserID,'assignment_id'=>$AssignmentID));
  if($record && count($record)>0){
    $record = $record[0];

    chdir($record['submission_path']);
    if($MarkType == "Build"){
      // Make a view that shows build log
      // For some reason the sdk root is not passed into the script. So we need to pass it ourselves
      shell_exec('bash BuildProject.sh');

      $record['status'] = "Built";
			$DB->update_record(ANDROID_SERVER_SUBMISSIONS_TABLE,$record,array('user_id'=>$UserID,'assignment_id'=>$AssignmentID));

      $errors = check_for_compilation_errors("log.txt", array());
      if(sizeof($errors) !== 0){
        print_r($errors);
      }
    }
    else if($MarkType == "Mark"){
      // Make a view that shows build log
      // For some reason the sdk root is not passed into the script. So we need to pass it ourselves

      // Update the Assignment Submission record
      $data = array("feedbacktype" => "UpdateStatus",
      "id" => $record['mark_id'],
      "grade" => $record['cmid'],
      "userid" => $record['user_id'],
      "assignment" => $record['assignment_id'],
      "status" => "marking");
      send_feedback( $record['url'], $data);

      $record['status'] = "Marking";
			$DB->update_record(ANDROID_SERVER_SUBMISSIONS_TABLE,$record,array('user_id'=>$UserID,'assignment_id'=>$AssignmentID));

      shell_exec("bash MarkProject.sh '".$argv[4]."'");

      $record['status'] = "Marked";
			$DB->update_record(ANDROID_SERVER_SUBMISSIONS_TABLE,$record,array('user_id'=>$UserID,'assignment_id'=>$AssignmentID));

      $emulatorArray = array();
      $emulatorArray['emulator_id'] = $argv[4];
      $emulatorArray['in_use'] = "false";
			$DB->update_record(ANDROID_SERVER_EMULATORS_TABLE,$emulatorArray,array('emulator_id'=>$emulatorArray['emulator_id']));

      $errors = check_for_compilation_errors("log.txt", array());
      if(sizeof($errors) !== 0){
        // Update the Assignment Submission record
        $data = array("feedbacktype" => "UpdateMark",
        "submissiontype" => $record['submission_type'],
        "id" => $record['mark_id'],
        "grade" => $record['cmid'],
        "userid" => $record['user_id'],
        "assignment" => $record['assignment_id'],
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
          deleteDirectory("$shardCount");
          $shardCount+=1;
        }
        // Update the Assignment Submission record
        $data = array("feedbacktype" => "UpdateMark",
        "submissiontype" => $record['submission_type'],
        "id" => $record['mark_id'],
        "grade" => $record['cmid'],
        "userid" => $record['user_id'],
        "assignment" => $record['assignment_id'],
        "results" => $shardResults,
        "resulttype" => "tests"
        );
      }
      send_feedback( $record['url'], $data);

      chdir("..");
      deleteDirectory($UserID);
    }
  }
?>
