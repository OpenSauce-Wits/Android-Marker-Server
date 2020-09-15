<?php
  require_once("lib.php");    // Include Library Functions
  // Check if a submission needs marking

  $BuiltSubmissions = $DB->get_record(ANDROID_SERVER_SUBMISSIONS_TABLE,array('status'=>"Built"));
  if ($BuiltSubmissions && count($BuiltSubmissions)>0) {
    // Get list of available emulators
    $EmulatorsRawInput = explode(" ", shell_exec("echo $(adb devices)"));

    // Delete the "list of adb devies" string
    for($i=0;$i<4;$i++) unset($EmulatorsRawInput[$i]);

    $AvailableEmulators = array();
    for($i=0;$i<count($EmulatorsRawInput);$i+=2){
      if(trim($EmulatorsRawInput[$i+5]) !== "device") continue;
      $tempEmulatorData = array();
      $tempEmulatorData['emulator_id'] = trim($EmulatorsRawInput[$i+4]);
      $tempEmulatorData['state'] = trim($EmulatorsRawInput[$i+5]);
      array_push( $AvailableEmulators, $tempEmulatorData);
    }

    // Check if any of them are free
    $EmulatorQuery = $DB->get_record(ANDROID_SERVER_EMULATORS_TABLE);
    if($EmulatorQuery && count($EmulatorQuery)>0){
      foreach($AvailableEmulators as $AvailableEmulatorsKey => $emulator){
        $found = false;
        foreach ($EmulatorQuery as $EmulatorQueryKey => $value) {
          if($value['emulator_id'] === $emulator['emulator_id']){
              $found = true;
              $AvailableEmulators[$AvailableEmulatorsKey]['id'] = $value['id'];
              if ($value['in_use'] === "true") {
                unset($AvailableEmulators[$AvailableEmulatorsKey]);
              }
              break;
          }
        }
        if(!$found){
          $AvailableEmulators[$AvailableEmulatorsKey]['in_use'] = "false";
          $DB->insert_record( ANDROID_SERVER_EMULATORS_TABLE, $AvailableEmulators[$AvailableEmulatorsKey]);
        }
      }
    }
    else if(count($AvailableEmulators)>0){
      foreach($AvailableEmulators as $EmulatorKey => $emulator){
        $AvailableEmulators[$EmulatorKey]['in_use'] = "false";
        $AvailableEmulators[$EmulatorKey] = $DB->insert_record( ANDROID_SERVER_EMULATORS_TABLE, $AvailableEmulators[$EmulatorKey]);
      }
    }

    // Mark a MarkProject
    $NumEmulators = count($AvailableEmulators);
    $NumSubmissions = count($BuiltSubmissions);
    $SubmissionEmulatorString = "";
    foreach($AvailableEmulators as $EmulatorKey => $emulator){
      if($NumEmulators>$NumSubmissions && count(explode(" ", $SubmissionEmulatorString))<2){
        // Maximum of two emulators for each submission
        $SubmissionEmulatorString .= "'".$emulator['emulator_id']."' ";
        --$NumEmulators;
        continue;
      }
      else{
        $SubmissionEmulatorString .= "'".$emulator['emulator_id']."' ";
        --$NumEmulators;
      }
      foreach ($BuiltSubmissions as $key => $value) {
        $descriptorspec = array(
    			0 => array('pipe', 'r'), // stdin is a pipe that the child will read from
    			1 => array('pipe', 'w'), // stdout is a pipe that the child will write to
    			2 => array('pipe', 'w')  // stderr is a pipe the child will write to
    		);
    		if($argv[1] !== NULL && $argv[1] === "cron"){
    			$execString = 'ANDROID_SDK_ROOT=$ANDROID_SDK_ROOT '.'PATH=$PATH'." php MarkAndroidProject.php 'Mark' '".$value['user_id']."' '".$value['assignment_id']."' $SubmissionEmulatorString";
    		}
    		else{
    			$execString = "sudo -E env \"".'PATH=$PATH'."\" php MarkAndroidProject.php 'Mark' '".$value['user_id']."' '".$value['assignment_id']."' $SubmissionEmulatorString";
    		}
    		$process = proc_open($execString, $descriptorspec, $pipes);
    		if (!is_resource($process)) {
    			throw new Exception('bad_program could not be started.');
    		}
		    else{
          foreach (explode(" ", $SubmissionEmulatorString) as $SESvalue) {
            if($SESvalue == "") continue;
            foreach($AvailableEmulators as $UpdateKey => $UpdateEm){
              if("'".$UpdateEm['emulator_id']."'" !== $SESvalue) continue;
              $AvailableEmulators[$UpdateKey]['in_use'] = "true";
    	    		$DB->update_record(ANDROID_SERVER_EMULATORS_TABLE,$AvailableEmulators[$UpdateKey],array('id'=>$AvailableEmulators[$UpdateKey]['id']));
            }
          }
		      unset($BuiltSubmissions[$key]);
		    }
        break;
      }
      $SubmissionEmulatorString = "";
    }
  }
?>
