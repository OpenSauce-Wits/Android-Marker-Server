<?php

namespace App\HTTP;
/**
1. Error
curl_errno(): Supplied Resource Is Not A Valid cURL
Cause
Make use of curl after executing curl close
Resource
https://stackoverflow.com/questions/3868183/php-how-to-check-if-curl-actually-post-send-request/4355462
**/

/**
Assertions
1. Copy Marking Scripts

**/
$mark_project = "/home/molefe/Learning/Playground/Projects/OpenSauce/version2/MarkingScripts/MarkProject.sh";
$run_test_on_emulator = "/home/molefe/Learning/Playground/Projects/OpenSauce/version2/MarkingScripts/runTestOnEmulator";
$Priority = 2;

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
