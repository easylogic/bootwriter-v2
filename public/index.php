<?php

session_start();

include_once "../module/Storage.php";

$storageId = Storage::getCurrentStorage();

if ($storageId) {
	$path = ".".$_SERVER['PATH_INFO'];
	
	$arr = explode("/", $path);
	$file = array_pop($arr);
	
	if (strstr($file, ".layout")) {
		$title = "Layout";
		$type = "layout";
		$is_layout = true; 
		$layout = $file;
		$path = implode("/", $arr);	 
		$id = str_replace(".layout", "", $layout);
	} else if (strstr($file, ".resource")) {
		$title = "Resource";
		$type = "resource";
		$resource = $file;
		$path = implode("/", $arr);
		$id = str_replace(".resource", "", $resource);		 	
	} else  {
		$title = "Document";
		$type = "document";	
		$is_document = true; 
		$id = $file;
	}
	
	
	/*
	$storage = new FileDocument($root, $path);
	*/
	$root = "storage";
	$storage = Storage::load($storageId, $path);
	
	if (in_array($type, array("resource", "layout"))) {
		$realfile = $storage->path($file);
	} else {
		$realfile = $storage->path("document");
	}
	
	// real object 
	if ($file == ".") {
		$doc = (object)array();	
	} else {
		$doc = $storage->get($realfile);	
	}
	
	
	
	if ($path == '.'){
		$document_root = "/";
	} else {
		$document_root = "/".$path."/";	
	}
	
	if ($type == 'resource') {
		$storage->output($id);
		exit;
	}
	
}


?>

<!DOCTYPE html>
<html lang="ko">
    <head>
        <title>EasyLogic</title>
        <meta charset='UTF-8' />
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta http-equiv="X-UA-Compatible" content="IE-EmulateIE8">
        <!--[if lt IE 9]><script src="/lib/html5.js">   </script><![endif]-->
        <link rel="stylesheet" href="/lib/jquery.ui/smoothness/jquery-ui-1.8.20.custom.css">
        <link rel="stylesheet" href="/lib/bootstrap/css/bootstrap.min.css">
        <!--link rel="stylesheet" href="/lib/bootstrap-toggle-buttons/static/stylesheets/bootstrap-toggle-buttons.css"-->
        
        <link rel="stylesheet" href="/lib/google-code-prettify/prettify.css">
                
        <script type="text/javascript" src="/lib/json2.js"></script>
        <script type="text/javascript" src="/lib/jquery-1.7.1.min.js"></script>
        <script type="text/javascript" src="/lib/jquery.ui/jquery-ui-1.8.20.custom.min.js"></script>
        <script type="text/javascript" src="/lib/jquery.ui/jquery.ui.touch-punch.min.js"></script>
        <!--script type="text/javascript" src="/lib/bootstrap-toggle-buttons/static/js/jquery.toggle.buttons.js"></script-->
        <!-- script type="text/javascript" src="/lib/underscore-min.js"></script -->
        <!-- script type="text/javascript" src="/lib/backbone-min.js"></script -->
        <!-- script type="text/javascript" src="/lib/require-2.0.js"></script -->
        <!-- script type="text/javascript" src="/logic/main-built.js"></script -->
        

        <!--script type="text/javascript" src="/lib/beautify-html.js"></script-->
        <script type="text/javascript" src="/lib/jquery.bPopup.min.js"></script>
        <script type="text/javascript" src="/lib/markdown.js"></script>
        <script type="text/javascript" src="/lib/tiny_mce/jquery.tinymce.js"></script>
        <!--script type="text/javascript" src="/lib/less-1.3.0.min.js"></script-->
        <script type="text/javascript" src="/lib/google-code-prettify/prettify.js"></script>
        <script type="text/javascript">
        		/*
            require.config({
                paths : {
                    'jade' : '/lib/requirejs.plugin/jade-0.25',
                    'text' : '/lib/requirejs.plugin/text',
                    'jadeRuntime' : '/lib/requirejs.plugin/jadeRuntime'
                }
            }); */

            // global object
            var App = {
                mode : 'write',
                GridWidth: 60,
                GridGutter: 20,
                MaxSpan: 12,
                MinSpan: 1,          
                MaxOffset: 11,
                MinOffset: 0
                      
            };

        </script>
    </head>
    <body>
    	
		<script type="text/javascript" src="/lib/ace/ace.js"></script>        
		<link rel="stylesheet" href="/logic/main.css">              
		<script type="text/javascript" src="http://feather.aviary.com/js/feather.js"></script>
		<script type="text/javascript" src="/logic/main.js"></script>    	
    	
    	<input type="hidden" id="path" value="<?php echo $path ?>" />
    	<input type="hidden" id="document_root" value="<?php echo $document_root ?>" />
        <div class="container">
            <div class="navbar">
                <div class="navbar-inner">
                        <a data-toggle="collapse" data-target="#main-collapse" class="btn btn-navbar">
                            <span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span>
                        </a>
                        <a href="/" class="brand"><?php echo $title ?></a>
                        <div class="nav-collapse collapse">
                        <ul class="nav">
                            <li class="divider-vertical"></li>
                            <li id="menubox"></li>
                            <li id="menubox3"></li>
                        </ul>
                        <ul class="nav pull-right">
                            <li class="divider-vertical"></li>
                            <li id="menubox2"></li>
                        </ul>                        
                        </div>
                </div>
            </div>
        </div>
        <div class="container" id="main"> 
        	<?php 
        		if ($storageId) {
        			include_once "{$type}.php";
        		} else {
        	?>
        			&lt;--------- Select Storage!!
					<?		
        		}
       		?>	
        </div>
        <footer class="footer">
            <div class="container">
                <div style="height:10px;"></div>
                <ul class="unstyled">
                    <li class="copyright">
                        &copy; 2012 easylogic.co.kr
                    </li>
                </ul>
            </div>
        </footer>
        
        <script type="text/javascript" src="/lib/bootstrap/js/bootstrap.min.js"></script>        
        <script type="text/javascript" src="/lib/jquery-filedrop/jquery.filedrop.js"></script>        

