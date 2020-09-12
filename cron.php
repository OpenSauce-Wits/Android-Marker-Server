<?php
  require_once("lib.php");    // Include Library Functions

  // Check if a submission needs marking
  $BuiltSubmissions = $DB->get_record(ANDROID_SERVER_SUBMISSIONS_TABLE,array('status'=>"Built"));
  if ($BuiltSubmissions && count($BuiltSubmissions)>0) {
    // Get list of available emulators
    $EmulatorsRawInput = explode(" ", shell_exec("echo $(adb devices)"));
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
    foreach($AvailableEmulators as $EmulatorKey => $emulator){
      foreach ($BuiltSubmissions as $key => $value) {
        $descriptorspec = array(
    			0 => array('pipe', 'r'), // stdin is a pipe that the child will read from
    			1 => array('pipe', 'w'), // stdout is a pipe that the child will write to
    			2 => array('pipe', 'w')  // stderr is a pipe the child will write to
    		);

    		$execString = "sudo -E env \"".'PATH=$PATH'."\" php MarkAndroidProject.php 'Mark' '".$value['user_id']."' '".$value['assignment_id']."' '".$emulator['emulator_id']."'";

    		$process = proc_open($execString, $descriptorspec, $pipes);
    		if (!is_resource($process)) {
    			throw new Exception('bad_program could not be started.');
    		}
        else{
          $AvailableEmulators[$EmulatorKey]['in_use'] = "true";
    			$DB->update_record(ANDROID_SERVER_EMULATORS_TABLE,$AvailableEmulators[$EmulatorKey],array('id'=>$AvailableEmulators[$EmulatorKey]['id']));
          unset($BuiltSubmissions[$key]);
        }
        break;
      }
    }
  }
?>
