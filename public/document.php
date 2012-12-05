<?php

$list = $storage->list_document();

?>

<?php include_once "category.php" ?>

<div class='page-header'>
	<h1>
		Layouts <small> Layout & Slide </small>
		<div class='pull-right menu'>
			<a href='#' class='btn btn-large layout-btn'><i class='icon-plus'></i></a>
		</div>  		      				
	</h1>
</div>
<ul class="thumbnails">
	<?php foreach ($list['layout'] as $layout) { ?>
	<li class="span2">
		<a href="#<?php echo $document_root ?><?php echo $layout->id.".layout" ?>" class='thumbnail' style='text-align: center' rel="tooltip" title="<?php echo $layout->title ?>" data-type="layout">
			<img src="http://placehold.it/300x200" />
			<div class="caption" data-obj='<?php echo json_encode($layout) ?>'>
				<?php echo $layout->title ?>
			</div>    			
		</a>

		
	</li>
	<?php } ?>
	
</ul>

<div class="resource-view">
	

<div class='page-header'>
	<h1>
		Resources <small> Text, Html, MarkDown, Image, Movie, etc </small>
		<div class='pull-right menu btn-group'>
			<a href='#' class='btn btn-large resource-upload-btn' title="upload resource"><i class='icon-upload'></i></a>
			<a href='#' class='btn btn-large resource-btn' title="create resource"><i class='icon-plus'></i></a>			
		</div>  		      		      				
	</h1>  		
</div>

<ul class="thumbnails">
	<?php foreach ($list['resource'] as $resource) { ?>
	<li class="span2">
		<a href="javascript:editor_resource('<?php echo $resource->path ?>', '<?php echo $resource->id?>')" class='thumbnail' style='text-align: center' rel="tooltip" title="<?php echo $resource->title ?>" data-type="resource">
			<img src="/lib/image/<?php echo $resource->ext ?>.png" width="68"/>
			
			<div class="caption" data-obj='<?php echo json_encode($resource) ?>'>
				<?php echo $resource->title ?>
			</div>    			
		</a>
	</li>
	<?php } ?>
	
</ul>
</div>

<div class='page-header'>
	<h1>
		Document
		<div class='pull-right menu'>
			<a href='#' class='btn btn-large document-btn'><i class='icon-plus'></i></a>
		</div>  		      		      				
	</h1>  		
</div>

<ul class="thumbnails">
	<?php foreach ($list['directory'] as $directory) { ?>
	<li class="span2" style="text-align: center">
		<a href="#<?php echo $document_root ?><?php echo $directory->id ?>" class='thumbnail'  style='text-align: center' rel="tooltip" title="<?php echo $directory->title ?>" data-type="directory">
			<img src="/lib/image/_page.png" width="96"/>
			<div class="caption" data-obj='<?php echo json_encode($directory) ?>'>
				<?php echo $directory->title ?>
			</div>					
		</a>
	</li>
	<?php } ?>
	
</ul>



<script>
	function get_result() {
		var count = 0;
		var get_result_time = setInterval(function(){
			if (count == 2) {
				clearInterval(get_result_time);
				$("#resource_upload_popup").bPopup().close();
				
				alert("오류가 났나봐요. 업로드가 안되네요.ㅠㅠ");				
			}
			
			var obj = $("iframe[name=upload_frame]").contents().find("body").text();
			
			if (obj) {
				obj = JSON.parse(obj);
				
				var result = obj.result;
				
				alert("업로드가 되었습니다.축하축하")
				
				$("#resource_upload_popup").bPopup().close();
				
				clearInterval(get_result_time);
				
				var template = '<li class="span3"><a href="#" class="thumbnail" style="text-align: center"><img width="128" height="128"/></a><div class="caption" style="padding-top:2px;"></div></li>';
				
				var item = $(template);
				
				item.prependTo(".resource-view ul");
				
				var href = $("#document_root").val() + result.id + ".resource";
				item.find('a').attr("href", href );
				
        		item.find('.caption').html(resource.title).data('obj', JSON.stringify(result));
        				
        		item.find('img').attr('src', "/lib/image/" + resource.ext + ".png");				
				
			}
			
			count++;	
		 }, 1000);
	}
	
