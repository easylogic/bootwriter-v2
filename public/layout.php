<?php include_once "category.php" ?>

<div class='page-header'>
	<h1><?php echo $doc->title; ?></h1>
</div>
<div class="row layout-main"></div>

<div class="hide" id="select_resource_popup">
	<div class="modal">
	  <div class="modal-header">
	  	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	    <h3>Select Resource</h3>
	  </div>
	  <div class="modal-body" style='padding-left:50px;'>
	  	
		<ul class="thumbnails resource_tree">
			
		</ul>
	  </div>
	</div>
</div>


<script type="text/javascript">
var layout_id = "<?php echo $doc->id ?>";
var layout_list = JSON.parse('<?php echo json_encode($doc->list) ?>');
function save_layout() { 
	
	var $temp = [];
	
	$(".layout-main").children().each(function(index, elem){
		var $dom = $(elem);
		$temp.push({
			span : $dom.data('span'),
			offset: $dom.data('offset'),
			id : $dom.data('info').id,
			path : $dom.data('info').path
		})
	})
	
	$.post('/proc.php',
	{
		cmd: 'update layout',
		path : $("#path").val(),
		id : layout_id,
		list : $temp
	}, function(ret){
		if (ret.result == "ok") {
			alert("성공적으로 저장했어요.");
			location.reload();
		}
	})
}

function loadResource(path){
	$.get(
		"/proc.php",
		{
			cmd: "list document",
			path: path
		},function(response){
			var list = response.result;
			var tree = $(".resource_tree");
			tree.data('path', path);
			tree.empty();
	
			if (path != ".") {
				tree.append(
					$("<li class='span2' />").append(
						$("<a  href='#' class='thumbnail'  rel='tooltip'  style='text-align: center;background:#eee;'/>").append(
							$("<i class='icon-chevron-left pull-left' />").after("...")
						).css('cursor', 'pointer').attr('title', '..').click(function(e){
							var arr = path.split("/");
							
							arr.pop();
							
							loadResource(arr.join("/"))
						}).append(
							$("<div class='media-body' />").append(
								$("<h4 class='media-heading' />").html("Parent..")
							)
						)
					)
				)				
			}		

			tree.append($("<li class='span6 active' />").html("Documents"))
			
			for(var i = 0; i < list.directory.length; i++){
				tree.append(
					$("<li class='span2' />").append(
						$("<a  href='#' class='thumbnail'  rel='tooltip'   style='text-align: center;background:#eee;'/>").attr('title', list.directory[i].title).append(
							$("<img src='/lib/image/_page.png' />")
						).append(
							$("<div class='caption' />").html(list.directory[i].title)
						).css('cursor', 'pointer').data("info", list.directory[i]).click(function(e){
							loadResource(path + "/" + $(this).data('info').id)
						})
					)
				)
			}
			
			tree.append($("<li class='span6 active' />").html("Layouts"))			
			
			for(var i = 0; i < list.layout.length; i++){
				if (list.layout[i].id == layout_id) continue;
				if (!list.layout[i].id) continue;
				
				var $a = $("<a  href='#'  class='thumbnail'   rel='tooltip'  style='text-align: center;background:#eee;'/>").attr('title', list.layout[i].title);
				
				$a.append($("<img src='/lib/image/_page.png' />")).append(list.layout[i].title)
								
				tree.append(
					$("<li class='span2' />").append($a)
				)
			}
			
			tree.append($("<li class='span6 active' />").html("Resources"))						
			
			for(var i = 0; i < list.resource.length; i++){
				tree.append(
					$("<li class='span2' />").append(
						$("<a href='#' class='thumbnail'  rel='tooltip'  style='text-align: center;background:#eee;'/>").attr('title', list.resource[i].title).append(
							$("<img src='/lib/image/" + list.resource[i].ext + ".png'/>") 
						).append(
							$("<div class='caption' />").append(
								list.resource[i].title
							)
						).data('info', list.resource[i]).click(function(e){
							var info = $(this).data('info');
							
							new_resource({ span : 12, offset : 0, id : info.id, path : info.path }, function(){
								close_popup('#select_resource_popup')		
							});
						})
					)
				)
			}
			
			$("a[rel=tooltip]", tree).tooltip();
		}
	)
}

