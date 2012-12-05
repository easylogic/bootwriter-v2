<?php

$category = $storage->list_category();

$directory_arr = explode("/", $document_root);

$len = count($category);

$grow_path = "";

?>

<ul class="breadcrumb">
  <li><a href="/">Home</a> <?php echo (count($category) > 0) ? '<span class="divider" >/</span>' : '' ?></li>
  <?php foreach ($category as $key => $dir) { ?>
  	  <li >
  	  		<a href="<?php echo ($grow_path != "") ? $grow_path : "" ?>/<?php echo $dir->id ?>"><?php echo $dir->title ?></a>
  	  		 
  	  		<?php if ($len - 1 > $key) { ?>
  	  			<span class="divider">/</span>
  	  			<?php 	$grow_path .= "/".$dir->id; ?>  	  			
  	  		<?php } ?>
 
  	  </li>
  <?php } ?>

</ul>