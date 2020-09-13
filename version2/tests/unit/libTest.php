<?php


use PHPUnit\Framework\TestCase;
class libTest extends TestCase{
    public function setUp():void
    {
        parent::setUp();
        $this->path =getcwd();
        $this->url = "https://www.geeksforgeeks.org/";
        $this->data = array("Molefe" => "Molefe");
        $this->logFile = "../../files/log.txt";
    }
	/**
	Make Directories Throw Exceptions
	**/

/**

1. Write Test
2. Build Simple Pass
3. Refactor & Rebuild
4. Test Models

	/** @test */
	public function check_if_directory_exists(){
		$lib = new \App\Models\lib;
		$lib->create_directory($this->path);
		$this->assertDirectoryExists($this->path);
	}

 /** @test */
 public function check_that_directory_is_deleted(){
	 $lib = new \App\Models\lib;
	 $lib->create_directory($this->path);
	 $lib->remove_directory($this->path);
	 $this->assertDirectoryNotExists($this->path);

 }

 /** @test */
 public function check_feedback_was_sent(){
   $lib = new \App\Models\lib;
   $lib->send_feedback($this->url,$this->data);
   $ch = curl_init($this->url);
   curl_setopt($ch,CURLOPT_RETURNTRANSFER,TRUE);
   $result = curl_exec($ch);
   $result_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
   $this->assertEquals($result_status,$lib->feedback_status);
 }

 /** @test */
public function check_that_checking_for_complilation_errors_works(){
  $lib = new \App\Models\lib;
  $array = $lib->check_for_compilation_errors($this->logFile,array());
  $this->assertEquals( gettype(array()),gettype($array));
  $this->assertEquals( "MainActivity.java",$array[0]['filename']);
}

 //
	// /** @test */
	// public function check_that_directory_secure(){
	// 	$lib = new \App\Models\lib;
	// 	$path = "/home/molefe/Learning/Playground/Project/OpenSauce/version1/folder";
	// 	$content = '# Don\'t list directory contents
 // IndexIgnore *
 // # Disable script execution
 // AddHandler cgi-script .php .pl .jsp .asp .sh .cgi
 // Options -ExecCGI -Indexes';
 // 	file_put_contents($path . DIRECTORY_SEPARATOR . '.htaccess', $content);
	// chmod($path.DIRECTORY_SEPARATOR.'.htaccess',0755);
	// //Testing
	// 	$lib->create_content();
	// 	$this->assertEquals($content,$lib->get_content());
	// 	$lib->secure_directory();
	// 	$this->assertFileEquals('$path/.htaccess',$lib->get_secure_directoty());
	// }

}