function loadData($dom, callback) { 
	
	var params = { cmd : 'info resource', path : $dom.data('info').path, id : $dom.data('info').id};

	$.get('/proc.php', params, function(ret) {
		var resource = ret.result;
		
		var data = convertTo(resource);

		$dom.attr('data-id', resource.id).data('info', resource).data('id', resource.id);
		
		if ($dom.find(".toy").length) {
			$dom.find(".toy").html(data);	
		} else {
			$dom.prepend($("<div class='toy' />").html(data));
		}
		
		if (callback) callback();
	})	
}

function convertTo(resource) { 
		var data = resource.data;
		
		if (resource.type == 'markdown') {
			data = markdown.toHTML(data)
		} else if (resource.type == 'html') {
			data = data; 
		} else if (resource.type == 'text') {
			data = "<pre>" + data + "</pre>";
		} else if (resource.type == 'code') {
			data = prettyPrintOne(htmlEntities(resource.data), resource.ext, 1)	
			data = "<pre class='prettyprint linenums'>" + data + "</pre>";
		} else { 
			if (resource.mime && resource.mime.indexOf("image") > -1) {
				data = "<img src='" + resource.path + "/" + resource.id + ".resource' />"
			}
		}
		
		return data; 	
}

function new_resource(info, callback) {
	
	if ($(".drop-blank").length) {
		$(".drop-blank").remove();
	}

	var $dom = $("<div />").data('info', info).data('resource', info.id).data({
		span : parseInt(info.span || App.MaxSpan),
		offset : parseInt(info.offset || App.MinOffset)
	})

	$(".layout-main").append($dom);
	
	loadData($dom, function(){
		setResourceEvent($dom);
		changeSpan($dom, $dom.data('span'));
		changeOffset($dom, $dom.data('offset'));
		
		if (callback) callback();
		
		select($dom);		
	});
}



function setResourceEvent ($dom) {
    var resizableReset = function(e, ui) { 
        
        var handle = $(this).css("cursor").split("-")[0];
        
        if (handle == "w"){
            
            var offset = Math.floor(( Math.abs(ui.position.left) + App.GridGutter)/(App.GridGutter + App.GridWidth));
            	                    
            if (ui.position.left > 0) { 
                offset = App.offsetOrigin + span  ;
            } else { 
                offset = App.offsetOrigin - span ;    
            }

            if (offset < App.MinOffset) offset = App.MinOffset;

           	var span = App.spanOrigin - offset;                    
            ui.element.css({ top: "", left: "", width: "", height: "", position: "" })

            if (e.ctrlKey) {
                changeOffset(ui.element, offset);                    	
            } else { 
            	changeOffset(ui.element, offset);
            	changeSpan(ui.element, span);
            }
   
            App.offset = offset;
            App.span = span;
            App.resizableMode = "w";                        
        } else { 
            
            var span = Math.floor((ui.size.width + App.GridGutter)/(App.GridGutter + App.GridWidth));
            
            if (span > App.MaxSpan) span = App.MaxSpan;
            
            ui.element.css({ top: "", left: "", width: "", height: "", position: "" })
            
            changeSpan(ui.element, span);
            
            App.resizableMode = "e";
        }
    };
    
    
    $dom.resizable({
        minHeight: App.GridWidth,
        minWidth: App.GridWidth,
        handles: "e,w",
        distance: App.GridWidth + App.GridGutter,
        grid : App.GridWidth + App.GridGutter,
        cursorAt: {
        	left: 500,
        	top : 500
        },
        start: function(e, ui) {
        	
            var handle = $(this).css("cursor").split("-")[0];
        
            if (handle == "w"){
                App.spanOrigin = ui.element.data('offset') + ui.element.data('span'); 
                App.offsetOrigin = ui.element.data('offset'); 
            }
        },
        
        resize: function(e, ui) { 
            resizableReset.call(this, e, ui);
        },
        stop: function(e, ui) {
            if (App.resizableMode == 'w') {
                if (e.ctrlKey){
                    ui.element.data('offset', App.offset);    
                    ui.element.data('span', App.span);    
                } else {
                    ui.element.data('offset', App.offset);    
                }
            }

            App.resizableMode = "";
            App.offset = "";                        
            App.span = "";                        
            
            ui.element.css({ top: "", left: "", width: "", height: "", position: "" })
        },                    
    });          
    
    $dom.on('dblclick', function(e){
    	var $temp = $(this);
    	
    	open_resource_popup($temp.data('info').path, $temp.data('info').type, $temp.data('info'), function(resource) {
    		var list = $(".layout-main").find("[data-id=" + resource.id + "]");
    		
    		if (list.length > 0) {
	    		list.each(function(i, elem){
	    			loadData($(elem));
	    			setResourceEvent ($(elem))
	    			close_resource_popup(resource.type);
	    		})	
    		} else {
    			new_resource(resource, function(){
    				close_resource_popup(resource.type);
    			});
    		}
    		
    	});
    })   
    
}

