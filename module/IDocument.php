<?php 

interface IDocument {
	public function create_document($title);
	public function create_layout($title);
	public function list_document();
	public function create_resource($title, $type, $ext, $data);
	
}

?>