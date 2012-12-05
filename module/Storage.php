<?php

include_once "FileDocument.php";
include_once "FtpDocument.php";

class Storage {
	
	public static function load	($id, $path) {
		$dir = "../config";
		$filename = $dir.DIRECTORY_SEPARATOR.$id.".json";
		
		$obj = json_decode(file_get_contents($filename));
		
		if ($obj->type == 'file') {
			return new FileDocument($obj->root, $path);
		} else if ($obj->type == 'ftp') {
			return new FtpDocument($obj->host, $obj->port, $obj->root, $path, $obj->username, $obj->password);
		}
		
		return null;
	}
	
	public static function setCurrentStorage($id) {
		$_SESSION['storage'] = $id;
	}
	
	public static function getCurrentStorage() {
		return $_SESSION['storage'];
	}
	
	public static function removeStorage($id) {
		$dir = "../config";
		$filename = $dir.DIRECTORY_SEPARATOR.$id.".json";
		
		unlink($filename);
		unset($_SESSION['storage']);		
	}
}

?>