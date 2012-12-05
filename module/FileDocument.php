<?php

include_once "IDocument.php";
include_once "Resource.php";


class FileDocument implements IDocument {
	protected $_root;
	protected $_path;
	
	public function __construct($root = '', $path = '') {
		$this->_root = realpath(getcwd().DIRECTORY_SEPARATOR.$root);
		$this->_path = $path;
	}
	
	public function setPath($path) {
		$this->_path = $path;
	}
	
	public static function urlencode($path) {
		return implode("/", array_map('urlencode', explode("/", $path)));
	}
	
	public static function urldecode($path) {
		return implode("/", array_map('urldecode', explode("/", $path)));
	}
	
	public function read($file) {
		return file_get_contents($file);		
	}
	
	public function write($file, $content) {
		return file_put_contents($file, $content);
	}
	
	public static function setObject($path, $data) {
		
		if (!strstr($path, "layout")) {
			$path = $path.".json";	
		}
		
		
		return self::write($path, json_encode($data));
	}
	
	public static function getObject($path) {
		
		if (!strstr($path, "layout")) {
			$path = $path.".json";	
		}
		
		return json_decode(self::read($path));
	}
	
	public function set($path, $data) {
		if (!strstr($path, "layout")) {
			$path = $path.".json";	
		}
		
		
		return $this->write($path, json_encode($data));		
	}
	
	public function get($path) {
		
		if (!strstr($path, "layout")) {
			$path = $path.".json";	
		}
		
		return json_decode($this->read($path));
	}	
	
	public function path() {
		
		$args = func_get_args();
		
		$args = array_merge(array($this->_root, self::urldecode($this->_path)), $args);
		
		$path = implode(DIRECTORY_SEPARATOR, $args);
		
		$path = self::urlencode($path);
		
		return $path;
	}
	
	public function real() {
		$args = func_get_args();
		
		$path = array_shift($args);
		
		$args = array_merge(array($this->_root, self::urldecode($path)), $args);
		
		$path = implode(DIRECTORY_SEPARATOR, $args);
		
		$path = self::urlencode($path);
		
		return $path;
	}
	
	protected function get_document($dir) {
		return $dir.DIRECTORY_SEPARATOR."document";
	}
	
	public function make_dir($path, $mode = 0777) {
		return mkdir($path);
	}
	
	public function create_document($title) {
		$title = trim($title);

		$obj = array(
			'path' => $this->_path,		
			'type' => 'document',
			'title' => $title,
			'id' => uniqid(),
			'create' => time()
		);
		
		$create_dir = $this->path($obj['id']);		
	
		$ret = @$this->make_dir($create_dir); 
		
		if ($ret) {
			$this->set($this->get_document($create_dir), $obj);
		}

		return $ret;
	}
	
	protected function get_layout($layout) {
		return $layout.".layout";
	}
	
	public function create_layout($title) {
		
		$obj = array(
			'path' => $this->_path,
			'type' => 'layout',
			'title' => trim($title),
			'id' => uniqid(),
			'create' => time(),
			'list' => array()
		);
				
		$layout = $this->path($obj['id']);
		
		$ret = $this->set($this->get_layout($layout), $obj);
		
		return $ret;
	}
	
	public function list_category() {
		$temp = array();
		
		$path = $this->path();
		
		do {
			if (basename($path) == ".") break;
			
			$obj = $this->get($this->get_document($path));
			
			array_unshift($temp, $obj);
			
			$path = dirname($path);	
		} while(basename($path) != ".");
		
		return $temp;
	}
	
	public function list_document(){
		$path = $this->path();

		$d = dir($path);
		
		$temp = array(
			'directory' => array(),
			'resource' => array(),
			'layout' => array()
		);
		while (false !== ($file = $d->read())) {
	   		if ($file != "." && $file != "..") {
	   			
				$realpath = $this->path($file);
				
				if (is_dir($realpath))  {
					$temp['directory'][] = $this->get($this->get_document($realpath));
				} else {
					$arr = explode(".", $file);
					$ext = array_pop($arr);
					
					if ($ext == 'json') continue;
					
					$temp[$ext][] = $this->get($realpath);
				}
	   		}
		}
		$d->close();	

		usort($temp['directory'], array($this, 'sort_user'));
		usort($temp['layout'], array($this, 'sort_user'));
		usort($temp['resource'], array($this, 'sort_user'));
		
		return $temp;
	}
	
