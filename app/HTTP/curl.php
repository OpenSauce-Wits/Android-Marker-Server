<?php

namespace App\HTTP;
//include("HTTPRequestInterface.php");

/**
 * Class curl
 * @package App\HTTP
 * 1. Some Weird Reason... I am supposed to include the interface
 * Error: PHP Interface Not Found
 * Cause: Unknwon
 * Solution: include('interface');
 *
 * Tip:
 * Instantiate Curl Object
 * 1. Create Namespace
 * $namespace = '\namespace\classname'
 * 2. Create Object From  Namespace
 * $object= new $namespace();
 */
class curl
{
    private $url = null;

    public function __construct($url)
    {
        $this->url = curl_init($url);
    }

    public function setOption($name, $value)
    {
        // TODO: Implement setOption() method.
        curl_setopt($this->url, $name, $value);
    }

    public function execute()
    {
        // TODO: Implement execute() method.
        return curl_exec($this->url);
    }

    public function getInfo($name)
    {
        // TODO: Implement getInfo() method.
        return curl_getinfo($this->url, $name);

    }

    public function close()
    {
        // TODO: Implement close() method.
        curl_close($this->url);
    }

}
$url = "https://www.geeksforgeeks.org//";
// $namespace = '\App\HTTP\curl';
// set up curl to point to your requested URL
//$ch = curl_init($url);
// tell curl to return the result content instead of outputting it
//curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

$lib = new \App\Models\DatabaseHelper();

// execute the request, I'm assuming you don't care about the result content
//$result = curl_exec($ch);
//print_r(curl_getinfo($ch,CURLINFO_HTTP_CODE));
// print_r($ch);
// $curl = new $namespace($url);
// $curl->setOption(CURLOPT_RETURNTRANSFER,1);
// $curl->setOption(CURLOPT_URL,$url);
// $result =  $curl->execute();
// echo $result;
// echo curl_getinfo($result, CURLINFO_HTTP_CODE);
// $j = json_encode($result);
// echo $result[];
// //echo $result;
// echo "here\n";
// echo $curl->getInfo(CURLOPT_RETURNTRANSFER);
// $curl->close();