<div class="hide" id="layout_popup">
	<div class="modal">
	  <div class="modal-header">
	  	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	    <h3>Layout</h3>
	  </div>
	  <div class="modal-body">
	    <input type="text" class="input-block-level" placeholder="Input Layout Title" id="layout_title" />
	  </div>
	  <div class="modal-footer">
	    <a href="#" class="btn btn-primary save">Create Layout</a>
	  </div>
	</div>
</div>

<div class="hide" id="resource_popup">
	<div class="modal">
	  <div class="modal-header">
	  	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	    <h3>Resource</h3>
	  </div>
	  <div class="modal-body" style="padding-left:50px;">
	    
	<ul class="thumbnails">
		<li class="span2">
			<a href="#text" class='thumbnail' style='text-align: center;background:#eee;'>
				<img src="http://placehold.it/200x200" width="200" height="100" />
				<span class="caption">Text</span>
			</a>
			
		</li>
		<li class="span2">			
			<a href="#html" class='thumbnail' style='text-align: center;background:#eee;'>
				<img src="http://placehold.it/200x200" width="200" height="100" />
				<span class="caption">Html</span>
			</a>
			
		</li>	
		<li class="span2">
			<a href="#markdown" class='thumbnail' style='text-align: center;background:#eee;'>
				<img src="http://placehold.it/200x200" width="200" height="100" />
				<span class="caption">MarkDown</span>
			</a>
			
		</li>			
	
		<li class="span2">			
			<a href="#code" class='thumbnail' style='text-align: center;background:#eee;'>
				<img src="http://placehold.it/200x200" width="200" height="100" />
				<span class="caption">Code</span>
			</a>						
			
		</li>
		
	</ul>
	    
	    
	  </div>
	</div>
</div>

<div class="hide" id="resource_upload_popup">
	<div class="modal">
	  <div class="modal-header">
	  	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	    <h3>Upload</h3>
	  </div>
	  <div class="modal-body">
	  	<form class="form-horizontal" action="/proc.php" enctype="multipart/form-data" method="post" target="upload_frame" onsubmit="get_result()">
	  		
			<div class="control-group">
			    <label class="control-label" for="inputEmail">Resource</label>
			    <div class="controls">
			      <input type="hidden" name="path" value="<?php echo $path ?>" />
			      <input type="hidden" name="cmd" value="create resource" />
			      <input type="hidden" name="upload_type" value="submit" />
			      <input type="file" name="data" />
			      
			    </div>
			</div>	  		
	  		
			<div class="form-actions">
			  <button type="submit" class="btn btn-primary pull-right">Upload Resource</button>
			</div>	  		
	  		
	  	</form>
	  	<iframe name="upload_frame" style="display:none"></iframe>
	  </div>
	</div>
</div>


