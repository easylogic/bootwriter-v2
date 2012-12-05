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
        	
			var_dump($args);
			 
            array_unshift($args,$this->con);
			
			var_dump($args);
			 
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

$ftp = new FtpClient();
$ftp->login("easylogic", "soclsrn");

var_dump($ftp->nlist("."));


// ftp 테스트 
echo "ftp ====================><br />";
$connect = ftp_connect("ftp.inpost.kr");

ftp_login($connect, "easylogic", "soclsrn");

$dir = ftp_pwd($connect);

echo "current directory : ", $dir, "<br />";


$contents = ftp_nlist($connect, $dir);

echo "<pre >", print_r($contents, true), "</pre>";

//ftp_mkdir($connect, "storage");

$contents = ftp_nlist($connect, $dir);

echo "<pre >", print_r($contents, true), "</pre>";

$contents = ftp_rawlist($connect, $dir);

echo var_dump($contents);



// 루트 디렉토리 지정 

// 사이트 오픈 

// 디렉토리 리스트 출력 (root 바로 하위 디렉토리만 ) 

// 디렉토리는 제목으로 되어 있음 
// 디렉토리 안에는 document.json 파일이 있음
// document.json 파일은 자동으로 생성되며 개별  Object 를 관리하는 개념으로 감 

/*
+ Title
 + document.json  (�붾㈃�곸쓽 援ъ“ �ㅼ젙) 
 + title1.md 
 + title2.json
 + title3.html 
 + title4.jpg
 + title5.mov
 
*/

// 원본 그대로의 파일을 저장한다.  그게 목적이다.  


?>