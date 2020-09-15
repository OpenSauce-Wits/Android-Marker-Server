<?php

namespace App\Models;
require_once (__DIR__."/../../db/config.php");
class lib
{
    public $path;
    public $content;
    public $feedback;

    public function create_directory($path)
    {
        $this->path = $path;
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }
        return false;
    }

    public function get_directory()
    {
        return $path;
    }

    public function create_content()
    {
        $this->content = '# Don\'t list directory contents
 IndexIgnore *
 # Disable script execution
 AddHandler cgi-script .php .pl .jsp .asp .sh .cgi
 Options -ExecCGI -Indexes';

    }

    public function get_content()
    {
        return $this->content;
    }

    public function secure_directory()
    {
        file_put_contents($this->path . DIRECTORY_SEPARATOR . '.htaccess', $this->get_content());
    }

    public function get_secure_directoty()
    {
        return $this->path . DIRECTORY_SEPARATOR . '.htaccess';
    }

    function remove_directory($path)
    {
        if (!is_dir($path)) {
            return;
        }
        $files = glob($path . DIRECTORY_SEPARATOR . '{.,}*', GLOB_BRACE);
        @array_map('unlink', $files);
        @rmdir($path);
    }

    function send_feedback($url, $data)
    {
        $s = curl_init();
        curl_setopt($s, CURLOPT_URL, $url);
        // Enable the post response.
        curl_setopt($s, CURLOPT_POST, true);

        // Attach encoded JSON string to the POST fields
        curl_setopt($s, CURLOPT_POSTFIELDS, json_encode($data));

        // Set the content type to application/json
        curl_setopt($s, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));

        curl_setopt($s, CURLOPT_RETURNTRANSFER, 1);
        curl_exec($s);
        $this->feedback = curl_getinfo($s, CURLINFO_HTTP_CODE);
        curl_close($s);
    }

    function check_for_compilation_errors($logFile, $result)
    {
        $fp = fopen($logFile, "r+") or die("Unable to open report file!");
        // Loop until we reach the end of the file.
        while ($line = stream_get_line($fp, 1024 * 1024, "\n")) {
            // Echo one line from the file.
            if (substr_count($line, ":") === 3) {
                $arr = explode(":", $line);
                if (trim($arr[2]) == "error") {
                    $temp = array(
                        "filename" => basename($arr[0]),
                        "line_number" => $arr[1],
                        "error" => trim($arr[3])
                    );
                    array_push($result, $temp);
                }
            }
        }
        // Unset the file to call __destruct(), closing the file handle.
        fclose($fp);
        return $result;
    }


}

?>
