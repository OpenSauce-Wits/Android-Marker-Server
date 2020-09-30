<?php
  require_once("lib.php");    // Include Library Functions
  $inputJSON = file_get_contents('php://input');  // Get input from the client
  $input = json_decode($_POST['json'], true);        // Decode the JSON object
  $RequestTypeType = $input['RequestType'];

  if($RequestTypeType == "Submissions"){
    $Records = $DB->get_record(ANDROID_SERVER_SUBMISSIONS_TABLE);
    $json = array();
    foreach($Records as $AvailableEmulatorsKey => $emulator){
      $json[] = array(
        'User ID' => $emulator['user_id'],
        'Assignment ID' => $emulator['assignment_id'],
        'Status' => $emulator['status'],
        'Submission Type'=> $emulator['submission_type'],
        'Priority'=> $emulator['priority']
      );
    }
    $json = json_encode($json);
    echo $json;
  }
  else if($RequestTypeType == "Emulators"){
    $Records = $DB->get_record(ANDROID_SERVER_EMULATORS_TABLE);
    $json = array();
    foreach($Records as $AvailableEmulatorsKey => $emulator){
      $json[] = array(
        'Emulator ID' => $emulator['emulator_id'],
        'State' => $emulator['state'],
        'In Use' => $emulator['in_use']
      );
    }
    $json = json_encode($json);
    echo $json;
  }
?>
