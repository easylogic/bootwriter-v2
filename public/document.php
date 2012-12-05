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
		<a href="#<?php echo $document_root ?><?php echo $resource->id.".resource" ?>" class='thumbnail' style='text-align: center' rel="tooltip" title="<?php echo $resource->title ?>" data-type="resource">

			<?php if (strstr($resource->mime, 'image')) { ?>
			<img src="<?php echo $document_root ?><?php echo $resource->id.".resource" ?>"/>				 
			<?php } else { ?>
			<img src="/lib/image/<?php echo $resource->ext ?>.png" width="68"/>
			<?php } ?>
			
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
				
				var template = '<li class="span2"><a href="#" class="thumbnail" style="text-align: center"><img width="128" height="128"/></a><div class="caption" style="padding-top:2px;"></div></li>';
				
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
			
			function add_resource_view(obj) {
				
				var $a = $("<a />").attr({
						'data-type' : 'resource',
						title : obj.title,
						rel : 'tooltip',
						href : obj.path + "/" + obj.id + ".resource"
					}).addClass('thumbnail').css({
						'text-align' : 'center'
					})
					
					if (obj.mime.indexOf("image") > -1) {
						$a.append($("<img />").attr('src' , obj.path + "/" + obj.id + ".resource"));
					} else {
						$a.append($("<img />").attr('src' , "/lib/image/"+ obj.ext + ".png"));
					}
				
					$a.append($("<div class='caption' />").data('obj', obj).append(obj.title))
				
				$(".resource-view .thumbnails").prepend($("<li  class='span2' />").append( $a));
			}
        
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
        		

				set_upload_component(".resource-view", {
    				cmd : 'create resource',
    				path : $("#path").val()
				}, function(i, file, response){
					add_resource_view(response.result);
				}, function(e){
					var obj = JSON.parse(e.dataTransfer.getData("application/json"));
	      			$.post("/proc.php", { cmd: 'copy resource', resource : obj, path : $("#path").val() }, function(response) {
	      				add_resource_view(response.result);
	      			})
				})
				
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
        			var $a = $(e.currentTarget);
        			var href = $a.attr('href');
        			
        			var obj = $a.find(".caption").data('obj');
        			
        			if ($a.attr('data-type') == 'resource') {
        				editor_resource(obj.path, obj.id);
        			} else { 
        				location.href = href.split("#")[1];
        			}
        			
        			return false; 
        		})
        		        		
        		        		
        		$("a[rel=tooltip]").tooltip();
        	})
        	
        </script>