</script>


			
			<script>
        
        	$(function(){
        		$(".layout-btn").click(function(e){
        			
        			open_popup("#layout_popup", function($dom){
        				
							$dom.find('#layout_title').off().on('keydown', function(e) {
								if (e.keyCode == 13) {
									$dom.find(".save").click();	
								}
								
							});
														
							setTimeout(function(){ $dom.find("#layout_title").focus() }, 500);        	        				
        				
							$dom.find(".save").click(function(e){
								var obj = {
									cmd : "create layout",
									path : $("#path").val(),
									title : $dom.find("#layout_title").val()
								}
									
								$.post("/proc.php", obj, function(response){
									if (response.result == "ok") {
										location.reload();
									}
								})
							})
        			})

        		})
        		
        		$(".resource-btn").click(function(e){
        			
        			open_popup("#resource_popup", function($dom){
							$dom.find("a.thumbnail").click(function(e){
									var ext = $(this).attr('href').split("#")[1];
									
									$dom.bPopup().close();
									
									open_resource_popup($("#path").val(), ext);
									
							})
        			})        			
        		})
        		
        		$(".resource-upload-btn").click(function(e){
        			
        			
        			open_popup(
        				"#resource_upload_popup", 
        				function($dom){
							$dom.css('opacity', 1);
        				}
        			)        			
        			
        		})

        		$(".document-btn").click(function(e){
        			
        			open_popup(
        				"#document_popup", 
        				function($dom){
        					
							$dom.find('#document_title').on('keydown', function(e) {
								if (e.keyCode == 13) {
									$dom.find(".save").click();	
								}
								
							});
														
							setTimeout(function(){ $dom.find("#document_title").focus() }, 500);        					
        					
							$dom.find(".save").click(function(e){
								var obj = {
									cmd : "create document",
									path : $("#path").val(),
									title : $dom.find("#document_title").val()
								}
									
								$.post("/proc.php", obj, function(response){
									if (response.result == "ok") {
										location.reload();
									}
								})
							})
							

        				}
        			)
        		})
        		

        		
        		
        		var dropbox = $(".resource-view");
        		
        		dropbox.filedrop({
        			paramname: 'data',
        			maxfiles: 5,
        			maxfilesize: 2,
        			url: "/proc.php",
        			data: {
        				cmd : 'create resource',
        				path : $("#path").val()
        			},
        			uploadFinished: function(i, file, response) {
            				var href = $("#document_root").val() + response.result.id + "." + response.resource.ext + ".resource";
    						$.data(file).find('a').attr("href", href );
    						
    						$.data(file).find(".caption").data('obj', JSON.stringify(response.result));
						
						$(".resource-view").css('background', 'none');
        			},
        			
        			dragOver: function(e) {
        				$(".resource-view").css('background', '#eee');
        			},
        			
        			dragLeave: function(e) {
        				$(".resource-view").css('background', 'none');
        			},
        			
					drop : function(e) {
						
						if (e.dataTransfer.effectAllowed == 'move' && e.dataTransfer.getData("application/json")) {
			      	
			      			var obj = JSON.parse(e.dataTransfer.getData("application/json"));
			      			$.post("/proc.php", { cmd: 'copy resource', resource : obj, path : $("#path").val() }, function(response) {
			      				console.log(response);
			      			})

			      			return false; 
			      		}			
						
					},        			
        			
					error: function(err, e) {
						
						if (e.dataTransfer.effectAllowed == 'move' && e.dataTransfer.getData("application/json")) {
							return; 	
						}
        				switch(err) {
        					case "BrowserNotSupported":
        						alert("브라우저에서 지원안해요. ");
        						break;
        					case "TooManyFiles":
        						alert("너무 많은 파일을 올리셨군요. 한번에 5개까지만 올려주세요.");
        						break;
        					case "FileToLarge":
        						alert("파일이 너무 커요. 흐규흐규");
        						break;
        					default:
        						break;
        				}	
        			},
        			
        			beforeEach: function(file) {

						
        			},
        			
        			uploadStarted: function(i, file, len) { 
        				createImage(file);	
        			},
        			
        			progressUpdated: function(i, file, progress) {
        				var $dom = $.data(file) 
        				$dom.find('.progress .bar').width(progress + "%");
        				$dom.find('.progress .title').html(file.name);
        				
        				$dom.find('img').attr('src', "/lib/image/" + file.name.split(".").pop() + ".png");
        				
        				if (progress == 100) {
        					setTimeout(function() { $dom.find(".caption").text($dom.find(".progress").text()); $dom.find(".progress").remove(); }, 500);
        				}
	
        			}
        		})
        		
				var template = '<li class="span3"><a href="#" class="thumbnail" style="text-align: center"><img width="128" height="128"/></a><div class="caption" ><div class="progress"><div style="position: absolute;z-index: 99999;color: white;text-align: center;width: 220px;" class="title"></div><div class="bar" style="width:0%"></div></div></div></li>'; 
				
				function createImage(file){
			
					var preview = $(template),
						image = $('img', preview);
			
					var reader = new FileReader();
			
					reader.onload = function(e){
			
						// e.target.result holds the DataURL which
						// can be used as a source of the image:
			
						image.attr('src',e.target.result);
					};
			
					// Reading the file as a DataURL. When finished,
					// this will trigger the onload function above:
					reader.readAsDataURL(file);

					preview.prependTo(dropbox.find("ul.thumbnails"));
			
					// Associating a preview container
					// with the file, using jQuery's $.data():
			
					$.data(file,preview);
				}        	        		
        		
        		
        		$('ul.thumbnails').on("click", ".thumbnail .caption", function(e){
        			
        			e.preventDefault();
        			
        			var dataObj = $(e.currentTarget).data('obj');
        			
        			var type = $(this).data('type');
        			var self = $(e.currentTarget);
        			
        			
        			open_popup("#change_popup", function($dom){
							$dom.find('#change_title').val(dataObj.title).off().on('keydown', function(e) {
								if (e.keyCode == 13) {
									$dom.find(".save").click();	
								}
								
							});
							
							setTimeout(function(){ $dom.find('#change_title').focus() }, 500); 
							
							$dom.find(".save").click(function(e){
								var obj = {
			        				cmd : 'change title',
			        				path : $("#path").val(),
			        				id : dataObj.id,
			        				ext: dataObj.ext,
			        				type: dataObj.type,
									title : $dom.find("#change_title").val()
								}
									
								$.post("/proc.php", obj, function(response){
									if (response.result == "ok") {
										
										if (obj.type == "resource") {
											self.text(obj.title + "." + obj.ext);
										} else {
											self.text(obj.title);	
										}
										
										self.data("obj", obj);
										
										$dom.bPopup().close();
									}
								})
							})
        			})
        			
        			return false;
        			
        		}).on('dblclick', '.thumbnail', function(e) {
        			e.preventDefault();
        			
        			location.href = $(e.currentTarget).attr('href').split("#")[1];
        			
        			return false; 
        		})
        		        		
        		        		
        		$("a[rel=tooltip]").tooltip();
        	})
        	
        </script>