function keyMap (e) {
    if (e.shiftKey && e.keyCode == 187) { // plus(+)
        $('#menubox a').click();
    }      
 }

function select($dom) {
	$(".select").removeClass('select')
	$dom.addClass("select");
	
	scroll($dom);
}

$(function(){
	
	$(".layout-main").sortable({
		cursorAt: {
			left: 5,
			top: 25
		},
		cursor: 'move',
		placeholder : "sortable-placeholder",
		helper: function() { 
        	return $("<div />").css({
        		width: '0px',
        		height: '0px',
        	})	
    	},
    	start: function(event, ui) { 
    		ui.placeholder.height(ui.item.height()).css({
    			opacity : 0.5
    		}).html(ui.item.clone().html());
    		
    		changeSpan(ui.placeholder, ui.item.data('span'));
    		changeOffset(ui.placeholder, ui.item.data('offset'));
    	}

	}).on('click', '.toy', function(e){
		var $dom = $(e.currentTarget);

		 select($dom)
	});
		
		
	if (layout_list.length == 0) {
		$(".layout-main").append($("<div style='width:100%;height:100px' class='drop-blank' />"))
	}
			
	
	for(var i = 0; i < layout_list.length; i++) {
		new_resource(layout_list[i]);	
	}
	
	
	$("#menubox").append(
		$("<a href='#' />").html("<i class='icon-plus' />").append(" Add").click(function(e){
    			open_popup("#resource_popup", function($dom){
						$dom.find("a.thumbnail").click(function(e){
								var ext = $(this).attr('href').split("#")[1];
								var path = $("#path").val();
								
								$dom.bPopup().close();

								open_resource_popup(path, ext, null, function(info){
									new_resource({ span : 12, offset : 0, id : info.id, path : path }, function(){
										close_resource_popup(info.type)		
									});
								});
								
						})
    			})   
		})
	)
	
	$("#menubox3").append(
		$("<a href='#' />").html("<i class='icon-folder-open' />").append(" Choose").click(function(e){
			open_popup('#select_resource_popup', function($dom){
				loadResource($("#path").val());
			}) 
		})
	)
	
	$("#menubox2").append(
		$("<a href='#' />").html("<i class='icon-check' />").append(" Save").click(function(e){
			save_layout();
		})
	)	
	
	$(document).keydown(function(e){
		keyMap(e);
	})
	
	set_upload_component(".layout-main", {
		cmd : 'create resource',
		path : $("#path").val()
	}, function(i, file, response){
		new_resource({ span : 12, offset : 0, id : response.result.id, path : $("#path").val() });
	}, function(e){
		new_resource(JSON.parse(e.dataTransfer.getData("application/json")), function(){
      		$(".layout-main").css('background', 'none');
      	}); 
	})
	

})

</script>