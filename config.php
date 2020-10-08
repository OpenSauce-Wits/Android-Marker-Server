<?php

// ams (android marker server) is the prefix of the server tables
define('ANDROID_SERVER_SUBMISSIONS_TABLE','ams_submissions');
define('ANDROID_SERVER_EMULATORS_TABLE','ams_emulators');

unset($CFG);
global $CFG;
$CFG = new stdClass();
// CFG holds the settings for the server
$CFG->dbtype    = 'mysqli';
$CFG->dblibrary = 'native';
$CFG->dbhost    = 'localhost';
$CFG->dbname    = 'android_marker_server';
$CFG->dbuser    = 'thando';
$CFG->dbpass    = 'Le@dYourself4ward';
$CFG->prefix    = 'ams_';
$CFG->dboptions = array (
  'dbpersist' => 0,
  'dbport' => '',
  'dbsocket' => '',
  'dbcollation' => 'utf8mb4_unicode_ci',
);

$CFG->wwwroot   = 'http://localhost/9999';
$CFG->dataroot  = '/home/thando/Desktop/Android_Marker_Plugin/Android-Marker-Server';
$CFG->admin     = 'admin';

$CFG->directorypermissions = 0777;

//require_once(__DIR__ . '/lib/setup.php');*/

// There is no php closing tag in this file,
// it is intentional because it prevents trailing whitespace problems!
