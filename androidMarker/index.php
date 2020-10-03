<?php
namespace androidMarker;
require_once('FileManager.php');
define("AMS_EMULATORS","ams_emulators");
define("AMS_SUBMISSION","ams_submissions");
define("EMULATOR","EmulatorsTable.json");
define("SUBMISSION","SubmissionTable.json");
$location = "http://127.0.0.1:4040";
global $CFG;
$emulators = new FileManager($CFG->dbhost,$CFG->dbuser,$CFG->dbpass,$CFG->dbname,constant("AMS_EMULATORS"),constant("EMULATOR"));
$submission = new FileManager($CFG->dbhost,$CFG->dbuser,$CFG->dbpass,$CFG->dbname,constant("AMS_SUBMISSION"),constant("SUBMISSION"));
$emulators->createJSONFile("json");
$submission->createJSONFile("json");
header("Location: {$location}/index.html");
die();
?>
