<?php

include_once "FileDocument.php";
include_once "FtpClient.php";

class FtpDocument extends FileDocument {
	private $_username;
	private $_passwd;
	private $_host;
	private $_port;
	private $ftp;
	
	public function __construct($host = '', $port = 21, $root = '', $path = '', $username ='', $passwd = '') {
		$this->_host = $host;
		$this->_port = $port;
		$this->_root = $root;
		$this->_path = $path;
		$this->_username = $username;
		$this->_passwd = $passwd;
		
		$this->ftp = new FtpClient($this->_host, $this->_port, $this->_root);
		$this->ftp->login($this->_username, $this->_passwd);		
	}
	
	public function setPath($path) {
		$this->_path = $path;
	}
	
	public function read($file) {

	    $temp = fopen('php://temp', 'r+'); 
		    
		//Get file from FTP: 
		if (@$this->ftp->fget($temp, $file, FTP_BINARY, 0)) { 
		    rewind($temp); 
		    return stream_get_contents($temp); 
		} else { 
			return false; 
		} 		

	}
	
	public function make_dir($path, $mode = 0777){
		return $this->ftp->mkdir($path);
	}
	
	public function write($file, $content) {
		
		$temp = fopen('php://temp', 'r+');
		fwrite($temp, $content);
		rewind($temp);        
		
		return $this->ftp->fput($file, $temp, FTP_BINARY);
	}
	
	public function list_document(){
		$path = $this->path();
		
		$list = $this->ftp->nlist($path);

		$temp = array(
			'directory' => array(),
			'resource' => array(),
			'layout' => array()
		);
		foreach($list as $file) {
		
			$realpath = $file;			
			
			if ($this->ftp->isDir($realpath))  {
				$temp['directory'][] = $this->get($this->get_document($realpath));
			} else {
				$arr = explode(".", $file);
				$ext = array_pop($arr);
				
				if ($ext == 'json') continue;
				
				$temp[$ext][] = $this->get($realpath);
			}

		}
		
		usort($temp['directory'], array($this, 'sort_user'));
		usort($temp['layout'], array($this, 'sort_user'));
		usort($temp['resource'], array($this, 'sort_user'));
		
		return $temp;
	}
	
	
	
}


?>