<div class="hide" id="change_popup">
	<div class="modal">
	  <div class="modal-header">
	  	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	    <h3>Change Title</h3>
	  </div>
	  <div class="modal-body">
	    <input type="text" class="input-block-level" placeholder="Input Title" id="change_title" />
	  </div>
	  <div class="modal-footer">
	    <a href="#" class="btn btn-primary save">Save Changes </a>
	  </div>
	</div>
</div>

<div class="hide" id="document_popup">
	<div class="modal">
	  <div class="modal-header">
	  	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	    <h3>Document</h3>
	  </div>
	  <div class="modal-body">
	    <input type="text" class="input-block-level" placeholder="Input Document Title" id="document_title" />
	  </div>
	  <div class="modal-footer">
	    <a href="#" class="btn btn-primary save">Create Document</a>
	  </div>
	</div>
</div>

<div class="hide" id="resource_popup_markdown">
	<div class="modal">
	  <div class="modal-header">
	    <h3>
	    	Markdown
		    <div class="pull-right">
		  		<button type="button" class="download" title="Download"><i class='icon-download'></i></button>
		  		<button type="button" class="close-popup" data-dismiss="modal" aria-hidden="true"><i class='icon-remove'></i></button>		  			    	    	
		    </div>
	    	
	    </h3>

	  </div>
	  <div class="modal-body">
	    <input type="text" class="input-block-level title" placeholder="Input Title"/>
	    
	    
	  </div>
	  <div class="modal-footer">
	    <a href="#" class="btn btn-primary save"><i class='icon-edit icon-white'></i> Save</a>
	    <a href="#" class="btn btn-danger saveas pull-left"><i class='icon-pencil icon-white'></i>  Save As</a>
	  </div>
	</div>
</div>

<div class="hide" id="resource_popup_text">
	<div class="modal">
	  <div class="modal-header">
	    <h3>
	    	Text
		    <div class="pull-right">
		  		<button type="button" class="download" title="Download"><i class='icon-download'></i></button>
		  		<button type="button" class="close-popup" data-dismiss="modal" aria-hidden="true"><i class='icon-remove'></i></button>		  			    	    	
		    </div>
	    </h3>
	  </div>
	  <div class="modal-body">
	    <input type="text" class="input-block-level title" placeholder="Input Title"/>
	    
	    
	  </div>
	  <div class="modal-footer">
	    <a href="#" class="btn btn-primary save"><i class='icon-edit icon-white'></i> Save</a>
	    <a href="#" class="btn btn-danger saveas pull-left"><i class='icon-pencil icon-white'></i>  Save As</a>
	  </div>
	</div>
</div>

<div class="hide" id="resource_popup_html">
	<div class="modal">
	  <div class="modal-header">
	    <h3>Html
	    
		    <div class="pull-right">
		  		<button type="button" class="download" title="Download"><i class='icon-download'></i></button>
		  		<button type="button" class="close-popup" data-dismiss="modal" aria-hidden="true"><i class='icon-remove'></i></button>		  			    	    	
		    </div>	    	
	    	
	    </h3>
	  </div>
	  <div class="modal-body">
	    <input type="text" class="input-block-level title" placeholder="Input Title"/>
	    
	    
	  </div>
	  <div class="modal-footer">
	    <a href="#" class="btn btn-primary save"><i class='icon-edit icon-white'></i> Save</a>
	    <a href="#" class="btn btn-danger saveas pull-left"><i class='icon-pencil icon-white'></i>  Save As</a>
	  </div>
	</div>
</div>

<div class="hide" id="resource_popup_code">
	<div class="modal">
	  <div class="modal-header">
	    <h3>Code
	    
		    <div class="pull-right">
		  		<button type="button" class="download" title="Download"><i class='icon-download'></i></button>
		  		<button type="button" class="close-popup" data-dismiss="modal" aria-hidden="true"><i class='icon-remove'></i></button>		  			    	    	
		    </div>	    	
	    	
	    </h3>
	  </div>
	  <div class="modal-body">
	    <select class="type span2">
	    	<option value='txt'>Text</option>
	    	<option value='html'>Html</option>
	    	<option value='js'>Javascript</option>
	    	<option value='css'>CSS</option>
	    </select>
	    <input type="text" class="span5 title" placeholder="Input Title"/>
	    
	    
	  </div>
	  <div class="modal-footer">
	    <a href="#" class="btn btn-primary save"><i class='icon-edit icon-white'></i> Save</a>
	    <a href="#" class="btn btn-danger saveas pull-left"><i class='icon-pencil icon-white'></i>  Save As</a>
	  </div>
	</div>
