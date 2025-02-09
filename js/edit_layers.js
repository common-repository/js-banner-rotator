var UniteLayersBanner = new function() {
	
	var initTop = 100;
	var initLeft = 100;
	var initSpeed = 300;
	
	var initTopVideo = 20;
	var initLeftVideo = 20;
	var g_startTime = 500;
	var g_stepTime = 300;
	var g_slideTime;
	
	var initText = "Caption Text";
	
	//Init system vars
	var t = this;
	var containerID = "#divLayers";
	var container;	
	var arrLayers = {};
	var id_counter = 0;
	var initLayers = null;
	var selectedLayerSerial = -1;
	var urlCssCaptions = null;
	var initArrCaptionClasses = [];
	var sortMode = "time";				//Can be "depth" or "time"
	var g_codemirrorCss = null;

	
	//Set init layers object (from db)
	t.setInitLayersJson = function(jsonLayers) {
		initLayers = jQuery.parseJSON(jsonLayers);
	}
	
	//Set init captions classes array (from the captions.css)
	t.setInitCaptionClasses = function(jsonClasses){
		initArrCaptionClasses = jQuery.parseJSON(jsonClasses);
	}
	
	//Set captions url for refreshing when needed
	t.setCssCaptionsUrl = function(url){
		urlCssCaptions = url;
	}
	
	//Close css dialog
	t.closeCssDialog = function() {
		jQuery("#dialog_edit_css").dialog("close");
	}
		
	//Insert button by class and text
	t.insertButton = function(buttonClass,buttonText){
		if(selectedLayerSerial == -1)
			return(false);
		
		var html = "<a href='javascript:alert(\"click\");' class='fb-button "+buttonClass+" small'>"+buttonText+"</a>";
		
		jQuery("#layer_caption").val("");
		jQuery("#layer_text").val(html);
		updateLayerFromFields();
		
		jQuery("#dialog_insert_button").dialog("close");
	}	
	
	/*************************
	    - Init functions -
	*************************/
	
	//Init the layout
	t.init = function(slideTime) {		
		if(jQuery().draggable == undefined || jQuery().autocomplete == undefined){
			jQuery("#jqueryui_error_message").show();			
		}			
		
		g_slideTime = Number(slideTime);
		
		container = jQuery(containerID);
		
		//Add all layers from init
		if(initLayers){
			var len = initLayers.length;
			if(len){
				for(var i=0;i<len;i++)
					addLayer(initLayers[i],true);
			}else{
				for(key in initLayers)
					addLayer(initLayers[key],true);
			}
		}
				
		//Disable the properties box
		disableFormFields();
		
		//Init elements
		initMainEvents();
		initSortbox();
		initButtons();
		initEditCSSDialog();
		initHtmlFields();
		initAlignTable();
	}	
	
	//Init the align table
	var initAlignTable = function() {
		jQuery("#align_table a").click(function() {			
			var obj = jQuery(this);
			if(jQuery("#align_table").hasClass("table_disabled"))
				return(false);
			if(obj.hasClass("selected"))
				return(false);
			
			var alignHor = obj.data("hor");
			var alignVert = obj.data("vert");
			jQuery("#align_table a").removeClass("selected");
			obj.addClass("selected");
			
			jQuery("#layer_align_hor").val(alignHor);
			jQuery("#layer_align_vert").val(alignVert);
			
			var objLayerXText = jQuery("#layer_left_text");
			var objLayerYText = jQuery("#layer_top_text");
			
			switch(alignHor) {
				case "left":
					objLayerXText.html(objLayerXText.data("textnormal")).css("width","auto");
					jQuery("#layer_left").val("10");
				break;
				case "right":
					objLayerXText.html(objLayerXText.data("textoffset")).css("width","42px");
					jQuery("#layer_left").val("10");
				break;
				case "center":
					objLayerXText.html(objLayerXText.data("textoffset")).css("width","42px");
					jQuery("#layer_left").val("0");
				break;
			}
			
			switch(alignVert) {
				case "top":
					objLayerYText.html(objLayerYText.data("textnormal")).css("width","auto");
					jQuery("#layer_top").val("10");
				break;
				case "bottom":
					objLayerYText.html(objLayerYText.data("textoffset")).css("width","42px");
					jQuery("#layer_top").val("10");
				break;					
				case "middle":
					objLayerYText.html(objLayerYText.data("textoffset")).css("width","42px");
					jQuery("#layer_top").val("0");
				break;
			}
			
			updateLayerFromFields();			
		});		
	}
	
	
	//Init general events
	var initMainEvents = function() {		
		//Unselect layers on container click
		container.click(unselectLayers);
	}	
	
	//Init events (update) for html properties change.
	var initHtmlFields = function() {		
		//Show / hide slide link offset
		jQuery("#layer_slide_link").change(function() {
			showHideOffsetRow();
		});
		
		//Set layers autocompolete
		jQuery( "#layer_caption" ).autocomplete({
			source: initArrCaptionClasses,
			minLength:0,
			close:updateLayerFromFields
		});	
		
		//Open the list on right button
		jQuery( "#layer_captions_down" ).click(function(event){
			event.stopPropagation();
			
			//if opened - close autocomplete
			if(jQuery('#layer_caption').data("is_open") == true)
				jQuery( "#layer_caption" ).autocomplete("close");
			else   //else open autocomplete
			if(jQuery(this).hasClass("ui-state-active"))
				jQuery( "#layer_caption" ).autocomplete( "search", "" );
		});
		
		//Handle autocomplete close
		jQuery('#layer_caption').bind('autocompleteopen', function() {
			jQuery(this).data('is_open',true);
		});		

		jQuery('#layer_caption').bind('autocompleteclose', function() {
			jQuery(this).data('is_open',false);
		});	
		
		jQuery("body").click(function() {
			jQuery( "#layer_caption" ).autocomplete("close");
		});		
		
		//Set events
		jQuery("#form_layers select").change(updateLayerFromFields);
		jQuery("#layer_text").keyup(updateLayerFromFields);
		var pressEnterFields = "#form_layers input, #form_layers textarea";
		jQuery(pressEnterFields).blur(updateLayerFromFields);
		jQuery(pressEnterFields).keypress(function(event){
			if(event.keyCode == 13)
				updateLayerFromFields();
		});
		
		//End time validation
		jQuery("#layer_endtime").blur(validateCurrentLayerTimes).keypress(function(event){
			if(event.keyCode == 13)
				validateCurrentLayerTimes();
		});
	}			
	
	//Init buttons actions
	var initButtons = function() {		
		//Set event buttons actions
		jQuery("#button_add_layer").click(function() {
			addLayerText();
		});
		
		jQuery("#button_add_layer_image").click(function() {
			UniteAdminBanner.openAddImageDialog("Select Layer Image",function(urlImage){
				addLayerImage(urlImage);
			});
		});
		
		//Add youtube actions
		jQuery("#button_add_layer_video").click(function() {
			UniteAdminBanner.openVideoDialog(function(videoData){
				addLayerVideo(videoData);
			});
		});
		
		//Edit video actions
		jQuery("#button_edit_video").click(function() {
			var objCurrentLayer = getCurrentLayer();						
			var objVideoData = objCurrentLayer.video_data;
			
			//Open video dialog
			UniteAdminBanner.openVideoDialog(function(videoData){
				//update video layer
				var objLayer = getVideoObjLayer(videoData);
				updateCurrentLayer(objLayer);
				updateHtmlLayersFromObject(selectedLayerSerial);
				updateLayerFormFields(selectedLayerSerial);
				redrawLayerHtml(selectedLayerSerial);
			},
			objVideoData);
			
		});
		
		//Change image source actions
		jQuery("#button_change_image_source").click(function() {
			UniteAdminBanner.openAddImageDialog("Select Layer Image",function(urlImage){
				var objData = {};
				objData.image_url = urlImage;
				updateCurrentLayer(objData);
				redrawLayerHtml(selectedLayerSerial);
			});
		});
		
		//Delete layer actions
		jQuery("#button_delete_layer").click(function() {
			if(jQuery(this).hasClass("button-disabled"))
				return(false);
			
			//Delete selected layer
			deleteCurrentLayer();
		});

		//Delete layer actions:
		jQuery("#button_duplicate_layer").click(function() {
			if(jQuery(this).hasClass("button-disabled"))
				return(false);
			
			//Delete selected layer
			duplicateCurrentLayer();
		});
		
		//Delete all layers actions:
		jQuery("#button_delete_all").click(function() {
			if(confirm("Do you really want to delete all the layers?") == false)
				return(true);
			
			if(jQuery(this).hasClass("button-disabled"))
				return(false);
			
			deleteAllLayers();
		});
		
		//Insert button link - open the dialog
		jQuery("#linkInsertButton").click(function() {			
			if(jQuery(this).hasClass("disabled"))
				return(false);
			
			var buttons = {"Cancel":function() {jQuery("#dialog_insert_button").dialog("close")}}
			jQuery("#dialog_insert_button").dialog({buttons:buttons,minWidth:500,modal:true});			
		});
	}	

	//Show / hide offset row accorging the slide link value
	var showHideOffsetRow = function() {
		var value = jQuery("#layer_slide_link").val();
		var offsetRow = jQuery("#layer_scrolloffset_row");
		
		if(value == "scroll_under")
			offsetRow.show();
		else
			offsetRow.hide();
	}	
	
	//Do various form validations
	var doCurrentLayerValidations = function() {
		if(selectedLayerSerial == -1)
			return(false);
		
		validateCurrentLayerTimes();
	}	
	
	//Validate times (start and end times) of the current layer
	var validateCurrentLayerTimes = function() {
		var currentLayer = getCurrentLayer();
		if(!currentLayer)
			return(false);
		
		var startTime = currentLayer.time;
		var endTime = jQuery("#layer_endtime").val();
		endTime = jQuery.trim(endTime);
		
		if(!endTime || endTime == ""){
			unmarkFieldError("#layer_endtime");
			return(false);
		}
		
		startTime = Number(startTime);
		endTime = Number(endTime);
		
		if(startTime >= endTime)
			markFieldError("#layer_endtime","Must be greater then start time ("+startTime+")");
		else
			unmarkFieldError("#layer_endtime");
	}
	
	//Mark some field as error field and give it a error title
	var markFieldError = function(field_selector,errorTitle){
		jQuery(field_selector).addClass("field_error");
		if(errorTitle)
			jQuery(field_selector).prop("title",errorTitle);
	}	
	
	//Unmark field error class and title
	var unmarkFieldError = function(field_selector){
		jQuery(field_selector).removeClass("field_error");
		jQuery(field_selector).prop("title","");
	}	
	
	//Get the first style from the styles list (from autocomplete)
	var getFirstStyle = function() {
		var arrClasses = jQuery( "#layer_caption" ).autocomplete("option","source");
		var firstStyle = "";
				
		if(arrClasses.length == 0)
			return("");
				
		var firstStyle = arrClasses[0];
		
		return(firstStyle);
	}	

	//Clear layer html fields, and disable buttons
	var disableFormFields = function() {		
		//Clear html form
		jQuery("#form_layers")[0].reset();
		jQuery("#form_layers input, #form_layers select, #form_layers textarea").attr("disabled", "disabled").addClass("setting-disabled");
		
		jQuery("#button_delete_layer").addClass("button-disabled");
		jQuery("#button_duplicate_layer").addClass("button-disabled");
		
		jQuery("#form_layers label, #form_layers .setting_text, #form_layers .setting_unit").addClass("text-disabled");
		
		jQuery("#layer_captions_down").removeClass("ui-state-active").addClass("ui-state-default");
		
		jQuery("#linkInsertButton").addClass("disabled");
		
		jQuery("#align_table").addClass("table_disabled");
	}
	
	//Enable buttons and form fields.
	var enableFormFields = function() {
		jQuery("#form_layers input, #form_layers select, #form_layers textarea").removeAttr("disabled").removeClass("setting-disabled");
		
		jQuery("#button_delete_layer").removeClass("button-disabled");
		jQuery("#button_duplicate_layer").removeClass("button-disabled");
		
		jQuery("#form_layers label, #form_layers .setting_text, #form_layers .setting_unit").removeClass("text-disabled");
		
		jQuery("#layer_captions_down").removeClass("ui-state-default").addClass("ui-state-active");
		
		jQuery("#linkInsertButton").removeClass("disabled");
		
		jQuery("#align_table").removeClass("table_disabled"); 
	}
	
	//Set code mirror editor
	t.setCodeMirrorEditor = function() {
		g_codemirrorCss = CodeMirror.fromTextArea(document.getElementById("textarea_edit"), {});
	}
	
	//Init dialog actions
	var initEditCSSDialog = function() {
		jQuery("#button_edit_css").click(function() {
			
			UniteAdminBanner.ajaxRequest("get_captions_css","",function(response) {				
				//Update textarea with css
				var cssData = response.data;
				
				if(g_codemirrorCss != null)
					g_codemirrorCss.setValue(cssData);
				else{
					jQuery("#textarea_edit").val(cssData);
					setTimeout('UniteLayersBanner.setCodeMirrorEditor()',500);
				}
								
				//Open captions edit dialog	
				var buttons = {	
						
				//Update button action						
				"Update":function() {
					
						UniteAdminBanner.setErrorMessageID("dialog_error_message");						
						var data;
						if(g_codemirrorCss != null)
							data = g_codemirrorCss.getValue();
						else
							data = jQuery("#textarea_edit").val();
						
						UniteAdminBanner.ajaxRequest("update_captions_css",data,function(response){
							jQuery("#dialog_success_message").show().html(response.message);
							setTimeout("UniteLayersBanner.closeCssDialog()",500);
							
							if(urlCssCaptions)
								UniteAdminBanner.loadCssFile(urlCssCaptions,"banner-rotator-caption-css");
							
							//update html select (got as "data" from response)
							updateCaptionsInput(response.arrCaptions);
						});
				},
				
				//Restore original button action				
				"Restore Original":function() {
					UniteAdminBanner.setErrorMessageID("dialog_error_message");
					UniteAdminBanner.ajaxRequest("restore_captions_css","",function(response){						
						jQuery("#dialog_success_message").show().html("css content restored, please press update");
						
						if(g_codemirrorCss != null)
							g_codemirrorCss.setValue(response.data);
						else
							jQuery("#textarea_edit").val(response.data);
						
						setTimeout("jQuery('#dialog_success_message').hide()",1000);
					});					
				},
						
				//Cancel button action
				"Cancel":function() {t.closeCssDialog()}
				};
				
				//Lock scrollbars
				jQuery('body').css({'overflow':'hidden'});
				
				//Hide dialog error message
				jQuery("#dialog_error_message").hide();
				jQuery("#dialog_success_message").hide();
				
				//Open the dialog
				jQuery("#dialog_edit_css").dialog({
					buttons:buttons,
					minWidth:800,
					modal:true,
					close: function(event, ui){
						//return scrollbars
						jQuery('body').css({'overflow':'auto'});
			        }
				});				
			});			
		});
	}	
	
	//Update z-index of the layers by order value
	var updateZIndexByOrder = function() {
		for(var key in arrLayers){
			var layer = arrLayers[key];
			if(layer.order !== undefined){
				var zindex = layer.order+1;
				jQuery("#slide_layer_"+key).css("z-index",zindex);
			}
		};		
	}	
	
	//Update the select html, set selected option, and update events.
	var updateCaptionsInput = function(arrCaptions) {		
		jQuery("#layer_caption").autocomplete("option","source",arrCaptions);
		
	}	
	
	//Get layers array
	t.getLayers = function() {
		if(selectedLayerSerial != -1)
			updateLayerFromFields();
		
		return(arrLayers);
	}	
	
	//Refresh layer events
	var refreshEvents = function(serial) {		
		//Update layer events
		var layer = getHtmlLayerFromSerial(serial);		
		layer.draggable({
					drag: onLayerDrag,	//Set ondrag event
					grid: [1,1]	//Set the grid to 1 pixel
				});
		
		layer.click(function(event){
			setLayerSelected(serial);
			event.stopPropagation();
		});				
	}
	
	//Get layer serial from id
	var getSerialFromID = function(layerID){
		var layerSerial = layerID.replace("slide_layer_","");
		return(layerSerial);
	}
	
	//Get serial from sortID
	var getSerialFromSortID = function(sortID){
		var layerSerial = sortID.replace("layer_sort_","");
		return(layerSerial);
	}
	
	//Get html layer from serial
	var getHtmlLayerFromSerial = function(serial){
		var htmlLayer = jQuery("#slide_layer_"+serial);
		if(htmlLayer.length == 0)
			UniteAdminBanner.showErrorMessage("Html Layer with serial: "+serial+" not found!");
		
		return(htmlLayer);
	}
	
	//Get sort field element from serial
	var getHtmlSortItemFromSerial = function(serial){
		var htmlSortItem = jQuery("#layer_sort_"+serial);
		if(htmlSortItem.length == 0){
			UniteAdminBanner.showErrorMessage("Html sort field with serial: "+serial+" not found!");
			return(false);
		}
		
		return(htmlSortItem);
	}
	
	//Get layer object by id
	var getLayer = function(serial){
		var layer = arrLayers[serial];
		if(!layer)
			UniteAdminBanner.showErrorMessage("getLayer error, Layer with serial:"+serial+"not found");
		
		//Modify some data
		layer.speed = Number(layer.speed);
		layer.endspeed = Number(layer.endspeed);
		
		return layer;
	}
	
	//Get current layer object
	var getCurrentLayer = function() {
		if(selectedLayerSerial == -1){
			UniteAdminBanner.showErrorMessage("Selected layer not set");
			return(null);
		}
		
		return getLayer(selectedLayerSerial);
	}	
	
	//Set layer object to array
	var setLayer = function(layerID,layer){
		if(!arrLayers[layerID]){
			UniteAdminBanner.showErrorMessage("setLayer error, Layer with ID:"+layerID+"not found");
			return(false);
		}
		arrLayers[layerID] = layer;
	}	
	
	//Make layer html, with params from the object
	var makeLayerHtml = function(serial,objLayer){
		var type = "text";
		if(objLayer.type)
			type = objLayer.type;
		
		var zIndex = Number(objLayer.order)+1;
		
		var style = "z-index:"+zIndex+";position:absolute;";
		var html = '<div id="slide_layer_' + serial + '" style="' + style + '" class="slide_layer caption '+objLayer.style+'" >';		
		
		//Add layer specific html
		switch(type){
			case "image":
				html += '<img src="'+objLayer.image_url+'" alt="'+objLayer.text+'"></img>';
			break;
			default:
			case "text":
				html += objLayer.text;	
			break;
			case "video":
				
				var styleVideo = "width:"+objLayer.video_width+"px;height:"+objLayer.video_height+"px;";
				
				switch(objLayer.video_type){
					case "youtube":						
					case "vimeo":
						styleVideo += ";background-image:url("+objLayer.video_image_url+");";
					break;
					case "html5":
						if(objLayer.video_image_url !== undefined && objLayer.video_image_url != "")
							styleVideo += ";background-image:url("+objLayer.video_image_url+");";
					break;
				}
				
				html += "<div class='slide_layer_video' style='"+styleVideo+"'><div class='video-layer-inner video-icon-"+objLayer.video_type+"'>"
				html += "<div class='layer-video-title'>" + objLayer.video_title + "</div>";
				html += "</div></div>";
			break;
		}
		
		//Add cross icon
		html += "<div class='icon_cross'></div>";
		
		html += '</div>';
		return(html);
	}	
	
	//Update layer by data object
	var updateLayer = function(serial,objData){
		var layer = getLayer(serial);
		if(!layer)
			return(false);
		
		for(key in objData){
			layer[key] = objData[key];
		}
		
		setLayer(serial,layer);
	}	
	
	//Update current layer
	var updateCurrentLayer = function(objData){
		if(!arrLayers[selectedLayerSerial]){
			UniteAdminBanner.showErrorMessage("error! the layer with serial: "+selectedLayerSerial+" don't exists");
			return(false);
		}
		
		updateLayer(selectedLayerSerial,objData);
	}	
	
	//Add image layer
	var addLayerImage = function(urlImage){
		
		objLayer = {
			style : "",
			text : "Image " + (id_counter+1),
			type : "image",
			image_url : urlImage
		};
				
		addLayer(objLayer);
	}	
	
	//Get video layer object from video data
	var getVideoObjLayer = function(videoData) {		
		var objLayer = {
				type:"video",
				style : "",
				video_type: videoData.video_type,
				video_width:videoData.width,
				video_height:videoData.height,
				video_data:videoData
			};
		
		if(objLayer.video_type == "youtube" || objLayer.video_type == "vimeo"){
			objLayer.video_id = videoData.id;
			objLayer.video_title = videoData.title;
			objLayer.video_image_url = videoData.thumb_medium.url;
			objLayer.video_args = videoData.args;
		}
		
		//Set sortbox text
		switch(objLayer.video_type){			
			case "youtube":
				objLayer.text = "Youtube: " + videoData.title;
			break;
			case "vimeo":
				objLayer.text = "Vimeo: " + videoData.title;
			break;
			case "html5":
				objLayer.text = "Html5 Video";
				objLayer.video_title = objLayer.text;
				objLayer.video_image_url = "";
				
				if(videoData.urlPoster != "")
					objLayer.video_image_url = videoData.urlPoster;
					
			break;
		}
		
		return(objLayer);
	}	
	
	//Add video layer
	var addLayerVideo = function(videoData){
		var objLayer = getVideoObjLayer(videoData);
		addLayer(objLayer);
	}
	
	
	//Add text layer
	var addLayerText = function() {
		
		var objLayer = {
				text:initText + (id_counter+1),
				type:"text"
		};
		
		addLayer(objLayer);
	}	
	
	//Add layer
	var addLayer = function(objLayer,isInit){
		
		if(!isInit)
			var isInit = false;
		
		//Set init fields (if not set)
		if(objLayer.order == undefined)
			objLayer.order = (id_counter);
		objLayer.order = Number(objLayer.order);
		
		//Set init position
		if(objLayer.type == "video"){
			if(objLayer.left == undefined)			
				objLayer.left = initLeftVideo;
			
			if(objLayer.top == undefined)			
				objLayer.top = initTopVideo;
			
			objLayer = checkUpdateFullwidthVideo(objLayer);
			
		} else {
			if(objLayer.left == undefined)
				objLayer.left = initLeft;
			
			if(objLayer.top == undefined)			
				objLayer.top = initTop;	
		}
		
		//Set animation
		if(objLayer.animation == undefined)			
			objLayer.animation = jQuery("#layer_animation").val();
		
		//Set easing
		if(objLayer.easing == undefined)
			objLayer.easing = jQuery("#layer_easing").val();
		
		//Set speed
		if(objLayer.speed == undefined)			
			objLayer.speed = initSpeed;

		if(objLayer.align_hor == undefined)			
			objLayer.align_hor = "left";
		
		if(objLayer.align_vert == undefined)
			objLayer.align_vert = "top";
		
		//Set hidden under		
		if(objLayer.hiddenunder == undefined)			
			objLayer.hiddenunder = "";	

		//Set resizeme
		if(objLayer.resizeme == undefined)			
			objLayer.resizeme = "";		
		
		//Set image link
		if(objLayer.link == undefined)
			objLayer.link = "";
		
		//Set image link open in
		if(objLayer.link_open_in == undefined)
			objLayer.link_open_in = "same";

		//Set slide link
		if(objLayer.link_slide == undefined)
			objLayer.link_slide = "nothing";
		
		//Set scroll under offset
		if(objLayer.scrollunder_offset == undefined)
			objLayer.scrollunder_offset = "";				
		
		
		//Set style, if empty, add first style from the list
		if(objLayer.style == undefined)
			objLayer.style = jQuery("#layer_caption").val();
		
		objLayer.style = jQuery.trim(objLayer.style);
		if(isInit == false && objLayer.type == "text" && (!objLayer.style || objLayer.style == "") ){
			objLayer.style = getFirstStyle();
		}
		
		//Add time
		if(objLayer.time == undefined)			
			objLayer.time = getNextTime();
		
		objLayer.time = Number(objLayer.time);	//casting
		
		//End time
		if(objLayer.endtime == undefined)
			objLayer.endtime = "";

		if(objLayer.endspeed == undefined)
			objLayer.endspeed = initSpeed;
		
		//Set end animation
		if(objLayer.endanimation == undefined)			
			objLayer.endanimation = jQuery("#layer_endanimation").val();
		
		//Set end easing
		if(objLayer.endeasing == undefined)
			objLayer.endeasing = jQuery("#layer_endeasing").val();
		
		//Set corners
		if(objLayer.corner_left == undefined)			
			objLayer.corner_left = "nothing";
		
		if(objLayer.corner_right == undefined)			
			objLayer.corner_right = "nothing";
		
		//Round position
		objLayer.top = Math.round(objLayer.top);
		objLayer.left = Math.round(objLayer.left);
		
		objLayer.serial = id_counter;
		
		arrLayers[id_counter] = objLayer;
		
		//Add html
		var htmlLayer = makeLayerHtml(id_counter,objLayer);
		container.append(htmlLayer);
		
		var objHtmlLayer = getHtmlLayerFromSerial(id_counter);
		
		//Update layer position
		updateHtmlLayerPosition(objHtmlLayer,objLayer.top,objLayer.left,objLayer.align_hor,objLayer.align_vert);
		
		//Update corners
		updateHtmlLayerCorners(objHtmlLayer,objLayer);
		
		//Update cross position
		updateCrossIconPosition(objHtmlLayer,objLayer);
		
		//Add layer to sortbox
		addToSortbox(id_counter,objLayer);
		
		//Refresh draggables
		refreshEvents(id_counter);
		id_counter++;
		
		//Enable "delete all" button
		jQuery("#button_delete_all").removeClass("button-disabled");
		
		//Select the layer
		if(isInit == false){
			setLayerSelected(objLayer.serial);
			jQuery("#layer_text").focus();
		}				
	}	
	
	//Delete layer from layers object
	var deleteLayerFromObject = function(serial) {		
		var arrLayersNew = {};
		var flagFound = false;
		for (key in arrLayers){
			if(key != serial)
				arrLayersNew[key] = arrLayers[key];
			else
				flagFound = true;
		}
		
		if(flagFound == false)
			UniteAdminBanner.showErrorMessage("Can't delete layer, serial: "+serial+" not found");
		
		arrLayers = arrLayersNew;
	}
	
	//Delete the layer from html.
	var deleteLayerFromHtml = function(serial){
		var htmlLayer = getHtmlLayerFromSerial(serial);
		htmlLayer.remove();
	}		
	
	//Delete all representation of some layer
	var deleteLayer = function(serial){
		deleteLayerFromObject(serial);
		deleteLayerFromHtml(serial);
		deleteLayerFromSortbox(serial);
	}
	
	//Call "deleteLayer" function with selected serial
	var deleteCurrentLayer = function() {
		if(selectedLayerSerial == -1)
			return(false);
		
		deleteLayer(selectedLayerSerial);
		
		//Set unselected
		selectedLayerSerial = -1;
		
		//Clear form and disable buttons
		disableFormFields();
	}
	
	//Duplicate layer, set it a little aside of the layer position
	var duplicateLayer = function(serial){
		var obj = arrLayers[serial];		
		var obj2 = jQuery.extend(true, {}, obj);	//Duplicate object
		obj2.left += 5;
		obj2.top += 5;
		obj2.order = undefined;
		obj2.time = undefined;
		
		addLayer(obj2);
		redrawSortbox();
	}	
	
	//Call "duplicateLayer" function with selected serial 
	var duplicateCurrentLayer = function() {
		if(selectedLayerSerial == -1)
			return(false);
		
		duplicateLayer(selectedLayerSerial);
	}	
	
	//Delete all layers
	var deleteAllLayers = function() {
		arrLayers = {};
		container.html("");
		emptySortbox();
		selectedLayerSerial = -1;
		
		disableFormFields();
		jQuery("#button_delete_all").addClass("button-disabled");		
	}
	
	//Update the corners
	var updateHtmlLayerCorners = function(htmlLayer,objLayer) {		
		var ncch = htmlLayer.outerHeight();
		var bgcol = htmlLayer.css('backgroundColor');
		
		switch(objLayer.corner_left){
			case "curved":
				htmlLayer.append("<div class='frontcorner'></div>");				
			break;
			case "reverced":
				htmlLayer.append("<div class='frontcornertop'></div>");
			break;
		}
		
		switch(objLayer.corner_right){
			case "curved":
				htmlLayer.append("<div class='backcorner'></div>");				
			break;
			case "reverced":
				htmlLayer.append("<div class='backcornertop'></div>");
			break;
		}
		
			
		htmlLayer.find(".frontcorner").css({
            'borderWidth':ncch+"px",
            'left':(0-ncch)+'px',
            'borderRight':'0px solid transparent',
            'borderTopColor':bgcol
		});
		
		htmlLayer.find(".frontcornertop").css({
            'borderWidth':ncch+"px",
            'left':(0-ncch)+'px',
            'borderRight':'0px solid transparent',
            'borderBottomColor':bgcol
		});
		
		htmlLayer.find('.backcorner').css({
            'borderWidth':ncch+"px",
            'right':(0-ncch)+'px',
            'borderLeft':'0px solid transparent',
            'borderBottomColor':bgcol
        });		
        
		htmlLayer.find('.backcornertop').css({
             'borderWidth':ncch+"px",
             'right':(0-ncch)+'px',
             'borderLeft':'0px solid transparent',
             'borderTopColor':bgcol
         });
		 
	}
	
	//Update the position of html cross
	var updateCrossIconPosition = function(objHtmlLayer,objLayer) {		
		var htmlCross = objHtmlLayer.find(".icon_cross");
		var crossWidth = htmlCross.width();
		var crossHeight = htmlCross.height();
		var totalWidth = objHtmlLayer.outerWidth();
		var totalHeight = objHtmlLayer.outerHeight();
		var crossHalfW = Math.round(crossWidth / 2);
		var crossHalfH = Math.round(crossHeight / 2);
		
		var posx = 0;
		var posy = 0;
		switch(objLayer.align_hor){
			case "left":
				posx = - crossHalfW;
			break;
			case "center":
				posx = (totalWidth - crossWidth) / 2;
			break;
			case "right":
				posx = totalWidth - crossHalfW;
			break;
		}

		switch(objLayer.align_vert){
			case "top":
				posy = - crossHalfH;
			break;
			case "middle":
				posy = (totalHeight - crossHeight) / 2;
			break;
			case "bottom":
				posy = totalHeight - crossHalfH;
			break;
		}
		
		htmlCross.css({"left":posx+"px","top":posy+"px"});
	}
	
	
	//Update html layer position
	var updateHtmlLayerPosition = function(htmlLayer,top,left,align_hor,align_vert){
		
		//Update positions by align
		var width = htmlLayer.width();
		var height = htmlLayer.height();
		var totalWidth = container.width();
		var totalHeight = container.height();
		
		var objCss = {};
		
		//Handle horizontal
		switch(align_hor){
			default:
			case "left":
				objCss["right"] = "auto";
				objCss["left"] = left+"px";
			break;
			case "right":
				objCss["left"] = "auto";
				objCss["right"] = left+"px"; 
			break;
			case "center":
				var realLeft = (totalWidth - width)/2;
				realLeft = Math.round(realLeft) + left;
				objCss["left"] = realLeft + "px";
				objCss["right"] = "auto";
			break;
		}
		
		//Handle vertical
		switch(align_vert){
			default:
			case "top":
				objCss["bottom"] = "auto";
				objCss["top"] = top+"px";
			break;
			case "middle":
				var realTop = (totalHeight - height)/2;
				realTop = Math.round(realTop)+top;
				objCss["top"] = realTop + "px";
				objCss["bottom"] = "auto";
			break;
			case "bottom":
				objCss["top"] = "auto";
				objCss["bottom"] = top+"px";
			break;
		}		
		
		htmlLayer.css(objCss);
	}	
	
	//Check / update full width video position and size
	var checkUpdateFullwidthVideo = function(objLayer) {		
		if(objLayer.type != "video")
			return(objLayer);
		
		if(objLayer.video_data && objLayer.video_data.fullwidth && objLayer.video_data.fullwidth == true){
			objLayer.top = 0;
			objLayer.left = 0;
			objLayer.align_hor = "left";
			objLayer.align_vert = "top";
			objLayer.video_width = container.width();
			objLayer.video_height = container.height();
		}
				
		return(objLayer);
	}	
	
	//Update html layers from object
	var updateHtmlLayersFromObject = function(serial){
		if(!serial)
			serial = selectedLayerSerial
			
		var objLayer = getLayer(serial);
		
		if(!objLayer)
			return(false);
		
		var htmlLayer = getHtmlLayerFromSerial(serial);
		
		//Set class name
		var className = "slide_layer ui-draggable caption";
		if(serial == selectedLayerSerial)
			className += " layer_selected";
		className += " "+objLayer.style;
		htmlLayer.attr("class",className);
		

		//Set html
		var type = "text";
		if(objLayer.type)
			type = objLayer.type;
		
		//Update layer by type:
		switch(type){
			case "image":
			break;
			case "video":	//Update fullwidth position
				objLayer = checkUpdateFullwidthVideo(objLayer);
			break;
			default:
			case "text":
				htmlLayer.html(objLayer.text);
				updateHtmlLayerCorners(htmlLayer,objLayer);
				htmlLayer.append("<div class='icon_cross'></div>");
			break;
		}
		
		//Set position
		updateHtmlLayerPosition(htmlLayer,objLayer.top,objLayer.left,objLayer.align_hor,objLayer.align_vert);
		
		updateCrossIconPosition(htmlLayer,objLayer);		
	}	
	
	//update layer from html fields
	var updateLayerFromFields = function() {		
		if(selectedLayerSerial == -1){
			UniteAdminBanner.showErrorMessage("No layer selected, can't update.");
			return(false);
		}
		
		var objUpdate = {};
		
		objUpdate.style = jQuery("#layer_caption").val();
		objUpdate.text = jQuery("#layer_text").val();
		objUpdate.top = Number(jQuery("#layer_top").val());
		objUpdate.left = Number(jQuery("#layer_left").val());				
		objUpdate.animation = jQuery("#layer_animation").val();		
		objUpdate.speed = jQuery("#layer_speed").val();
		objUpdate.align_hor = jQuery("#layer_align_hor").val();
		objUpdate.align_vert = jQuery("#layer_align_vert").val();
		objUpdate.hiddenunder = jQuery("#layer_hidden").is(":checked");
		objUpdate.resizeme = jQuery("#layer_resizeme").is(":checked");		
		objUpdate.easing = jQuery("#layer_easing").val();
		objUpdate.link_slide = jQuery("#layer_slide_link").val();
		objUpdate.scrollunder_offset = jQuery("#layer_scrolloffset").val();		
		
		objUpdate.link = jQuery("#layer_image_link").val();
		objUpdate.link_open_in = jQuery("#layer_link_open_in").val();
		
		objUpdate.endtime = jQuery("#layer_endtime").val();				
		objUpdate.endanimation = jQuery("#layer_endanimation").val();				
		objUpdate.endspeed = jQuery("#layer_endspeed").val();				
		objUpdate.endeasing = jQuery("#layer_endeasing").val();
		
		objUpdate.corner_left = jQuery("#layer_cornerleft").val();
		objUpdate.corner_right = jQuery("#layer_cornerright").val();
		
		//update object
		updateCurrentLayer(objUpdate);
		
		//update html layers
		updateHtmlLayersFromObject();
		
		//update html sortbox
		updateHtmlSortboxFromObject();
		
		//update the timeline with the new data
		updateCurrentLayerTimeline();		
	}	
	
	//redraw some layer html
	var redrawLayerHtml = function(serial) {		
		var objLayer = getLayer(serial);		
		var html = makeLayerHtml(serial,objLayer)
		var htmlInner = jQuery(html).html();
		var htmlLayer = getHtmlLayerFromSerial(serial);
		
		htmlLayer.html(htmlInner);
	}	
	
	//update layer parameters from the object
	var updateLayerFormFields = function(serial) {
		var objLayer = arrLayers[serial];
		
		jQuery("#layer_caption").val(objLayer.style);
		jQuery("#layer_text").val(objLayer.text);
		jQuery("#layer_top").val(objLayer.top);
		jQuery("#layer_left").val(objLayer.left);
		jQuery("#layer_animation").val(objLayer.animation);
		
		jQuery("#layer_easing").val(objLayer.easing);
		jQuery("#layer_slide_link").val(objLayer.link_slide);
		jQuery("#layer_scrolloffset").val(objLayer.scrollunder_offset);
		
		jQuery("#layer_speed").val(objLayer.speed);
		jQuery("#layer_align_hor").val(objLayer.align_hor);
		jQuery("#layer_align_vert").val(objLayer.align_vert);
		
		if(objLayer.hiddenunder == "true" || objLayer.hiddenunder == true)
			jQuery("#layer_hidden").prop("checked",true);
		else
			jQuery("#layer_hidden").prop("checked",false);

		if(objLayer.resizeme == "true" || objLayer.resizeme == true)
			jQuery("#layer_resizeme").prop("checked",true);
		else
			jQuery("#layer_resizeme").prop("checked",false);		
		
		jQuery("#layer_image_link").val(objLayer.link);
		jQuery("#layer_link_open_in").val(objLayer.link_open_in);
		
		jQuery("#layer_endtime").val(objLayer.endtime);
		jQuery("#layer_endanimation").val(objLayer.endanimation);
		jQuery("#layer_endeasing").val(objLayer.endeasing);
		jQuery("#layer_endspeed").val(objLayer.endspeed);
		
		//set advanced params
		jQuery("#layer_cornerleft").val(objLayer.corner_left);
		jQuery("#layer_cornerright").val(objLayer.corner_right);
				
		//set align table
		var alignClass = "#linkalign_"+objLayer.align_hor+"_"+objLayer.align_vert;
		jQuery("#align_table a").removeClass("selected");
		jQuery(alignClass).addClass("selected");
		
		//show / hide go under slider offset row
		showHideOffsetRow();
	}	
	
	//unselect all html layers
	var unselectHtmlLayers = function() {
		jQuery(containerID + " .slide_layer").removeClass("layer_selected");
	}	
	
	//set all layers unselected
	var unselectLayers = function() {
		unselectHtmlLayers();
		unselectSortboxItems();
		selectedLayerSerial = -1;
		disableFormFields();
		hideLayerTimeline();
		
		//reset elements
		jQuery("#button_edit_video_row").hide();
		jQuery("#button_change_image_source_row").hide();
		jQuery("#layer_text").css("height","80px");
		
		jQuery("#layer_image_link_row").hide();
		jQuery("#layer_link_open_in_row").hide();
	}	
	
	//set layer selected representation
	var setLayerSelected = function(serial) {		
		if(selectedLayerSerial == serial)
			return(false);
		
		objLayer = getLayer(serial);
		
		var layer = getHtmlLayerFromSerial(serial);
		
		//unselect all other layers
		unselectHtmlLayers();
		
		//set selected class
		layer.addClass("layer_selected");
						
		setSortboxItemSelected(serial);
		
		//update selected serial var
		selectedLayerSerial = serial;
		
		//update bottom fields
		updateLayerFormFields(serial);
		
		//enable form fields
		enableFormFields();
				
		//do specific operations depends on type
		switch(objLayer.type){
			case "video":	//show edit video button
				jQuery("#linkInsertButton").addClass("disabled");
				jQuery("#button_edit_video_row").show();
				
				jQuery("#layer_text").css("height","25px");
			break;
			case "image":	
				//disable the insert button
				jQuery("#linkInsertButton").addClass("disabled");
				
				//show / hide some elements
				jQuery("#button_change_image_source_row").show();
				jQuery("#layer_text").css("height","25px");
				jQuery("#layer_image_link_row").show();
				jQuery("#layer_link_open_in_row").show();
			break;
			default:  //set layer text to default height
				jQuery("#layer_text").css("height","80px");
			break;
		}
		
		//hide edit video button
		if(objLayer.type != "video"){
			jQuery("#button_edit_video_row").hide();
		}
		
		//hide image layer related fields
		if(objLayer.type != "image"){			
			jQuery("#layer_image_link_row").hide();
			jQuery("#layer_link_open_in_row").hide();
			jQuery("#button_change_image_source_row").hide();
		}
		
		//show/hide text related layers
		if(objLayer.type == "text"){
			jQuery("#layer_cornerleft_row").show();
			jQuery("#layer_cornerright_row").show();
			jQuery("#layer_resizeme_row").show();
		}else{
			jQuery("#layer_cornerleft_row").hide();
			jQuery("#layer_cornerright_row").hide();
			jQuery("#layer_resizeme_row").hide();
		}						
			
		//hide autocomplete
		jQuery( "#layer_caption" ).autocomplete("close");
		
		//make layer form validations
		doCurrentLayerValidations();
		
		//update timeline of the layer
		updateCurrentLayerTimeline();
		
		//set focus to text editor
		jQuery("#layer_text").focus();
	}	
	
	//Return if the layer is selected or not
	var isLayerSelected = function(serial){
		return(serial == selectedLayerSerial);
	}

	//Hide in html and sortbox
	var hideLayer = function(serial,skipGlobalButton){
		var htmlLayer = jQuery("#slide_layer_"+serial);
		htmlLayer.hide();
		setSortboxItemHidden(serial);
		
		if(skipGlobalButton != true){
			if(isAllLayersHidden())
				jQuery("#button_sort_visibility").addClass("e-disabled");
		}
	}	
	
	//Show layer in html and sortbox
	var showLayer = function(serial,skipGlobalButton){
		var htmlLayer = jQuery("#slide_layer_"+serial);
		htmlLayer.show();		
		setSortboxItemVisible(serial);
		
		if(skipGlobalButton != true)
			jQuery("#button_sort_visibility").removeClass("e-disabled");		
	}	
	
	//Hide all layers
	var showAllLayers = function() {
		for (serial in arrLayers)
			showLayer(serial,true);		
	}

	//Hide all layers
	var hideAllLayers = function() {
		for (serial in arrLayers)
			hideLayer(serial,true);
	}		
		
	//Get true / false if the layer is hidden
	var isLayerVisible = function(serial){
		var htmlLayer = jQuery("#slide_layer_"+serial);
		var isVisible = htmlLayer.is(":visible");
		return(isVisible);
	}
	
	//Get true / false if all layers hidden
	var isAllLayersHidden = function() {
		for(serial in arrLayers){
			if(isLayerVisible(serial) == true)
				return(false);
		}
		
		return(true);
	}
	
	
	/****************************
	    - Sortbox functions -
	****************************/
	
	//Init the sortbox
	var initSortbox = function() {
				
		redrawSortbox();
		
		//Set the sortlist sortable
		jQuery( "#sortlist" ).sortable({
				axis:'y',
				update: function() {
					onSortboxSorted();
				}
		});
		
		//Set input time events
		jQuery("#sortlist").delegate(".sortbox_time","keyup",function(event) {
			if(event.keyCode == 13)
				onSortboxTimeChange(jQuery(this));
		});
		
		jQuery("#sortlist").delegate(".sortbox_time","blur",function(event) {
			onSortboxTimeChange(jQuery(this));
		});
		
		jQuery("#sortlist").delegate(".sortbox_depth","focus",function(event) {
			jQuery(this).blur();
		});
		
		//Set click event
		jQuery("#sortlist").delegate("li","mousedown",function() {
			var serial = getSerialFromSortID(this.id);
			setLayerSelected(serial);
		});
		
		//Sort type buttons events
		jQuery(".button_sorttype").click(function() {
			var mode = this.id.replace("button_sort_","");
			changeSortmode(mode);
		});
		
		//On show / hide layer icon click - show / hide layer
		jQuery("#sortlist").delegate(".sortbox_eye","mousedown",function(event){
			
			var sortboxID = jQuery(this).parent().attr("id");
			var serial = getSerialFromSortID(sortboxID);
			if(isLayerVisible(serial))
				hideLayer(serial);
			else
				showLayer(serial);
			
			//Prevent the layer from selecting
			event.stopPropagation();
		});		
		
		//Show / hide all layers
		jQuery("#button_sort_visibility").click(function() {
			var button = jQuery(this);
			if(button.hasClass("e-disabled")){	
				//Show all
				button.removeClass("e-disabled");
				showAllLayers();
			} else {	
				//Hide all
				button.addClass("e-disabled");
				hideAllLayers();
			}				
		});		
	}	
	
	//Set sortbox items selected
	var setSortboxItemSelected = function(serial) {
		var sortItem = getHtmlSortItemFromSerial(serial);			
		unselectSortboxItems();
		
		sortItem.removeClass("ui-state-default").addClass("ui-state-hover");
	}	
	
	//Set sortbox item hidden mode
	var setSortboxItemHidden = function(serial) {
		var sortItem = getHtmlSortItemFromSerial(serial);
		sortItem.addClass("sortitem-hidden");
	}
	
	//Set sortbox item visible mode
	var setSortboxItemVisible = function(serial) {
		var sortItem = getHtmlSortItemFromSerial(serial);
		sortItem.removeClass("sortitem-hidden");
	}	
	
	//Change sortmode, display the changes
	var changeSortmode = function(mode) {		
		if(mode != "depth" && mode != "time"){
			trace("wrong mode: "+mode);
		}
		if(sortMode == mode)
			return(false);
		
		sortMode = mode;
		
		redrawSortbox();
		
		if(sortMode == "time"){
			//Change to time mode
			jQuery("#button_sort_time").removeClass("ui-state-hover").addClass("ui-state-active");
			jQuery("#button_sort_depth").removeClass("ui-state-active").addClass("ui-state-hover");	
			
		}else{	
			//Change to depth mode			
			jQuery("#button_sort_depth").removeClass("ui-state-hover").addClass("ui-state-active");
			jQuery("#button_sort_time").removeClass("ui-state-active").addClass("ui-state-hover");
			
			updateOrderFromSortbox();
		}
	}
	
	
	//Add layer to sortbox
	var addToSortbox = function(serial,objLayer) {		
		var isVisible = isLayerVisible(serial);
		var classLI = "";
		if(isVisible == false)
			classLI = " sortitem-hidden";
				
		var sortboxText = getSortboxText(objLayer.text);
		var depth = Number(objLayer.order)+1;
		
		var htmlSortbox = '<li id="layer_sort_'+serial+'" class="ui-state-default'+classLI+'"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span>';
		htmlSortbox += '<span class="sortbox_text">' + sortboxText + '</span>';
		htmlSortbox += '<div class="sortbox_eye"></div>';
		htmlSortbox += '<input type="text" class="sortbox_time" title="Edit Timeline" value="'+objLayer.time+'">';
		htmlSortbox += '<input type="text" class="sortbox_depth" readonly title="Edit Depth" value="'+depth+'">';
		htmlSortbox += '<div class="clear"></div>';
		htmlSortbox += '</li>';
		
		jQuery("#sortlist").append(htmlSortbox);
	}	
	
	//Delete layer from sortbox
	var deleteLayerFromSortbox = function(serial){
		var sortboxLayer = getHtmlSortItemFromSerial(serial);
		sortboxLayer.remove();
	}
	
	//Unselect all items in sortbox
	var unselectSortboxItems = function() {
		jQuery("#sortlist li").removeClass("ui-state-hover").addClass("ui-state-default");
	}	
	
	//Update layers order from sortbox elements
	var updateOrderFromSortbox = function() {		
		var arrSortLayers = jQuery( "#sortlist" ).sortable("toArray");
		
		for(var i=0;i<arrSortLayers.length;i++){
			var sortID = arrSortLayers[i];
			var serial = getSerialFromSortID(sortID);
			var objUpdate = {order:i};
			updateLayer(serial,objUpdate);
						
			//Update sortbox order input
			var depth = i+1;
			jQuery("#"+sortID+" input.sortbox_depth").val(depth);
		}
		
		//Update z-index of the html window by order
		updateZIndexByOrder();
	}

	//shift order among all the layers, push down all order num beyong the given
	//Need to redraw after this function
	var shiftOrder = function(orderToFree){
		
		for(key in arrLayers){
			var obj = arrLayers[key];
			if(obj.order >= orderToFree){
				obj.order = Number(obj.order)+1;
				arrLayers[key] = obj;
			}
		}
	}	
	
	//Get sortbox text from layer html
	var getSortboxText = function(text){
		sorboxTextSize = 50;
		var textSortbox = UniteAdminBanner.stripTags(text);
		
		//If no content - escape html
		if(textSortbox.length < 2)
			textSortbox = UniteAdminBanner.htmlspecialchars(text);
			
		//Short text
		if(textSortbox.length > sorboxTextSize)
			textSortbox = textSortbox.slice(0,sorboxTextSize)+"...";
		
		return(textSortbox);
	}
	
	//Redraw the sortbox
	var redrawSortbox = function(mode) {				
		if(mode == undefined)
			mode = sortMode;
				
		emptySortbox();
				
		var layers_array = getLayersSorted(mode);
		
		if(layers_array.length == 0)
			return(false);
		
		for(var i=0; i<layers_array.length;i++){
			var objLayer = layers_array[i];
			addToSortbox(objLayer.serial,objLayer);
		}
				
		if(selectedLayerSerial != -1)
			setSortboxItemSelected(selectedLayerSerial);		
	}		
	
	//Remove all from sortbox
	var emptySortbox = function() {
		jQuery("#sortlist").html("");
	}
	
	//Update sortbox text from object
	var updateHtmlSortboxFromObject = function(serial){
		if(!serial)
			serial = selectedLayerSerial;

		var objLayer = getLayer(serial);
		
		if(!objLayer)
			return(false);
		
		var htmlSortItem = getHtmlSortItemFromSerial(serial);
		
		if(!htmlSortItem)
			return(false);

		var sortboxText = getSortboxText(objLayer.text);
		htmlSortItem.children(".sortbox_text").text(sortboxText);
	}
	
	//On sortbox sorted event.
	var onSortboxSorted = function() {		
		if(sortMode == "depth") {
			updateOrderFromSortbox();
		} else {	
			//Sort by time
			redistributeTimes();
		}
	}
		
	
	/*************************
	    - Time functions -
	*************************/
	
	//Get next available time
	var getNextTime = function() {
		var maxTime = 0;
		
		//Get max time
		for (key in arrLayers){
			var layer = arrLayers[key];
			
			layerTime = (layer.time)?Number(layer.time):0;
			
			if(layerTime > maxTime)
					maxTime = layerTime;
		}
				
		var outputTime;
		
		if(maxTime == 0)
			outputTime = g_startTime;
		else
			outputTime = Number(maxTime) + Number(g_stepTime);
						
		return(outputTime);
	}	
	
	//Change time on the layer from the sortbox and reorder
	var onSortboxTimeChange = function(inputBox) {		
		//Update the time by inputbox:
		var timeValue = inputBox.val();
		timeValue = Number(timeValue);
		var sortLayerID = inputBox.parent().attr("id");
		var serial = getSerialFromSortID(sortLayerID);		
		var objUpdate = {time:timeValue};
		
		updateLayer(serial,objUpdate);
		
		if(sortMode == "time")
			redrawSortbox();
		
		validateCurrentLayerTimes();
	}
	
	//Change time on the layer from the sortbox and reorder
	var onSortboxDepthChange = function(inputBox) {		
		//Update the time by inputbox:
		var depthValue = inputBox.val();
		depthValue = Number(depthValue);
		var order = depthValue-1;
		
		var sortLayerID = inputBox.parent().attr("id");
		var serial = getSerialFromSortID(sortLayerID);		
		var objUpdate = {order:order};
		
		updateLayer(serial,objUpdate);
		
		redrawSortbox();
		
		if(sortMode == "depth")
			updateOrderFromSortbox();		
	}	
	
	//Order layers by time
	//Type can be [time] or [order]
	var getLayersSorted = function(type) {		
		if(type == undefined)
			type = "time";
		
		//Convert to array
		var layers_array = [];
		for(key in arrLayers){
			var obj = arrLayers[key];
			obj.serial = key;
			layers_array.push(obj);
		}
		
		if(layers_array.length == 0)
			return(layers_array);
			
		//Sort layers array
		layers_array.sort(function(layer1,layer2) {			
			switch(type){
				case "time":					
					if(Number(layer1.time) == Number(layer2.time)){
						if(layer1.order == layer2.order)
							return(0);
						
						if(layer1.order > layer2.order)
							return(1);
						
						return(-1);
					}
					
					if(Number(layer1.time) > Number(layer2.time))
						return(1);
					break;
				case "depth":
					if(layer1.order == layer2.order)
						return(0);
					
					if(layer1.order > layer2.order)
						return(1);
					break;
				default:
					trace("wrong sort type: "+type);
					break;
			}
			
			return(-1);
		});
		
		return(layers_array);		
	}	
	
	//Redistribute times between the layers sorted from small to big
	var redistributeTimes = function() {		
		//Collect times to array:
		var arrTimes = [];
		for(key in arrLayers)
			arrTimes.push(Number(arrLayers[key].time));
		
		arrTimes.sort(function(a,b){return a-b});	//sort number
		
		var arrSortLayers = jQuery( "#sortlist" ).sortable("toArray");

		for(var i=0;i<arrSortLayers.length;i++){
			var sortID = arrSortLayers[i];
			var serial = getSerialFromSortID(sortID);
			
			//Update time
			var newTime = arrTimes[i];
			var objUpdate = {time:newTime};
			updateLayer(serial,objUpdate);
			
			//Update input box
			jQuery("#"+sortID+" input.sortbox_time").val(newTime);
		}		
	}	
	
	/****************************
	     - Events functions -
	****************************/
	
	//On layer drag event - update layer position
	var onLayerDrag = function() {		
		var layerSerial = getSerialFromID(this.id);
		var htmlLayer = jQuery(this); 
		var position = htmlLayer.position();
		
		var objLayer = getLayer(layerSerial);
		
		var posTop = Math.round(position.top);
		var posLeft = Math.round(position.left);		
		var layerWidth = htmlLayer.width();
		var totalWidth = container.width();
		var layerHeight = htmlLayer.height();
		var totalHeight = container.height();
		
		var updateY,updateX;
			
		switch(objLayer.align_hor){
			case "left":
				updateX = posLeft;
			break;
			case "right":
				updateX = totalWidth - posLeft - layerWidth;
			break;
			case "center":
				updateX = posLeft - (totalWidth - layerWidth)/2;
				updateX = Math.round(updateX);
			break;
		}
		
		switch(objLayer.align_vert){
			case "top":
				updateY = posTop;
			break;
			case "bottom":
				updateY = totalHeight - posTop - layerHeight;
			break;
			case "middle":
				updateY = posTop - (totalHeight - layerHeight)/2;
				updateY = Math.round(updateY);
			break;
		}
		
		var objUpdate = {top:updateY,left:updateX};
		updateLayer(layerSerial,objUpdate);	
		
		//Update the position back with the rounded numbers (improve precision)
		updateHtmlLayerPosition(htmlLayer,objUpdate.top,objUpdate.left);
		
		//Update bottom fields (only if selected)
		if(isLayerSelected(layerSerial))
			updateLayerFormFields(layerSerial);
	}	
	
	//Move some layer
	var moveLayer = function(serial,dir,step) {
		var layer = getLayer(serial);
		if(!layer)
			return(false);
		
		switch(dir) {
			case "down":
				arrLayers[serial].top += step;
			break;
			case "up":
				arrLayers[serial].top -= step;
			break;
			case "right":
				arrLayers[serial].left += step;
			break;
			case "left":
				arrLayers[serial].left -= step;
			break;			
			default:
				UniteAdminBanner.showErrorMessage("wrong direction: "+dir);
				return(false);
			break;
		}
		
		updateHtmlLayersFromObject(serial);
		
		if(isLayerSelected(serial))
			updateLayerFormFields(serial);
	}
	
	/*****************************
	    - Timeline functions -
	*****************************/	
	
	//Get some calculations like real end time
	var getLayerExtendedParams = function(layer) {				
		var endSpeed = layer.endspeed;
		if(!endSpeed)
			endSpeed = layer.speed;
		
		endSpeed = Number(endSpeed); 
		
		var endTime = layer.endtime;
		
		var realEndTime;
		
		if(!endTime || endTime == undefined || endTime == ""){	//End time does not given
			endTime = g_slideTime - Number(layer.speed);
			realEndTime = g_slideTime;
		}else{	//end time given
			realEndTime = Number(endTime) + Number(endSpeed);
		}
		
		layer.endTimeFinal = Number(endTime); 	 //Time caption stay - without end transition
		layer.endSpeedFinal = Number(endSpeed); 	
		layer.realEndTime = Number(realEndTime); //Time with end transition
		
		layer.timeLast = layer.realEndTime - layer.time;	//Time that the whole caption last
		
		return(layer);
	}	
	
	//Hide layer timeline
	var hideLayerTimeline = function() {
		jQuery("#layer_timeline").hide();
	}	
	
	//Show layer timeline
	var showLayerTimeline = function(xStart,widthLast,mode) {		
		var props = {};
		props.left = xStart+"px";
		props.width = widthLast+"px";
		var layerTimeline = jQuery("#layer_timeline");
		
		if(mode == "error"){
			layerTimeline.addClass("layertime-error");
			layerTimeline.prop("title","Error - Something wrong with the caption times!");
		}
		else{
			layerTimeline.removeClass("layertime-error");
			layerTimeline.prop("title","");
		}
		
		jQuery("#layer_timeline").show().css(props);
	}	
	
	//Update timeline of current layer
	var updateCurrentLayerTimeline = function() {
		var layer = getCurrentLayer();
		if(!layer)
			return(false);
		
		layer = getLayerExtendedParams(layer);
		
		var gTimeline = jQuery("#global_timeline");		
		var gWidth = gTimeline.width();
		
		var multiplier = gWidth / g_slideTime;
		
		var widthLast = Math.round(layer.timeLast * multiplier);
		var widthStart = Math.round(layer.speed * multiplier);
		var widthEnd = Math.round(layer.endSpeedFinal * multiplier);
				
		var xStart = Math.round(layer.time * multiplier);
		var xEnd = Math.round(layer.endTimeFinal * multiplier);	//Start of the end transition
		
		var xFinal = xStart + widthLast;
		
		if(xFinal > (gWidth+1)){
			var errorWidth;
			if(xStart >= gWidth){
				hideLayerTimeline();
			}else{
				errorWidth = gWidth - xStart;
				showLayerTimeline(xStart,errorWidth,"error");	//Show error timeline mode
			}
		} else {
			showLayerTimeline(xStart,widthLast);
		}
	}
	
	
}




