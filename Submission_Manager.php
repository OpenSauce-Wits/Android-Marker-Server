<?php

	require_once("config.php");
	require_once("lib.php");    // Include Library Functions

	$inputJSON = file_get_contents('php://input');  // Get input from the client
	$input = json_decode($inputJSON, TRUE);        // Decode the JSON object
	$RequiredDocuments = NULL;
	$LecturerZip = NULL;
	$StudentZip = NULL;

	$SubmissionsPath = getcwd().DIRECTORY_SEPARATOR."Submissions";
	create_directory($SubmissionsPath);


	$SubmissionType = $input['submissiontype'];
	$UserID = $input['userid'];
	$Priority = $input['priority'];
	$AssignmentID = $input['assignment'];
	$cmid = $input['grade'];
	if($SubmissionType == "LecturerSubmission"){
		$RequiredDocuments = base64_decode($input['RequiredDocuments']); // Decode the Base64
		$LecturerZip = base64_decode($input['LecturerZip']);		 // Decode the Base64

		// Store files
		$SubmissionsPath .= DIRECTORY_SEPARATOR. $AssignmentID;
		create_directory($SubmissionsPath);

		$SubmissionsPath .= DIRECTORY_SEPARATOR. "LecturerSubmission";
		if(is_dir($SubmissionsPath)){
			deleteDirectory($SubmissionsPath);
		}
		create_directory($SubmissionsPath);

		file_put_contents( $SubmissionsPath.DIRECTORY_SEPARATOR."RequiredDocuments.txt", $RequiredDocuments);
		file_put_contents($SubmissionsPath.DIRECTORY_SEPARATOR."LecturerZip.zip", $LecturerZip);

		$SubmissionsPath .= DIRECTORY_SEPARATOR. $UserID;
		create_directory($SubmissionsPath);

		file_put_contents( $SubmissionsPath.DIRECTORY_SEPARATOR."RequiredDocuments.txt", $RequiredDocuments);
		file_put_contents($SubmissionsPath.DIRECTORY_SEPARATOR."LecturerZip.zip", $LecturerZip);
            	//Creates a psuedo student submission
		file_put_contents($SubmissionsPath.DIRECTORY_SEPARATOR."StudentZip.zip", $LecturerZip);
	}
	else if($SubmissionType == "StudentSubmission"){
		$StudentZip = base64_decode($input['StudentZip']); // Decode the Base64

		// Store files
		$SubmissionsPath .= DIRECTORY_SEPARATOR. $AssignmentID;
		create_directory($SubmissionsPath);

		$SubmissionsPath .= DIRECTORY_SEPARATOR. "StudentSubmissions";
		create_directory($SubmissionsPath);

		$SubmissionsPath .= DIRECTORY_SEPARATOR. $UserID;
		if(is_dir($SubmissionsPath)){
			deleteDirectory($SubmissionsPath);
		}
		create_directory($SubmissionsPath);

		file_put_contents($SubmissionsPath.DIRECTORY_SEPARATOR."StudentZip.zip", $StudentZip);
	}

	ignore_user_abort(true);
	set_time_limit(0);

	ob_start();
	// do initial processing here
	header('Connection: close');
	header('Content-Length: '.ob_get_length());
	ob_end_flush();
	ob_flush();
	flush();

	// Mark project submission
	mark( $SubmissionsPath, $input['id'], $UserID, $AssignmentID, $input['url'], $Priority, $SubmissionType, $cmid);
?>
