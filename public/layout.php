<?php include_once "category.php" ?>

<div class='page-header'>
	<h1><?php echo $doc->title; ?></h1>
</div>
<div class="row layout-main"></div>
<div class="row toy-maker">
	
</div>

<div id="disqus_thread"></div>
<script type="text/javascript">
    /* * * CONFIGURATION VARIABLES: EDIT BEFORE PASTING INTO YOUR WEBPAGE * * */
    var disqus_shortname = 'bootwritev2'; // required: replace example with your forum shortname

    /* * * DON'T EDIT BELOW THIS LINE * * */
    (function() {
        var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
        dsq.src = 'http://' + disqus_shortname + '.disqus.com/embed.js';
        (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
    })();
</script>
<noscript>Please enable JavaScript to view the <a href="http://disqus.com/?ref_noscript">comments powered by Disqus.</a></noscript>
<a href="http://disqus.com" class="dsq-brlink">comments powered by <span class="logo-disqus">Disqus</span></a>


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
var layout_storage_id  = "<?php echo $doc->storageId ?>";
var layout_list = JSON.parse('<?php echo json_encode($doc->list) ?>');
function save_layout() { 
	
	var $temp = [];
	
	$(".layout-main").children().each(function(index, elem){
		var $dom = $(elem);
		$temp.push({
			span : $dom.data('span'),
			offset: $dom.data('offset'),
			id : $dom.data('info').id,
			path : $dom.data('info').path,
			storageId : $dom.data('info').storageId,
			style : $dom.data('style')
		})
	})
	
	console.log($temp);
	
	$.post('/proc.php',
	{
		cmd: 'update layout',
		path : $("#path").val(),
		storageId : layout_storage_id ,
		id : layout_id,
		list : $temp
	}, function(ret){
		if (ret.result == "ok") {
			alert("성공적으로 저장했어요.");
			//location.reload();
		}
	})
} 

function loadData($dom, callback) { 
	
	var info = $dom.data('info');
	var params = { cmd : 'info resource', path : info.path, id : info.id, storageId : info.storageId};

	$.get('/proc.php', params, function(ret) {
		var resource = ret.result;
		
		var data = convertTo(resource);

		$dom.attr('data-id', resource.id).data('info', resource).data('id', resource.id);
		
		if ($dom.find(".toy").length) {
			$dom.find(".toy").html(data);	
		} else {
			$dom.prepend($("<div class='toy box' />").html(data));
		}
		
		if (callback) callback();
	})	
}

function get_resource_path(obj) {
	return "/" + obj.storageId + "@" + obj.path + "/" + obj.id + ".resource";
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
				data = "<img src='" + get_resource_path(resource) + "' />"
			}
		}
		
		return data; 	
}

function new_resource(info, callback, $prev, isSelect) {
	
	isSelect = isSelect || false;
	
	if ($(".drop-blank").length) {
		$(".drop-blank").remove();
	}

	var $dom = $("<div />").data('info', info).data('resource', info.id).data({
		span : parseInt(info.span || App.MaxSpan),
		offset : parseInt(info.offset || App.MinOffset),
		style : info.style || {} 
	})

	if ($prev) {
		$prev.after($dom);
	} else {
		$(".layout-main").append($dom);	
	}
	
	
	loadData($dom, function(){
		setResourceEvent($dom);
		changeSpan($dom, $dom.data('span'));
		changeOffset($dom, $dom.data('offset'));
		
		apply_style($dom.find(".toy"), $dom.data('style'));
		
		if (callback) callback();
		
		if (isSelect) select($dom);		
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
    			}, $dom, true);
    		}
    		
    	});
    })   
    
}

