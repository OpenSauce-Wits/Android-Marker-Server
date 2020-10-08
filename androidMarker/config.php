<?php

// ams (android marker server) is the prefix of the server tables
define('ANDROID_SERVER_SUBMISSIONS_TABLE', 'ams_submissions');
define('ANDROID_SERVER_EMULATORS_TABLE', 'ams_emulators');

unset($CFG);

global $CFG;
$CFG = new stdClass();
// CFG holds the settings for the server
$CFG->dbtype = 'mysqli';
$CFG->dblibrary = 'native';
$CFG->dbhost = '127.0.0.1';
$CFG->dbname = 'testDB';
$CFG->dbuser = 'moodledude';
$CFG->dbpass = 'password';
$CFG->prefix = 'ams_';
$CFG->dboptions = array(
    'dbpersist' => 0,
    'dbport' => '',
    'dbsocket' => '',
    'dbcollation' => 'utf8mb4_unicode_ci',
);

$CFG->wwwroot = 'http://localhost/9999';
$CFG->dataroot = __DIR__."/../";
$CFG->admin = 'admin';

$CFG->directorypermissions = 0777;