</div>

<div class="hide" id="progressbar_popup">
	<div class="modal">
	  <div class="modal-header">
	    <h3 class="modal-title">Upload</h3>
	  </div>
	  <div class="modal-body">
	  	
	  	<div class="all">
			<div class="progress progress-striped active">
			  <div class="bar" style="width: 0%;"></div>
			</div>  	  		
	  	</div>	  	
	  	
	  	<div class="file">
		  	<div class="file-name"></div>
			<div class="progress progress-striped active">
			  <div class="bar" style="width: 0%;"></div>
			</div>  	  		
	  	</div>

	  </div>
	</div>	
</div>

<div class="hide" id="storage_popup">
	<div class="modal">
	  <div class="modal-header">
	    <h3 class="modal-title">Add Storage</h3>
	  </div>
	  <div class="modal-body">
	  	<div>
	  		<label class="radio inline">
				  <input type="radio" id="StorageFile" name="storage_type" value="file" checked="checked"> File
				</label>
				<label class="radio inline">
				  <input type="radio" id="StorageFtp" name="storage_type" value="ftp"> Ftp
				</label>
	  	</div>
	  	
	  	<hr />
	  	
	  	<div id="StorageFileConfig" class="StorageConfig">
				<form class="form-horizontal">
				  <div class="control-group">
				    <label class="control-label" for="fileTitle">Title</label>
				    <div class="controls">
				      <input type="text" id="fileTitle" placeholder="Title">
				    </div>
				  </div>
				  <div class="control-group">
				    <label class="control-label" for="fileRoot">Root Directory</label>
				    <div class="controls">
							<div class="input-append">
							  <input type="text" id="fileRoot" placeholder="Root Directory">
							  <button class="btn" type="button">Find</button>
							</div>								    	
				       <span class="help-block">ex) ../storage </span>
				    </div>
				  </div>
				</form>	  		
	  	</div>

	  	<div id="StorageFtpConfig" class="StorageConfig" style="display:none" >
				<form class="form-horizontal">
				  <div class="control-group">
				    <label class="control-label" for="'"ftpTitle">Title</label>
				    <div class="controls">
				      <input type="text" id="ftpTitle" placeholder="Title">
				    </div>
				  </div>

				  <div class="control-group">
				    <label class="control-label" for="ftpHost">Host</label>
				    <div class="controls">
				      <input type="text" id="ftpHost" placeholder="Host"> : 
				      <input type="text" id="ftpPort" class="span1" placeholder="Port" value="21">
				    </div>
				  </div>
				  <div class="control-group">
				    <label class="control-label" for="ftpUsername">User Name</label>
				    <div class="controls">
				      <input type="text" id="ftpUsername" placeholder="User Name">
				    </div>
				  </div>
				  <div class="control-group">
				    <label class="control-label" for="ftpPassword">Password</label>
				    <div class="controls">
				      <input type="password" id="ftpPassword" placeholder="Password">
				    </div>
				  </div>
				  
				  <div class="control-group">
				    <label class="control-label" for="ftpRoot">Root Directory</label>
				    <div class="controls">
							<div class="input-append">
							  <input type="text" id="ftpRoot" placeholder="Root Directory">
							  <button class="btn" type="button">Find</button>
							</div>				    	
				       <span class="help-block">ex) storage </span>
				    </div>
				  </div>				  
				  				  
				</form>	  			  		
	  	</div>
	  	
	  </div>
	  <div class="modal-footer">
	    <a href="#" class="btn btn-primary save"><i class='icon-edit icon-white'></i> Save</a>
	    <a href="#" class="btn btn-danger saveas pull-left"><i class='icon-pencil icon-white'></i>  Save As</a>	  	
	  </div>
	</div>	
</div>

<div class="manager">
	
	<div class="tree">
			<div class='menu clearfix'>

				<div class='well'>
					<select id="storage" style="width:170px;margin-bottom: 0px">
						<option>Select Storage</option>
					</select>
					<button class='plus-btn'><i class='icon-plus'></i></button>						
					<button class='minus-btn'><i class='icon-minus'></i></button>						
				</div>

			</div>
	</div>
	<div class="splitter">
		<i class="icon-white icon-chevron-right" style="position: absolute;top:50%;"></i>
	</div>
</div>

    </body>
</html>