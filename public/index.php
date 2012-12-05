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
        <script type="text/javascript" src="/lib/ace/ace.js"></script>        
        <link rel="stylesheet" href="/main.css">        
                
    </head>
    <body>
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
        
<script type="text/javascript" src="http://feather.aviary.com/js/feather.js"></script>

<!-- Instantiate Feather -->
<script type="text/javascript">

        function scroll($dom) {
            $('html,body').animate({ scrollTop : $($dom).offset().top - 150}, 'fast');
        }

		function proc(obj, callback){
			$.post("/proc.php", obj, callback);			
		}

        var featherEditor = new Aviary.Feather({
            apiKey: '340e1d562',
            apiVersion: 2,
            tools: 'all',
            language : 'ko',
            minimumStyling: true
        });

        function launchEditor(path, id, src, onSave, onClose) {
            featherEditor.launch({
                image: id,
                url: src,
                onSave: function(imageId, newURL) { 
                	if (onSave) onSave(imageId, newURL);
                },
                onClose: function(isDirty) {
                	if (onClose) onClose(isDirty);
                }
            });
            return false;
        }
        
	        function htmlEntities (str) {
	            return String(str).replace(/&/g, '&amp;')
	                              .replace(/</g, '&lt;')
	                              .replace(/>/g, '&gt;')
	                              .replace(/"/g, '&quot;')
	                              ;                 
	        }      
        
        	var editor_list = {
        		 setEditor: function(id, mode, theme) {
		            id = id || 'editor';
		            mode = mode || 'text';
		            theme = theme || 'chrome';
		            
		            this[id] = ace.edit(id);
		            this[id].setTheme("ace/theme/" + theme);
		            this[id].session.setMode('ace/mode/' + mode);
		            
		            var self = this; 
		            
		            setTimeout(function(){ 
		                self[id].focus();
		            }, 100);             
		        }, 
		        setHtmlEditor : function(id) {
		        	var self =  this;
		        	setTimeout(function(){
			           	$('#' + id).tinymce({
			                    script_url : '/lib/tiny_mce/tiny_mce.js',
			                    mode: 'none',
			                    theme : 'advanced',
			                    plugins : "autolink,lists,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,advlist",
			        
			                    // Theme options
			                    theme_advanced_buttons1 : "outdent,indent,blockquote,|,image,|,forecolor,backcolor,media,advhr,fullscreen",
			                    theme_advanced_buttons2 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,fontselect,fontsizeselect,|,bullist,numlist",
		
			                    //theme_advanced_buttons3 : "",
			                    theme_advanced_toolbar_location : "top",
			                    theme_advanced_toolbar_align : "left",
			                    theme_advanced_statusbar_location : "bottom",
			                    theme_advanced_resizing : false,
			        
			                    // Example content CSS (should be your site CSS)
			                    content_css : "/lib/bootstrap/css/bootstrap.css",
			        
			                    oninit: function() { 
			                        $('#' + id).tinymce().focus();
			                    },
			                    
			                    init_instance_callback: function(ed) { 
			                        ed.selection.getSel().collapseToEnd();
			                    },
			                    
			                    setup: function(ed){
			                        ed.onKeyDown.add(function(ed, evt){
			                            if (evt.altKey) {  // if press Alt + s  key, save contents
			                            
			                            }
			                        }) 
			                    }
			                                        
			                })
		            },10);
		        }
        	};
        	
        	function open_popup(selector, open, close) { 
    			var $dom = $(selector);
    			
    			$('body').append($dom);
    			
    			$dom.bPopup({

					onOpen: function() {
						if (open) open.call(this, $dom);
						$dom.find(".close-popup").click(function(e){
							$dom.bPopup().close();
						})							
					},
					
					onClose: function() {
						$dom.css("opacity", 1.0).hide();
						if (close) close.call(this, $dom);
					}
    			})
        	}
        	
        	function close_popup(selector) {
        		$(selector).bPopup().close();
        	}
        	
        	function editor_resource(path, id, callback) {
        		var obj = {
        			cmd: "info resource",
        			path : path,
        			id : id
        		} 
        		$.get("/proc.php", obj, function(ret){
        			open_resource_popup(ret.result.path, ret.result.type, ret.result, callback);
        		})
        	}
        	
        	function close_resource_popup(type) {
        		close_popup("#resource_popup_" + type);
        	}
        
        	function open_resource_popup(path, type, resource, callback) {
        		type = type || 'markdown'; 
        		resource = resource || {};
        		var selector = "#resource_popup_" + type;
        		
        		if (type == 'html') {
        			
        			open_popup(selector, function($dom){
        				
        						$dom.find(".modal-header .download").click(function(e){
        							location.href = path + "/" + resource.id + ".resource";
        						})
        				
				        	    $dom.find(".modal-body").append(
					        		$("<textarea id='html_contents' rows='15' />").css({
				    	    			width: '100%'
				        			}).val(resource.data || "")
				        		);
				        		
				        		$dom.find(".title").val(resource.title)
				        	        	
				        		editor_list.setHtmlEditor("html_contents");
	
								$dom.find(".save,.saveas").on('click', function(e){
									var title = $dom.find('.title').val();
									var data = $('#html_contents').html();
									
									var obj = { 
										cmd : (resource.id && $(e.currentTarget).hasClass('saveas') == false) ? "update resource" : "create resource",
										path : path,
										type : type,										
										ext : type,
										title : title,
										data : data,
										id : ( $(e.currentTarget).hasClass('saveas') == false) ? "" : resource.id	
									}										
									
									$.post("/proc.php", obj, function(response){
										if (response.result) {
											if (callback) callback(response.result);
											else location.reload();
										}
									})
								})

        			}, function($dom){
        				$("#html_contents").tinymce().remove();
        				$("#html_contents").remove();
        				
        				$dom.find(".save,.saveas").off();        				
        			})
        			
        		} else if (type == 'code') {
        			
        			open_popup(selector, function($dom){
	
						$dom.find(".modal-header .download").click(function(e){
							location.href = path + "/" + resource.id + ".resource";
						})	
	
		        	    $dom.find(".modal-body").append(
			        		$("<div class='editor_container' />").css({
			        			position: 'relative',
				        		height: '300px',
			    	    		width: '100%'
			        		}).append( 
			        			$("<div id='code' />").css({
			        				'position' : 'absolute',
			        				'left' : '0px',
			        				'top' : '0px',
			        				'bottom' : '0px',
			        				'right' : '0px'
			        			}).text(resource.data || "")
			        		)
		        		);
		        		
		        		editor_list.setEditor("code", resource.ext || "txt");
				       	$dom.find(".title").val(resource.title)		        		

						$dom.find(".type").on('change', function(e){
		        			editor_list.code.session.setMode("ace/mode/" + $(this).val());
		        		}).val(resource.ext || "txt")

						$dom.find(".save,.saveas").on('click', function(e){
							var title = $dom.find('.title').val();
							var data = editor_list.code.getValue();
							var ext = $dom.find(".type").val();
							
							var obj = { 
								cmd : (resource.id && $(e.currentTarget).hasClass('saveas') == false) ? "update resource" : "create resource",
								path : $("#path").val(),
								type : type,
								ext : ext,
								title : title,
								data : data,
								id : ( $(e.currentTarget).hasClass('saveas') == false) ? "" : resource.id
							}
							
							$.post("/proc.php", obj, function(response){
								if (response.result) {
									if (callback) callback(response.result);
									else location.reload();
								}
							})								
						})
        			}, function($dom){
        				editor_list.code.destroy()
        				$dom.find(".editor_container").remove();
        				
        				$dom.find(".save,.saveas").off();        				
        			})        			
 	
        		} else if (type == 'markdown' || type == 'text') { 
        			
        			open_popup(selector, function($dom){
        				
						$dom.find(".modal-header .download").click(function(e){
							location.href = path + "/" + resource.id + ".resource";
						})        				
	
 						$dom.find(".modal-body").append(
			        		$("<div class='editor_container' />").css({
			        			position: 'relative',
				        		height: '300px',
			    	    		width: '100%'
			        		}).append( 
			        			$("<div id='data' />").css({
			        				'position' : 'absolute',
			        				'left' : '0px',
			        				'top' : '0px',
			        				'bottom' : '0px',
			        				'right' : '0px'
			        			}).text(resource.data || "")
			        		)
		        		);
		        	        	
		        		editor_list.setEditor("data", type);
					    $dom.find(".title").val(resource.title)		
						$dom.find(".save,.saveas").on('click', function(e){
							var title = $dom.find('.title').val();
							var data = editor_list.data.getValue();
							
							var obj = { 
								cmd : (resource.id && $(e.currentTarget).hasClass('saveas') == false) ? "update resource" : "create resource",
								path : $("#path").val(),
								type : type,
								ext : type,
								title : title,
								data : data,
								id : ( $(e.currentTarget).hasClass('saveas') == false) ? resource.id : "" 
							}
							
							$.post("/proc.php", obj, function(response){
								if (response.result) {
									if (callback) callback(response.result);
									else location.reload();
								}
							})								
						})

        			}, function($dom){
        				editor_list.data.destroy()
        				$dom.find(".editor_container").remove();
        				
        				$dom.find(".save,.saveas").off();
        			})     
        			        			
        		} else {
        			if (resource.mime.indexOf("image") > -1) {
        				$("<img />").attr({
        					'src' : path + "/" + resource.id + ".resource",
        					'id' : resource.id
        				}).appendTo('body');
        				launchEditor($("#path").val(), resource.id, "http://" + location.hostname + "/" + real_path(path + "/" + resource.id + ".resource"), function(imageId, newURL){
        					
        					proc({
        						cmd: "update resource",
        						url : newURL,
        						id : resource.id,
        						type: resource.type,
        						ext : resource.ext,
        						path : path
        					}, function(response){
        						console.log(response);
        					});
        					
        					$("#" + resource.id).remove();
        				}, function(isDirty){
        					console.log(isDirty);
        				})
        			}
        		}
 
        	}
        	
        	function real_path(path) {
        		var arr = path.split("."); 
        		if (arr[0] == ".") {
        			arr.pop();
        		}	
        		
        		return arr.join(".");
        	}
        	
			</script>       
        
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
        
        <script type="text/javascript">
        
        function select_active($dom) {
        	$(".manager .active").removeClass("active");
        	$dom.addClass('active');
        }
        
        function add_storage_list($dom, list) {
        	for(var i = 0; i < list.length; i++) {
        		$dom.append($("<option />").text(list[i].title).val(list[i].id).attr('selected', list[i].selected));	
        	}
        }
        
        function add_tree_list($dom, list) {
    		var $ul = $("<ul class='nav nav-list' />");
    		var data = list;
    		
    		for(var i = 0; i < data.directory.length; i++){
    			$ul.append(
    				$("<li class='nav-header document' />").append(
    					$("<a draggable='true' />").append(data.directory[i].title).data('info', data.directory[i]).css('cursor', 'pointer').click(function(e){
    						var $a = $(this);
    						var data = $(this).data("info");
    						
    						if ($a.parent().find("ul").length) {
								$a.parent().find("ul").toggle();			
    						} else {
	    						proc({
	    							cmd : 'list document',
	    							path : data.path + "/" + data.id
	    						}, function(response) {
									add_tree_list($a.parent(), response.result)	    							
	    						})    							
    						}
    						
    						select_active($a.parent());
    					})
    				)
    			);
    		}
    		
    		if (data.layout.length > 0) $ul.append( $("<li  class='divider'/>") );
    		
    		for(var i = 0; i < data.layout.length; i++){
    			$ul.append(
    				$("<li class='layout' />").append(
    					
    					$("<a draggable='true' />").append(data.layout[i].title + ".layout" ).data('info', data.layout[i]).css('cursor', 'pointer').click(function(e){
    						var $a = $(this);
    						
    						select_active($a.parent());
    					})
    				)
    			);
    		}
    		
    		if (data.resource.length > 0) $ul.append( $("<li  class='divider'/>") );        		        		
    		
    		for(var i = 0; i < data.resource.length; i++){
    			$ul.append(
    				$("<li class='resource' />").append(
    					$("<a draggable='true' />").append(data.resource[i].title + "." + data.resource[i].ext).data('info', data.resource[i]).css('cursor', 'pointer').click(function(e){
    						var $a = $(this);
    						
    						select_active($a.parent());
    					})
    				)
    			);
    		}        	    
    		
    		$ul.find("a").on('dragstart', function(e){
			  e.dataTransfer.effectAllowed = 'move';
			  e.dataTransfer.setData('application/json', JSON.stringify($(this).data('info')));
    		});
    		
    		$dom.append($ul);
        }
        
        $(function(e){
        	
        	$('.manager .minus-btn').click(function(e){
        		proc({
        			cmd: 'remove storage',
        			id : $("#storage").val()
        		}, function(response) {
        			location.reload();
        		})
        	})
        	
        	$(".manager .plus-btn").click(function(e){
        		open_popup("#storage_popup", function($dom){
        			$dom.find("input[type=radio]").click(function(e){
        				var id = $(e.currentTarget).attr('id');
        				console.log(id);
        				
        				$dom.find(".StorageConfig").hide();

        				$dom.find('#' + id + 'Config').show();
        			})
        			
        			$dom.find(".save").click(function(e){
        				 var type  = $dom.find("input:radio:checked").val();
        				 var obj = {};
        				 
        				 if (type == 'file') { 
        				 	obj = {
        				 		type : type,
        				 		title : $dom.find("#fileTitle").val(),
        				 		root : $dom.find("#fileRoot").val()
        				 	}
        				 } else if (type == 'ftp' ) {
        				 	obj = {
        				 		type : type,        				 		
        				 		title : $dom.find("#ftpTitle").val(),
        				 		root : $dom.find("#ftpRoot").val(),
        				 		host : $dom.find("#ftpHost").val(),
        				 		port : $dom.find("#ftpPort").val(),
        				 		username : $dom.find("#ftpUsername").val(),
        				 		password : $dom.find("#ftpPassword").val(),
        				 	}        				 	
        				 }
        				 
        				 proc({
        				 	cmd : 'create storage',
        				 	obj : obj 
        				 }, function(response){
        				 	location.reload();
        				 })
        			})

        		})
        	})
        	
        	$(".manager .splitter").css('cursor', 'pointer').click(function(e){
        		if (parseInt($(".manager").css('left')) == 0) { 
							$(".manager").animate({
								'left' : '-290px'	
							}, 200, function(){
								$(".container").each(function(index, elem){ 
										$(elem).css({
											'margin-left' : 'auto'
										})
								})
							});
							
								
							$(".manager .splitter i").removeClass("icon-chevron-left").addClass("icon-chevron-right");							
        		} else {
        			
							$(".container").each(function(index, elem){
								if (parseInt($(elem).css('margin-left')) < 310) { 
									$(elem).animate({
										'margin-left': '310px'
									}, 100)
								}
							})
							        			
							$(".manager").animate({
								'left' : '0px'	
							}, 200);
							
							$(".manager .splitter i").removeClass("icon-chevron-right").addClass("icon-chevron-left");

							
        		}
        	});
        	
        	proc({ cmd : 'list document',  	path : "."}, function(response) {
        		add_tree_list($('.manager .tree'), response.result);
        	})

        	proc({ cmd : 'list storage'}, function(response) {
        		add_storage_list($('#storage'), response.result);
        		$('#storage').on('change', function(e){
        			
        			if ($("#storage").val()) {
	        			proc ({
	        				cmd : 'select storage',
	        				id : $("#storage").val()
	        			}, function(response) {
	        				if (response.result) {
	        					location.reload();
	        				}
	        			});        				
        			}

        		})
        	})        	

        })
        </script>
    </body>
</html>