function keyMap (e) {
    if (e.shiftKey && e.keyCode == 187) { // plus(+)
        $('#menubox a').click();
    }      
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
		 
		 $('#config-tab a[href="#config-background"]').tab('show');

         var css = $dom.parent().data('style') || {};
         

        $(".config .config-form .control-group label").each(function(i, elem){
            var $input = $(elem).find("input,select");
            
            var id = $input.attr('id').replace("style-", "");
            
            $input.val(css[id] || '');
        })
		 
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
									new_resource({ span : 12, offset : 0, id : info.id, path : path, storageId : info.storageId }, function(){
										close_resource_popup(info.type)		
									}, null, true);
								});
								
						})
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
		new_resource({ span : 12, offset : 0, id : response.result.id, path : $("#path").val(), storageId : response.result.storageId });
	}, function(e){
		new_resource(JSON.parse(e.dataTransfer.getData("application/json")), function(){
      		$(".layout-main").css('background', 'none');
      	}, null, true); 
	})
	
    $(".config .splitter").css('cursor', 'pointer').click(function(e){
        if (parseInt($(".config").css('right')) == 0) {  // close 
            $(".config").animate({
                'right' : '-244px'
            }, 200, function() {
                $(".container").each(function(index, elem) {
                    $(elem).css({
                        'margin-right' : 'auto'
                    })
                })
            });

            $(".config .splitter i").removeClass("open icon-chevron-right").addClass("icon-chevron-left").addClass("close");
        } else {        // open 

            if (!$(".layout-main .select").length) return;

            
            $(".container").each(function(index, elem) {
                if (parseInt($(elem).css('margin-right')) < 270) {
                    $(elem).animate({
                        'margin-right' : '270px'
                    }, 100)
                }
            })

            $(".config").animate({
                'right' : '0px'
            }, 200);

            $(".config .splitter i").removeClass("close icon-chevron-left").addClass("icon-chevron-right").addClass("open");
            
            if ($(".manager .splitter .open").length && $(window).width() < 1400) {
                $(".manager .splitter").click();
            }

        }
    })
    
    function changeStyle(e) { 
        var css = $(this).attr('id').replace("style-", "");
        console.log(css);
        var $select = $(".layout-main .select");
        var $parent = $select.parent();
        // set data 
        var style = $parent.data('style') || {};
        
        style[css] = $(this).val();    
        
        $parent.data('style', style);
        
        // apply css
        apply_style($select, style);       
    }
    
    
    
    $(".config-form .main-config label input.editor").on('keyup', changeStyle).on('change', changeStyle);
    $(".config-form .main-config label select.editor").on('change', changeStyle);
    var $label = $(".config-form .main-config label");
    $label.find("a.btn").click(function(e){
    	var $a = $(e.currentTarget);
    	var $dom = $(e.currentTarget).parent().parent();
    	var id = $dom.attr('for').replace("style-", "")
    	
    	if ($a.find("i").hasClass("icon-chevron-right") ) { 	// open 
    		$a.find("i").removeClass("icon-chevron-right").addClass("icon-chevron-down")
    		$('#config-' + id).show();
    		
    		var obj = expand_style(id, $a.parent().find("input").val());
    		
    		console.log(obj);
    		
    		$("#config-" + id).find(".editor").each(function(idx, elem) {
    			var $e = $(elem);
    			var key = $e.attr('id').split("_");
    			$e.val(obj[key[2]]);
    		})
    		
    	} else { 												// close 
    		$a.find("i").removeClass("icon-chevron-down").addClass("icon-chevron-right")
    		$('#config-' + id).hide();
        }
    	
    })
    
    $('#config-tab').tab();
    
    function collectStyle(e) {
    	var $config = $(e.currentTarget).parent().parent().parent().parent();
    	
    	var id = $config.attr('id').replace("config-", "");
    	
    	var obj = {};
    	$config.find("input,select").each(function(i, elem){
    		console.log(elem);
    		var subid = $(elem).attr('id').split("_");
    		obj[subid[2]] = $(elem).val();
    	})
    	
    	var value = collect_style(id, obj)
    	
    	console.log('value', value);
    	
    	$("#style-" + id).val(value).change(); 
    }
    
    $(".config-form .sub-config label input.editor").on('keyup', collectStyle).on('change', collectStyle);
    $(".config-form .sub-config label select.editor").on('change', collectStyle);
    
    $(".config-form .colors").miniColors({
    	letterCase: 'lowercase',
    	change: function(hex, rgb) {
			$(this).change();
    	}
    })

})

</script>