	public function sort_user($a, $b) {
		return intval($a->create) < intval($b->create); 
	}
	
	public function update_layout($id, $list){
		$path = $this->path($this->get_layout($id));
		$obj =  $this->get($path);
		
		$obj->list = $list;
		$obj->update = time();
		
		return $this->set($path, $obj);
	}
		
	
	public function update_resource($id, $title, $type, $ext, $data){
		if (is_array($data)){
			
		} else {
			$path = $this->path($this->get_resource($id));
			$obj =  $this->get($path);
			
			$this->write($path, $data);
			$obj->title = $title;
			$obj->type = $type;
			$obj->ext = $ext;
			$obj->update = time();
			
			if ($this->set($path, $obj)) {
				return $obj;
			}
		}
		
		return $ret;
	}
	
	public function copy_resource($obj) {
		 $obj = (object)$obj;
		 $path = $this->real($obj->path, $this->get_resource($obj->id));
		 
		 $data = $this->get($path);
		 
		 $content = $this->read($path);
		 
		$json_arr = array(
			'path' => $this->_path,			
			'type' => $obj->type,			
			'title' => $obj->title,
			'name' => $obj->name,
			'ext' => $obj->ext,
			'mime' => $obj->mime,
			'id' => uniqid(),
			'create' => time()
		);
		
		$file = $this->path($this->get_resource($json_arr['id']));
		
		$this->write($file, $content);
		
		if ($this->set($file, $json_arr)) {
			return $json_arr;
		}
	}
	
	public function create_resource($title, $type, $ext, $data){
		if (is_array($data)) {	// if uploded file  
			$arr = explode(".", $data['name']);
			$ext = array_pop($arr);		 
			$title = implode(".", $arr);
			$name = $title;
			
			$type = Resource::getType($ext);
			$mime = Resource::getMime($ext);
						
			$filename = uniqid();
			$file = $this->path($this->get_resource($filename));

			//$ret = move_uploaded_file($data['tmp_name'], $file);
			$ret = $this->write($file, file_get_contents($data['tmp_name']));

			
		} else {
			$name = $title;
			$filename = uniqid();
			$file = $this->path($this->get_resource($filename));
			
			$ret = $this->write($file, $data);
			
			$mime = Resource::getMime($ext);			
		}
		
		if ($ret) {
			$json_arr = array(
				'path' => $this->_path,			
				'type' => ($type) ? $type : 'resource',			
				'title' => $title,
				'name' => $name,
				'ext' => $ext,
				'mime' => $mime,
				'id' => $filename,
				'create' => time()
			);
			
			if ($this->set($file, $json_arr)) {
				return $json_arr;
			}
		}
		
		
		return $ret; 
	}
	
	protected function get_resource($path) {
		return $path.".resource";
	}
	
	public function info_resource($id) {
		$path = $this->path($this->get_resource($id));
		$obj =  $this->get($path);
		
		$obj->data = $this->read($path);
		
		return $obj;
	}

	public function change_title($type, $title, $id) {
		if ($type == 'document') {
			$path = $this->path($this->get_document($id));
		} else if ($type == 'resource') {
			$path = $this->path($this->get_resource($id));
		} else if ($type == 'layout') {
			$path = $this->path($this->get_layout($id));
		}
		
		$obj = $this->get($path);
		$obj->title = $title;
			
		$ret = $this->set($path, $obj);
		
		return $ret;
	}

	public function changeAsFileName($title) {
		return trim(str_replace(array('?'), array('-'), $title));
	}
	
	// output resource 
	public function output($id) {
		$path = $this->path($this->get_resource($id));
		
		$obj =  $this->get($path);

		header("Content-length: ".filesize($path));
		header('Content-Disposition: attachment; filename="'. $this->changeAsFileName($obj->title) . "." . $obj->ext . '"');
		header("Content-type: application/octet-stream");

		readfile($path);
	}
	
}

?>