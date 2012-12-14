<?php

session_start();

include_once "../module/Storage.php";


if (strstr($_SERVER['PATH_INFO'], "@")){
	$arr = explode("@", $_SERVER['PATH_INFO']);
	$storageId = str_replace("/", "", $arr[0]);
	$path = ".".str_replace($storageId."@", "", $_SERVER['PATH_INFO']);
	
} else {
	$storageId = Storage::getCurrentStorage();
	$path = ".".$_SERVER['PATH_INFO'];	
}




if ($storageId) {
	
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
		$document_root = "/".$storageId."@";
	} else {
		$document_root = "/".$storageId."@".$path."/";	
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
        <link rel="stylesheet" href="/logic/main.css">
        <link rel="stylesheet" href="/lib/google-code-prettify/prettify.css">
        <link rel="stylesheet" href="/lib/jquery-miniColors/jquery.miniColors.css">
                

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
    	
        <script type="text/javascript" src="/lib/json2.js"></script>
        <script type="text/javascript" src="/lib/jquery-1.7.1.min.js"></script>
    	
    	<input type="hidden" id="path" value="<?php echo $path ?>" />
    	<input type="hidden" id="document_root" value="<?php echo $document_root ?>" />
    	<input type="hidden" id="storageId" value="<?php echo $storageId ?>" />
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
		    	<div class='btn-group'>
		    		<button type="button" class="btn preview" title="Preview"><i class='icon-zoom-in'></i></button>
			  		<button type="button" class="btn download" title="Download"><i class='icon-download'></i></button>
			  		<button type="button" class="btn close-popup" data-dismiss="modal" aria-hidden="true"><i class='icon-remove'></i></button>
		    	</div>
		    </div>
	    	
	    </h3>

	  </div>
	  <div class="modal-body">
	    <input type="text" class="input-block-level title" placeholder="Input Title"/>
	    <div>
	        <div class="editor"></div>
	        <div class="preview" style="display:none;height:300px;overflow:auto"></div>
	    </div>
	    
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
			<div class='menu'>

				<div class='box'>
					<select id="storage" style="width:170px;margin-bottom: 0px">
						<option>Select Storage</option>
					</select>
					<button class='plus-btn'><i class='icon-plus'></i></button>						
					<button class='minus-btn'><i class='icon-minus'></i></button>						
				</div>

			</div>
	</div>
	<div class="splitter">
		<i class="icon-chevron-right" style="position: absolute;top:50%;"></i>
	</div>
</div>

<?php if ($type == 'layout') { ?>
<div class="config">
    
    <div class="splitter">
        <i class="icon-chevron-left" style="position: absolute;top:50%;"></i>
    </div>
    
    <div class="config-form">
        <div class="config-info"></div>
        <div class="config-attr">
            
        </div>


        <ul class="nav nav-pills" id="config-tab">
          <li class="active"><a href="#config-background" data-toggle='pill'>Back</a>
          </li>
          <li><a href="#config-border" data-toggle='pill'>Border</a></li>
          <li><a href="#config-box" data-toggle='pill'>Box</a></li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane active box" id="config-background">
             <div class="control-group main-config">
                 <label class="control-label" for="style-background-color">
                     Color
                     <div class='input-append pull-right'>
                     	<input type="text" id="style-background-color" placeholder="#000000" class='editor colors'>	
                     </div>
                     
                </label>
              </div>
             <div class="control-group main-config">
                <label class="control-label" for="style-background-image">
                    Image
                    <div class='input-append pull-right'> 
                    	<input type="text" id="style-background-image" placeholder="url(/image/test.jpg)" class='editor'>
                    </div>
                </label>
              </div>
              
             <div class="control-group main-config">
                <label class="control-label" for="style-background-attachment">
                    Attach
                   <select class='editor pull-right' id="style-background-attachment">
                       <option>scroll</option>
                       <option>fixed</option>
                       <option>inherits</option>
                   </select>
                </label>
              </div>    
              
             <div class="control-group main-config">
                <label class="control-label" for="style-background-position">
                    Position 
                    <input type="text" id="style-background-position" placeholder="0% 0%" class='editor pull-right'>
                </label>
              </div>
              
             <div class="control-group main-config">
                <label class="control-label" for="style-background-repeat">
                    Repeat 
                   <select class='editor pull-right' id="style-background-repeat">
                       <option>repeat</option>
                       <option>repeat-x</option>
                       <option>repeat-y</option>
                       <option>no-repeat</option>
                       <option>inherit</option>
                   </select>
                </label>
              </div>
              
             <div class="control-group main-config">
                <label class="control-label" for="style-background-clip">
                    Clip 
                   <select class='editor pull-right' id="style-background-clip">
                       <option>border-box</option>
                       <option>content-box</option>
                       <option>padding-box</option>
                   </select>
                </label>
              </div>
              
             <div class="control-group main-config">
                <label class="control-label" for="style-background-origin">
                    Origin 
                   <select class='editor pull-right' id="style-background-origin">
                       <option>border-box</option>
                       <option>content-box</option>
                       <option>padding-box</option>
                   </select>
                </label>
              </div>
              
             <div class="control-group main-config">
                 <label class="control-label" for="style-background-size">
                     Size
                     <input type="text" id="style-background-size" placeholder="100% 100%" class='editor pull-right'>
                     
                </label>
              </div>                                                                                                          
            </div>

            <div id="config-border" class="tab-pane box">
             <div class="control-group main-config">
                 <label class="control-label" for="style-border-color">
                     Color
                     
                       <div class="input-append pull-right">
                            <input type="text" id="style-border-color" placeholder="#000000" class='editor'>
                            <a class="btn pull-right"><i class="icon-chevron-right"></i></a>
                       </div>                                                
                </label>
                
              </div>
              
	            <div id="config-border-color" style='display:none' class="sub-config">
	                <div class="control-group">
	                    <label class="control-label" for="style_border-color_border-top-color">
	                        Top
	                       <div class="input-append pull-right">
	                            <input type="text" id="style_border-color_border-top-color" placeholder="#000000" class='editor colors'>
	                       </div>                                                               
	                       
	                    </label>                    
	                </div>
	                <div class="control-group">
	                    <label class="control-label" for="style_border-color_border-right-color">
	                        Right
	                       <div class="input-append pull-right">
	                            <input type="text" id="style_border-color_border-right-color" placeholder="#000000" class='editor colors'>
	                       </div>                                                               
	                       
	                    </label>                    
	                </div>
	                <div class="control-group">
	                    <label class="control-label" for="style_border-color_border-bottom-color">
	                        Bottom
	                       <div class="input-append pull-right">
	                            <input type="text" id="style_border-color_border-bottom-color" placeholder="#000000" class='editor colors'>
	                       </div>                                                               
	                       
	                    </label>                    
	                </div>
	                <div class="control-group">
	                    <label class="control-label" for="style_border-color_border-left-color">
	                        Left
	                       <div class="input-append pull-right">
	                            <input type="text" id="style_border-color_border-left-color" placeholder="#000000" class='editor colors'>
	                       </div>                                                               
	                       
	                    </label>                    
	                </div>      
	            </div>              
              
    
              
             <div class="control-group main-config">
                 <label class="control-label" for="style-border-width">
                     Width
                     
                       <div class="input-append pull-right">
                            <input type="text" id="style-border-width" placeholder="0px" class='editor'>
                            <a class="btn pull-right"><i class="icon-chevron-right"></i></a>
                       </div>                           
                </label>
              </div>
              
	            <div id="config-border-width" style='display:none' class="sub-config">
	                <div class="control-group">
	                    <label class="control-label" for="style_border-width_border-top-width">
	                        Top
	                       <div class="input-append pull-right">
	                            <input type="text" id="style_border-width_border-top-width" placeholder="0px" class='editor'>
	                       </div>                                                               
	                       
	                    </label>                    
	                </div>
	                <div class="control-group">
	                    <label class="control-label" for="style_border-width_border-right-width">
	                        Right
	                       <div class="input-append pull-right">
	                            <input type="text" id="style_border-width_border-right-width" placeholder="0px" class='editor'>
	                       </div>                                                               
	                       
	                    </label>                    
	                </div>
	                <div class="control-group">
	                    <label class="control-label" for="style_border-width_border-bottom-width">
	                        Bottom
	                       <div class="input-append pull-right">
	                            <input type="text" id="style_border-width_border-bottom-width" placeholder="0px" class='editor'>
	                       </div>                                                               
	                       
	                    </label>                    
	                </div>
	                <div class="control-group">
	                    <label class="control-label" for="style_border-width_border-left-width">
	                        Left
	                       <div class="input-append pull-right">
	                            <input type="text" id="style_border-width_border-left-width" placeholder="0px" class='editor'>
	                       </div>                                                               
	                       
	                    </label>                    
	                </div>      
	
	            </div>              
         
              
             <div class="control-group main-config">
                <label class="control-label" for="style-border-style">
                    Style 
                   <div class="input-append pull-right">
                        <input type="text" id="style-border-style" placeholder="dashed" class='editor'>
                        <a class="btn pull-right"><i class="icon-chevron-right"></i></a>
                   </div>                   
                </label>
              </div>
              
	            <div id="config-border-style" style='display:none' class="sub-config">
	                <div class="control-group">
	                    <label class="control-label" for="style_border-style_border-top-style">
	                        Top
	                       <div class='pull-right'>
	                           <select class='editor' id="style_border-style_border-top-style">
	                               <option>none</option>
	                               <option>hidden</option>
	                               <option>solid</option>
	                               <option>dashed</option>
	                               <option>dotted</option>
	                               <option>double</option>
	                               <option>groove</option>
	                               <option>ridge</option>
	                               <option>inset</option>
	                               <option>outset</option>
	                           </select>                           
	                       </div>                                                          
	                       
	                    </label>                    
	                </div>
	                <div class="control-group">
	                    <label class="control-label" for="style_border-style_border-right-style">
	                        Right
	                       <div class='pull-right'>
	                           <select class='editor' id="style_border-style_border-right-style">
	                               <option>none</option>
	                               <option>hidden</option>
	                               <option>solid</option>
	                               <option>dashed</option>
	                               <option>dotted</option>
	                               <option>double</option>
	                               <option>groove</option>
	                               <option>ridge</option>
	                               <option>inset</option>
	                               <option>outset</option>
	                           </select>            
	                        </div>           
	                    </label>                    
	                </div>
	                <div class="control-group">
	                    <label class="control-label" for="style_border-style_border-bottom-style">
	                        Bottom
	                       <div class='pull-right'>
	                           <select class='editor' id="style_border-style_border-bottom-style">
	                               <option>none</option>
	                               <option>hidden</option>
	                               <option>solid</option>
	                               <option>dashed</option>
	                               <option>dotted</option>
	                               <option>double</option>
	                               <option>groove</option>
	                               <option>ridge</option>
	                               <option>inset</option>
	                               <option>outset</option>
	                           </select>         
	                       </div>
	                    </label>                    
	                </div>
	                <div class="control-group">
	                    <label class="control-label" for="style_border-style_border-left-style">
	                        Left
	                       <div class='pull-right'>
	                           <select class='editor'  id="style_border-style_border-left-style">
	                               <option>none</option>
	                               <option>hidden</option>
	                               <option>solid</option>
	                               <option>dashed</option>
	                               <option>dotted</option>
	                               <option>double</option>
	                               <option>groove</option>
	                               <option>ridge</option>
	                               <option>inset</option>
	                               <option>outset</option>
	                           </select>        
	                       </div>
	                       
	                    </label>                    
	                </div>      
	            </div>                   
    
             <div class="control-group main-config">
                <label class="control-label" for="style-border-radius">
                    Radius
                   <div class="input-append pull-right">
                        <input type="text" id="style-border-radius" placeholder="0px" class='editor'>
                        <a class="btn pull-right"><i class="icon-chevron-right"></i></a>
                   </div>                                                               
                   
                </label>
              </div>
              
	            <div id="config-border-radius" style='display:none' class="sub-config"> 
	                <div class="control-group">
	                    <label class="control-label" for="style_border-radius_border-top-radius">
	                        Top
	                       <div class="input-append pull-right">
	                            <input type="text" id="style_border-radius_border-top-radius" placeholder="0px" class='editor'>
	                       </div>                                                               
	                       
	                    </label>                    
	                </div>
	                <div class="control-group">
	                    <label class="control-label" for="style_border-radius_border-right-radius">
	                        Right
	                       <div class="input-append pull-right">
	                            <input type="text" id="style_border-radius_border-right-radius" placeholder="0px" class='editor'>
	                       </div>                                                               
	                       
	                    </label>                    
	                </div>
	                <div class="control-group">
	                    <label class="control-label" for="style_border-radius_border-bottom-radius">
	                        Bottom
	                       <div class="input-append pull-right">
	                            <input type="text" id="style_border-radius_border-bottom-radius" placeholder="0px" class='editor'>
	                       </div>                                                               
	                       
	                    </label>                    
	                </div>
	                <div class="control-group">
	                    <label class="control-label" for="style_border-radius_border-left-radius">
	                        Left
	                       <div class="input-append pull-right">
	                            <input type="text" id="style_border-radius_border-left-radius" placeholder="0px" class='editor'>
	                       </div>                                                               
	                       
	                    </label>                    
	                </div>      
	            </div>                           
                                  
             </div>



            <div id="config-box" class="tab-pane box">
             <div class="control-group main-config">
                 <label class="control-label" for="style-padding">
                     Padding
                     
                       <div class="input-append pull-right">
                            <input type="text" id="style-padding" placeholder="0px" class='editor'>
                            <a class="btn pull-right"><i class="icon-chevron-right"></i></a>
                       </div>                                          
                </label>
              </div>
              
	            <div id="config-padding" style='display:none' class="sub-config"> 
	                <div class="control-group">
	                    <label class="control-label" for="style_padding_padding-top">
	                        Top
	                       <div class="input-append pull-right">
	                            <input type="text" id="style_padding_padding-top" placeholder="0px" class='editor'>
	                       </div>                                                               
	                       
	                    </label>                    
	                </div>
	                <div class="control-group">
	                    <label class="control-label" for="style_padding_padding-right">
	                        Right
	                       <div class="input-append pull-right">
	                            <input type="text" id="style_padding_padding-right" placeholder="0px" class='editor'>
	                       </div>                                                               
	                       
	                    </label>                    
	                </div>
	                <div class="control-group">
	                    <label class="control-label" for="style_padding_padding-bottom">
	                        Bottom
	                       <div class="input-append pull-right">
	                            <input type="text" id="style_padding_padding-bottom" placeholder="0px" class='editor'>
	                       </div>                                                               
	                       
	                    </label>                    
	                </div>
	                <div class="control-group">
	                    <label class="control-label" for="style_padding_padding-left">
	                        Left
	                       <div class="input-append pull-right">
	                            <input type="text" id="style_padding_padding-left" placeholder="0px" class='editor'>
	                       </div>                                                               
	                       
	                    </label>                    
	                </div>      
	            </div>                   
             <div class="control-group main-config">
                 <label class="control-label" for="style-margin">
                     Margin
                     
                       <div class="input-append pull-right">
                            <input type="text" id="style-margin" placeholder="0px" class='editor'>
                            <a class="btn pull-right"><i class="icon-chevron-right"></i></a>
                       </div>                     
                </label>
              </div>
	            <div id="config-margin" style='display:none' class="sub-config"> 
	                <div class="control-group">
	                    <label class="control-label" for="style_margin_margin-top">
	                        Top
	                       <div class="input-append pull-right">
	                            <input type="text" id="style_margin_margin-top" placeholder="0px" class='editor'>
	                       </div>                                                               
	                       
	                    </label>                    
	                </div>
	                <div class="control-group">
	                    <label class="control-label" for="style_margin_margin-right">
	                        Right
	                       <div class="input-append pull-right">
	                            <input type="text" id="style_margin_margin-right" placeholder="0px" class='editor'>
	                       </div>                                                               
	                       
	                    </label>                    
	                </div>
	                <div class="control-group">
	                    <label class="control-label" for="style_margin_margin-bottom">
	                        Bottom
	                       <div class="input-append pull-right">
	                            <input type="text" id="style_margin_margin-bottom" placeholder="0px" class='editor'>
	                       </div>                                                               
	                       
	                    </label>                    
	                </div>
	                <div class="control-group">
	                    <label class="control-label" for="style_margin_margin-left">
	                        Left
	                       <div class="input-append pull-right">
	                            <input type="text" id="style_margin_margin-left" placeholder="0px" class='editor'>
	                       </div>                                                               
	                       
	                    </label>                    
	                </div>      
	            </div>                         
             <div class="control-group main-config">
                <label class="control-label" for="style-box-shadow">
                    Shadow 

                   <div class="input-append pull-right">
                        <input type="text" id="style-box-shadow" placeholder="50px 50px 5px black" class='editor'>
                        <a class="btn pull-right"><i class="icon-chevron-right"></i></a>
                   </div>
                   
                </label>
              </div>
              
	            <div id="config-box-shadow" style='display:none' class="sub-config"> 
	                <div class="control-group">
	                    <label class="control-label" for="style_box-shadow_h-shadow">
	                        H-Shadow
	                       <div class="input-append pull-right">
	                            <input type="text" id="style_box-shadow_h-shadow" placeholder="0px" class='editor'>
	                       </div>                                                               
	                       
	                    </label>                    
	                </div>
	                <div class="control-group">
	                    <label class="control-label" for="style_box-shadow_v-shadow">
	                        V-Shadow
	                       <div class="input-append pull-right">
	                            <input type="text" id="style_box-shadow_v-shadow" placeholder="0px" class='editor'>
	                       </div>                                                               
	                       
	                    </label>                    
	                </div>
	                <div class="control-group">
	                    <label class="control-label" for="style_box-shadow_blur">
	                        Blur
	                       <div class="input-append pull-right">
	                            <input type="text" id="style_box-shadow_blur" placeholder="0px" class='editor'>
	                       </div>                                                               
	                       
	                    </label>                    
	                </div>
	                <div class="control-group">
	                    <label class="control-label" for="style-box-shadow-spread">
	                        Spread
	                       <div class="input-append pull-right">
	                            <input type="text" id="style_box-shadow_spread" placeholder="0px" class='editor'>
	                       </div>                                                               
	                       
	                    </label>                    
	                </div>
	                
	                <div class="control-group">
	                    <label class="control-label" for="style-box-shadow-color">
	                        Color
	                       <div class="input-append pull-right">
	                            <input type="text" id="style_box-shadow_color" placeholder="#000000" class='editor colors'>
	                       </div>                                                               
	                       
	                    </label>                    
	                </div>      	           
	                
	                <div class="control-group">
	                    <label class="control-label" for="style_box-shadow_inset">
	                        Inset
	                       <div class="input-append pull-right">
	                            <input type="checkbox" id="style_box-shadow_inset">
	                       </div>                                                               
	                       
	                    </label>                    
	                </div>      	                      
	            </div>               
              
             <div class="control-group main-config">
                <label class="control-label" for="style-transform">
                    Transform
                    
                   <div class="input-append pull-right">
                        <input type="text" id="style-transform" placeholder="scale(1.5, 0.5)" class='editor'>
                        <a class="btn pull-right"><i class="icon-chevron-right"></i></a>
                   </div>

                </label>
              </div>                                        
            </div>

            
        </div>




                
    </div>
</div>
<?php } ?>

        <script type="text/javascript" src="/lib/jquery.ui/jquery-ui-1.8.20.custom.min.js"></script>
        <script type="text/javascript" src="/lib/jquery.ui/jquery.ui.touch-punch.min.js"></script>
        <script type="text/javascript" src="/lib/jquery.bPopup.min.js"></script>
        <script type="text/javascript" src="/lib/markdown.js"></script>
        <script type="text/javascript" src="/lib/tiny_mce/jquery.tinymce.js"></script>
        <script type="text/javascript" src="/lib/google-code-prettify/prettify.js"></script>
		<script type="text/javascript" src="/lib/ace/ace.js"></script>        
		<script type="text/javascript" src="http://feather.aviary.com/js/feather.js"></script>
		<script type="text/javascript" src="/lib/jquery-miniColors/jquery.miniColors.min.js"></script>
		<script type="text/javascript" src="/logic/main.js"></script>    	

    </body>
</html>