<?php

namespace androidMarker\Test;

use PHPUnit\Framework\TestCase;
use androidMarker\lib;

class libTest extends TestCase
{

    public function setUp(): void
    {
        parent::setUp();
        $this->path = __DIR__ . "../Project/OpenSauce";
        $this->rootPath = __DIR__ .DIRECTORY_SEPARATOR. "../";
        $this->currentPath = __DIR__;
        $this->logFile = $this->currentPath.DIRECTORY_SEPARATOR."log.txt";
        $this->testURL = "https://www.geeksforgeeks.org/";
        $this->xmlData = array("Molefe" => "Molefe");
        $this->content = '# Don\'t list directory contents
 IndexIgnore *
 # Disable script execution
 AddHandler cgi-script .php .pl .jsp .asp .sh .cgi
 Options -ExecCGI -Indexes';


    }

    public function testCreateDirectory()
    {
        $lib = new lib();
        if (is_dir($this->path)) {
            rmdir($this->path);
        }
        $result = $lib->create_directory($this->path);
        $this->assertTrue($result);
    }

    public function testContent()
    {
        $lib = new lib();
        $lib->create_content();
        $this->assertEquals($this->content, $lib->get_content());
    }

    public function testRemoveDirectory()
    {
        $lib = new lib();
        if (!is_dir($this->path)) {
            $lib->create_directory($this->path);
        }
        $lib->remove_directory($this->path);
        $this->assertDirectoryNotExists($this->path);
    }

    public function testSendFeedbackWorks()
    {
        $lib = new lib();
        $libResultStatus = $lib->send_feedback($this->testURL, $this->xmlData);
        $ch = curl_init($this->testURL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $result = curl_exec($ch);
        $resultStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $this->assertEquals($resultStatus, $libResultStatus);

    }

    public function testCheckForCompilationErrorsWorks()
    {
        $lib = new lib();
        $array = $lib->check_for_compilation_errors($this->logFile,array());
        $this->assertEquals('MainActivity.java',$array[0]['filename']);

    }


}