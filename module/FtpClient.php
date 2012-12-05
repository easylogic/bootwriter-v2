<?php

class FtpClient {
	
	public function __construct($host = 'ftp.inpost.kr', $port = 21, $root = '') {
		$this->host = $host;
		$this->port = $port;
		$this->root = $root;
		
		$this->connect();
		
	}
	
	public function connect() {
		$this->con = ftp_connect($this->host, $this->port);
	}
	
	public function login($userid, $passwd) {
		return ftp_login($this->con, $userid, $passwd);
	}
	
	public function isFile($path) {
		return ftp_size($this->con, $path) != -1;
	}
	
	public function isDir($path) {
		return ftp_size($this->con, $path) == -1;
	}	
	
	public function __call($method, $args = array()) {
		$func = "ftp_".$method;
		
        if(function_exists($func)){
            array_unshift($args,$this->con);
            return call_user_func_array($func,$args); 
        }else{ 
            // replace with your own error handler. 
            die("$func is not a valid FTP function"); 
        } 
	}
	
	public function __desctuct() {
		ftp_close($this->con);
	}
	
}

?>