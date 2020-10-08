<?php
namespace androidMarker;
require_once('FileManager.php');
//require_once('config.php');
define("EMULATOR","EmulatorsTable.json");
define("SUBMISSION","SubmissionTable.json");
$location = "http://127.0.0.1:4040";
global $CFG;
$emulators = new FileManager($CFG->dbhost,$CFG->dbuser,$CFG->dbpass,$CFG->dbname,constant("AMS_EMULATORS"),constant("EMULATOR"));
$submission = new FileManager($CFG->dbhost,$CFG->dbuser,$CFG->dbpass,$CFG->dbname,constant("AMS_SUBMISSION"),constant("SUBMISSION"));
$emulators->createJSONFile("json");
$submission->createJSONFile("json");
//header("Location: {$location}");
//die();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Android Marker</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"
          integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/index.css">
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-3 col-md-3">
            <div class="d-flex flex-column sidebar">
                <div class="sidebar-heading text-center">
                    <h3>Navigation</h3>
                </div>
                <nav class="nav-menu">
                    <ul>
                        <li><a href="#"><i class="fas fa-home"></i><span>Home</span></a></li>
                        <li><a href="Submission.html"><i class="fas fa-table"></i><span>Submissions</span></a></li>
                        <li><a href="Emulators.html"><i class="fas fa-mobile"></i><span>Emulators</span></a></li>
                    </ul>
                </nav>
                <!--                    <button class="mobile-nav-toggle d-xl-none"><i class="icofont-navigation-menu"></i></button>-->
            </div>

        </div>

        <div class="col-lg-9 col-md-9">
            <section id="hero" class="d-flex flex-column justify-content-center align-items-center">
                <div class="heading">
                    <h2>Android Marker</h2>
                </div>
            </section>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"
        integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj"
        crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"
        integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN"
        crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"
        integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV"
        crossorigin="anonymous"></script>
<script src="https://kit.fontawesome.com/4981570ac7.js" crossorigin="anonymous"></script>
</body>
</html>
