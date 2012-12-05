<?php 

$id = $_REQUEST['id'];

$root = realpath(getcwd()."/../storage/");

$dir = $root.DIRECTORY_SEPARATOR.$id;
$file = $dir.DIRECTORY_SEPARATOR."document.json";

if (file_exists($file)) {
	$obj = file_get_contents($file);	
}


include_once "write.html" 
 
?>

<script>
	App.document = JSON.parse('<?php echo $obj ?>');
</script>