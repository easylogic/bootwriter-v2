<?php

session_start();

include_once '../module/Storage.php';

if ($_REQUEST['storageId']){
	$storageId = $_REQUEST['storageId'];
} else {
	$storageId = Storage::getCurrentStorage();
}

if ($storageId) {
	$path = $_REQUEST['path'];	
	$storage = Storage::load($storageId, $path);
}

header("Content-Type: application/json");

switch (urldecode($_REQUEST['cmd'])) {
	case 'create storage':
		$obj = (object)$_REQUEST['obj'];
		
		$dir = "../config";
		$id = uniqid();
		
		$filename = $dir.DIRECTORY_SEPARATOR.$id.".json";
		$obj->id = $id;
		
		$ret = file_put_contents($filename, json_encode($obj));
		
		$json = array('result' => $ret ? 'ok' : 'fail');
		break;	
	case 'list storage':
		$dir = "../config";

		$d = dir($dir);
	
		$temp = array();
		while (false !== ($entry = $d->read())) {
			 if (in_array($entry, array(".", ".."))) continue;
		   $obj = json_decode(file_get_contents($dir.DIRECTORY_SEPARATOR.$entry));
			 
			 $temp[] = array(
			 	'type' => $obj->type,
			 	'title' => $obj->title,
			 	'id' => $obj->id,
			 	'selected' => ($obj->id == Storage::getCurrentStorage())
			 );
		}
		$d->close();

		$json = array('result' => $temp);
		break;	
	case 'select storage':
		$id  = $_REQUEST['id'];
		
		Storage::setCurrentStorage($id);
		
		$json = array('result' => 'ok');
		
		break;
		
	case 'remove storage':
		$id  = $_REQUEST['id'];
		
		Storage::removeStorage($id);
		
		$json = array('result' => 'ok');
				
		break;
	case 'create document':
		$ret = $storage->create_document($_REQUEST['title']);
		$json = array('result' => $ret ? 'ok' : 'fail');
		break;
	case 'create layout':
		$ret = $storage->create_layout($_REQUEST['title'], $_REQUEST['type']);
		$json = array('result' => $ret ? 'ok' : 'fail');		
		break;
	case 'update document':
		$ret = $storage->update_document($_REQUEST['title'], $_REQUEST['json']);
		$json = array('result' => $ret ? 'ok' : 'fail');		
		break;
	case 'update layout':
		$ret = $storage->update_layout($_REQUEST['id'], $_REQUEST['list']);
		$json = array('result' => $ret ? 'ok' : 'fail');		
		break;	
	case 'list document':
		$ret = $storage->list_document();
		$json = array('result' => $ret);		
		break;
	case 'get document':
		$ret = $storage->get_document($_REQUEST['title']);
		$json = array('result' => $ret);		
		break;		
	case 'get layout':
		$ret = $storage->get_layout($_REQUEST['title']);
		$json = array('result' => $ret);		
		break;								
	case 'create resource':
		
		if ($_REQUEST['id']) {	// update 
			if ($_FILES['data']) {
				$ret = $storage->update_resource($_REQUEST['id'], $_REQUEST['title'], $_REQUEST['type'], $_REQUEST['ext'], $_FILES['data']);	
			} else {
				$ret = $storage->update_resource($_REQUEST['id'], $_REQUEST['title'], $_REQUEST['type'], $_REQUEST['ext'], $_REQUEST['data']);
			}		
		} else {				// create 
			if ($_FILES['data']) {
				$ret = $storage->create_resource($_REQUEST['title'], $_REQUEST['type'], $_REQUEST['ext'], $_FILES['data']);	
			} else {
				$ret = $storage->create_resource($_REQUEST['title'], $_REQUEST['type'], $_REQUEST['ext'], $_REQUEST['data']);
			}			
		}
		

		$json = array('result' => $ret);		
		break;
	case 'copy resource':
		
		$ret = $storage->copy_resource($_REQUEST['resource']);
		
		$json = array('result' => $ret);		
		break;		
	case 'info resource':
		$ret = $storage->info_resource($_REQUEST['id']);
		$json = array('result' => $ret);
		break;		
	case 'tree resource': 
		$ret = $storage->tree_resource();
		break;
	case 'update resource':
		if ($_FILES['data']) {
			$ret = $storage->update_resource($_REQUEST['id'], $_REQUEST['title'], $_REQUEST['type'], $_REQUEST['ext'], $_FILES['data']);	
		} else if ($_REQUEST['url']) {
			$ret = $storage->update_resource($_REQUEST['id'], $_REQUEST['title'], $_REQUEST['type'], $_REQUEST['ext'], file_get_contents($_REQUEST['url']));
		} else {
			$ret = $storage->update_resource($_REQUEST['id'], $_REQUEST['title'], $_REQUEST['type'], $_REQUEST['ext'], $_REQUEST['data']);
		}
		$json = array('result' => $ret);		
		break;		
	case 'change title':
		$ret = $storage->change_title($_REQUEST['type'], $_REQUEST['title'], $_REQUEST['id'], $_REQUEST['ext']);
		$json = array('result' => $ret ? 'ok' : 'fail');	
		break;
	default:
		break;
}

echo json_encode($json);

?>