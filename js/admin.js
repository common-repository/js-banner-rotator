var UniteAdminBanner = new function() {
	
	var t = this;
	
	var errorMessageID = null;
	var successMessageID = null;
	var ajaxLoaderID = null;
	var ajaxHideButtonID = null;
	
	//Video dialog vars
	var lastVideoData = null;		//Last fetched data
	var lastVideoCallback = null;   //Last callback from video dialog return
	var colorPickerCallback = null;
	

	//Escape html, turn html to a string
	t.htmlspecialchars = function(string){
	  return string
		  .replace(/&/g, "&amp;")
		  .replace(/</g, "&lt;")
		  .replace(/>/g, "&gt;")
		  .replace(/"/g, "&quot;")
		  .replace(/'/g, "&#039;");
	}	
	
	//Turn string value ("true", "false") to string 
	t.strToBool = function(str) {		
		if(str == undefined)
			return(false);
			
		if(typeof(str) != "string")
			return(false);
		
		str = str.toLowerCase();
		
		var bool = (str == "true") ? true : false;
		return(bool);
	}
	
	//Set callback on color picker movement
	t.setColorPickerCallback = function(callbackFunc){
		colorPickerCallback = callbackFunc;
	}
	
	//On color picker event. Pass the event further
	t.onColorPickerMoveEvent = function(event) {		
		if(typeof colorPickerCallback == "function")
			colorPickerCallback(event);
	}	
	
	//Strip html tags
	t.stripTags = function(input, allowed) {
	    allowed = (((allowed || "") + "").toLowerCase().match(/<[a-z][a-z0-9]*>/g) || []).join(''); // making sure the allowed arg is a string containing only tags in lowercase (<a><b><c>)
	    var tags = /<\/?([a-z][a-z0-9]*)\b[^>]*>/gi,
	        commentsAndPhpTags = /<!--[\s\S]*?-->|<\?(?:php)?[\s\S]*?\?>/gi;
	    return input.replace(commentsAndPhpTags, '').replace(tags, function ($0, $1) {
	        return allowed.indexOf('<' + $1.toLowerCase() + '>') > -1 ? $0 : '';
	    });
	}
	
	//Debug html on the top of the page (from the master view)
	t.debug = function(html){
		jQuery("#div_debug").show().html(html);
	}
	
	//Output data to console
	t.trace = function(data,clear) {
		if(clear && clear == true)
			console.clear();	
		console.log(data);
	}
	
	//Show error message or call once custom handler function
	t.showErrorMessage = function(htmlError) {
		if(errorMessageID !== null){
			jQuery("#"+errorMessageID).show().html(htmlError);			
		}else
			jQuery("#error_message").show().html(htmlError);
		
		showAjaxButton();
	}

	//Hide error message
	var hideErrorMessage = function() {
		if(errorMessageID !== null){
			jQuery("#"+errorMessageID).hide();
			errorMessageID = null;
		}else
			jQuery("#error_message").hide();
	}	
	
	//Set error message id
	t.setErrorMessageID = function(id) {
		errorMessageID = id;
	}	
	
	//Set success message id
	t.setSuccessMessageID = function(id) {
		successMessageID = id;
	}
	
	//Show success message
	var showSuccessMessage = function(htmlSuccess) {
		var id = "#success_message";		
		var delay = 2000;
		if(successMessageID){
			id = "#"+successMessageID;
			delay = 500;
		}
		
		jQuery(id).show().html(htmlSuccess);
		setTimeout("UniteAdminBanner.hideSuccessMessage()",delay);
	}	
	
	//Hide success message
	this.hideSuccessMessage = function() {		
		if(successMessageID){
			jQuery("#"+successMessageID).hide();
			successMessageID = null;	//Can be used only once
		} else
			jQuery("#success_message").slideUp("slow").fadeOut("slow");
		
		showAjaxButton();
	}	
	
	//Set ajax loader id that will be shown, and hidden on ajax request
	//This loader will be shown only once, and then need to be sent again
	this.setAjaxLoaderID = function(id) {
		ajaxLoaderID = id;
	}
	
	//Show loader on ajax actions
	var showAjaxLoader = function() {
		if(ajaxLoaderID)
			jQuery("#"+ajaxLoaderID).show();
	}
	
	//Hide and remove ajax loader. next time has to be set again before "ajaxRequest" function.
	var hideAjaxLoader = function() {
		if(ajaxLoaderID){
			jQuery("#"+ajaxLoaderID).hide();
			ajaxLoaderID = null;
		}
	}
	
	//Set button to hide / show on ajax operations.
	this.setAjaxHideButtonID = function(buttonID) {
		ajaxHideButtonID = buttonID;
	}
	
	//If exist ajax button to hide, hide it.
	var hideAjaxButton = function() {
		if(ajaxHideButtonID)
			jQuery("#"+ajaxHideButtonID).hide();
	}
	
	//If exist ajax button, show it, and remove the button id.
	var showAjaxButton = function() {
		if(ajaxHideButtonID){
			jQuery("#"+ajaxHideButtonID).show();
			ajaxHideButtonID = null;
		}		
	}	
	
	//Ajax request function. Call wp ajax, if error - print error message.
	//If success, call "success function" 
	t.ajaxRequest = function(action,data,successFunction) {			
		var objData = {
			action:g_uniteDirPlagin+"_ajax_action",
			client_action:action,
			nonce:g_bannerNonce,
			data:data
		};
		
		hideErrorMessage();
		showAjaxLoader();
		hideAjaxButton();
		
		jQuery.ajax({
			type:"post",
			url:ajaxurl,
			dataType: 'json',
			data:objData,
			success:function(response) {
				hideAjaxLoader();
				
				if(!response){
					t.showErrorMessage("Empty ajax response!");
					return(false);					
				}

				if(response == -1){
					t.showErrorMessage("ajax error!!!");
					return(false);
				}
				
				if(response == 0){
					t.showErrorMessage("ajax error, action: <b>"+action+"</b> not found");
					return(false);
				}
				
				if(response.success == undefined){
					t.showErrorMessage("The 'success' param is a must!");
					return(false);
				}
				
				if(response.success == false){
					t.showErrorMessage(response.message);
					return(false);
				}
				
				//Success actions

				//Run a success event function
				if(typeof successFunction == "function") {
					successFunction(response);
				} else {
					if(response.message)
						showSuccessMessage(response.message);
				}
				
				if(response.is_redirect)
					location.href=response.redirect_url;
			
			},		 	
			error:function(jqXHR, textStatus, errorThrown) {
				hideAjaxLoader();
				
				if(textStatus == "parsererror")
					t.debug(jqXHR.responseText);
				
				t.showErrorMessage("Ajax Error!!! " + textStatus);
			}
		});		
	}	
	
	//Open new add image dialog
	var openNewImageDialog = function(title,onInsert,isMultiple) {		
		if(isMultiple == undefined)
			isMultiple = false;
		
		//Media Library params
		var frame = wp.media({
			title : title,
			multiple : isMultiple,
			library : { type : 'image'},
			button : { text : 'Insert' }
		});

		//Runs on select
		frame.on('select',function() {
			var objSettings = frame.state().get('selection').first().toJSON();
			
			var selection = frame.state().get('selection');
			var arrImages = [];
			
			if(isMultiple == true) {		//Return image object when multiple
			    selection.map( function( attachment ) {
			    	var objImage = attachment.toJSON();
			    	var obj = {};
			    	obj.url = objImage.url;
			    	obj.id = objImage.id;
			    	arrImages.push(obj);
			    });
				onInsert(arrImages);
			} else {		//Return image url and id - when single
				onInsert(objSettings.url,objSettings.id);
			}
			    
		});

		//Open ML
		frame.open();
	}	
	
	//Open old add image dialog
	var openOldImageDialog = function(title,onInsert) {
		var params = "type=image&post_id=0&TB_iframe=true";
		
		params = encodeURI(params);
		
		tb_show(title,'media-upload.php?'+params);
		
		window.send_to_editor = function(html) {
			 tb_remove();
			 var urlImage = jQuery(html).attr('src');
			 if(!urlImage || urlImage == undefined || urlImage == "")
				var urlImage = jQuery('img',html).attr('src');
			
			onInsert(urlImage,"");	//Return empty id, it can be changed
		}
	}	
	
	t.openAddImageDialog = function(title,onInsert,isMultiple) {		
		if(!title)
			title = 'Select Image';
		
		if(typeof wp != "undefined" && typeof wp.media != "undefined") {
			openNewImageDialog(title,onInsert,isMultiple);
		} else {
			openOldImageDialog(title,onInsert);
		}		
	}	
	
	//Load css file on the fly, replace current item if exists
	t.loadCssFile = function(urlCssFile,replaceID) {		
		var rand = Math.floor((Math.random()*100000)+1);
		
		urlCssFile += "?rand="+rand;
		
		if(replaceID)
			jQuery("#"+replaceID).remove();
		
		jQuery("head").append("<link>");
		var css = jQuery("head").children(":last");
		css.attr({
		      rel:  "stylesheet",
		      type: "text/css",
		      href: urlCssFile
		});
		
		//Replace current element
		if(replaceID)
			css.attr({id:replaceID});
	}	
	
	//Get show image url
	t.getUrlShowImage = function(imageUrl,width,height,exact) {		
		var filepath = imageUrl.replace(g_urlContent,"");
		
		//If not internal image - return normal image url
		if(filepath == imageUrl) return(imageUrl);
		
		var urlImage = g_urlAjaxShowImage+"&img="+filepath;		
		if(width) urlImage += "&w="+width;		
		if(height) urlImage += "&h="+height;		
		if(exact && exact == true) urlImage += "&t=exact";
		
		return(urlImage);
	}	
	
	//Set html to youtube dialog, if empty data - clear the dialog
	var setYoutubeDialogHtml = function(data) {		
		//If empty data - clear the dialog
		if(!data){
			jQuery("#video_content").html("");
			return(false);
		}
		
		var thumb = data.thumb_medium;
		
		var html = '<div class="video-content-title">'+data.title+'</div>';
		html += '<img src="'+thumb.url+'" width="'+thumb.width+'" height="'+thumb.height+'" alt="thumbnail">';
		html += '<div class="video-content-description">'+data.desc_small+'</div>';
		
		jQuery("#video_content").html(html);
	}	
	
	//Pass youtube id or youtube url, and get the id
	var getYoutubeIDFromUrl = function(url){
		url = jQuery.trim(url);
		
		var video_id = url.split('v=')[1];
		if(video_id){
			var ampersandPosition = video_id.indexOf('&');
			if(ampersandPosition != -1) {
			  video_id = video_id.substring(0, ampersandPosition);
			}
		}else{
			video_id = url;
		}
		
		return(video_id);
	}
	
	//Get vimeo id from url
	var getVimeoIDFromUrl = function(url){
		url = jQuery.trim(url);
		
		var video_id = url.replace(/[^0-9]+/g, '');
		video_id = jQuery.trim(video_id);
		
		return(video_id);
	}	
	
	//Youtube callback script, set and store youtube data, and add it to dialog
	t.onYoutubeCallback = function(obj){
		jQuery("#youtube_loader").hide();
		var desc_small_size = 200;
		
		//Prepare data
		var entry = obj.entry;
		var data = {};
		data.id = jQuery("#youtube_id").val();
		data.id = jQuery.trim(data.id);
		data.video_type = "youtube";
		data.title = entry.title.$t;
		data.author = entry.author[0].name.$t;
		data.link = entry.link[0].href;
		data.description = entry.media$group.media$description.$t;
		data.desc_small = data.description;
		
		if(data.description.length > desc_small_size)
			data.desc_small = data.description.slice(0,desc_small_size)+"...";
		
		var thumbnails = entry.media$group.media$thumbnail;
		
		data.thumb_small = {url:thumbnails[0].url,width:thumbnails[0].width,height:thumbnails[0].height};
		data.thumb_medium = {url:thumbnails[1].url,width:thumbnails[1].width,height:thumbnails[1].height};
		data.thumb_big = {url:thumbnails[2].url,width:thumbnails[2].width,height:thumbnails[2].height};
		
		//Set html in dialog
		setYoutubeDialogHtml(data);
		
		//Set the youtube arguments
		var objArguments = jQuery("#input_video_arguments");
		if(objArguments.val() == "")
			objArguments.val(objArguments.data("youtube"));
		
		//Store last video data
		lastVideoData = data;
		
		//Show controls
		jQuery("#video_hidden_controls").show();
	}	
	
	//Vimeo callback script, set and store vimeo data, and add it to dialog
	t.onVimeoCallback = function(obj){
		jQuery("#vimeo_loader").hide();
		
		var desc_small_size = 200;
		obj = obj[0];
		
		var data = {};
		data.video_type = "vimeo";
		data.id = obj.id;
		data.id = jQuery.trim(data.id);
		data.title = obj.title;
		data.link = obj.url;
		data.author = obj.user_name;
		
		data.description = obj.description;
		if(data.description.length > desc_small_size)
			data.desc_small = data.description.slice(0,desc_small_size)+"...";
		
		data.thumb_large = {url:obj.thumbnail_large,width:640,height:360};
		data.thumb_medium = {url:obj.thumbnail_medium,width:200,height:150};
		data.thumb_small = {url:obj.thumbnail_small,width:100,height:75};
		
		//Set html in dialog
		setYoutubeDialogHtml(data);
		
		//Set the youtube arguments
		var objArguments = jQuery("#input_video_arguments");
		objArguments.val(objArguments.data("vimeo"));
		
		//Store last video data
		lastVideoData = data;
		
		//Show controls
		jQuery("#video_hidden_controls").show();
	}
	
	//Show error message on the dialog
	t.videoDialogOnError = function() {
		//If ok, don't do nothing
		if(jQuery("#video_hidden_controls").is(":visible"))
			return(false);
		
		//If error - show message
		jQuery("#youtube_loader").hide();
		var html = "<div class='video-content-error'>Video Not Found!</div>";
		jQuery("#video_content").html(html);
	}	
	
	//Update video size enabled disabled according fullwidth properties
	var updateVideoSizeProps = function() {
		var isFullwidth = jQuery("#input_video_fullwidth").is(":checked");
		if(isFullwidth==true){	
			//Disable
			jQuery("#video_size_wrapper").addClass("text-disabled");
			jQuery("#input_video_width, #input_video_height").addClass("input-disabled");
			
		}else{		
			//Enable
			jQuery("#video_size_wrapper").removeClass("text-disabled");
			jQuery("#input_video_width, #input_video_height").removeClass("input-disabled");
		}		
	}	
	
	//Open dialog for youtube or vimeo import , add / update
	t.openVideoDialog = function(callback,objCurrentVideoData) {		
		lastVideoCallback = callback;
		
		var dialogVideo = jQuery("#dialog_video");
		
		//Set buttons
		var buttons = {
			"Close":function() {
				dialogVideo.dialog("close");
			}
		};
		
		//clear the dialog content
		setYoutubeDialogHtml(false);
		
		//Enable fields
		jQuery("#video_type_chooser").show();
		jQuery("#youtube_id,#vimeo_id").prop("disabled","").removeClass("input-disabled");
		
		//Clear the fields
		jQuery("#input_video_arguments").val("");
		jQuery("#input_video_autoplay").prop("checked","");
		jQuery("#input_video_nextslide").prop("checked","");
		jQuery("#input_video_fullwidth").prop("checked","");
		
		jQuery("#youtube_id").val("");
		jQuery("#vimeo_id").val("");
		
		jQuery("#video_hidden_controls").hide();
				
		var buttonVideoAdd = jQuery("#button-video-add");
		buttonVideoAdd.text(buttonVideoAdd.data("textadd"));
		
		//Open the dialog
		dialogVideo.dialog({
				buttons:buttons,
				minWidth:830,
				minHeight:550,
				modal:true
		});
		
		//If update dialog open:		
		if(objCurrentVideoData)
			setVideoDialogUpdateMode(objCurrentVideoData);
		
		updateVideoSizeProps();
	}	
	
	//Prepare the dialog for video update
	var setVideoDialogUpdateMode = function(data) {		
		data.id = jQuery.trim(data.id);
		
		jQuery("#video_type_chooser").hide();
		
		//Disable fields
		jQuery("#youtube_id,#vimeo_id").prop("disabled","disabled").addClass("input-disabled");
		
		//Set mode and video id
		switch(data.video_type){
			case "youtube":
				jQuery("#video-dialog-wrap").removeClass("html5select");
				jQuery("#video_radio_youtube").trigger("click");			
				jQuery("#youtube_id").val(data.id);				
			break;
			case "vimeo":
				jQuery("#video-dialog-wrap").removeClass("html5select");
				jQuery("#video_radio_vimeo").trigger("click");
				jQuery("#vimeo_id").val(data.id);
			break;
			case "html5":
				jQuery("#video-dialog-wrap").addClass("html5select");
				jQuery("#html5_url_poster").val(data.urlPoster);
				jQuery("#html5_url_mp4").val(data.urlMp4);
				jQuery("#html5_url_webm").val(data.urlWebm);
				jQuery("#html5_url_ogv").val(data.urlOgv);
				jQuery("#video_radio_html5").trigger("click");
			break;
		}
		
		//Set width and height
		jQuery("#input_video_width").val(data.width);
		jQuery("#input_video_height").val(data.height);
		jQuery("#input_video_arguments").val(data.args);
		
		if(data.autoplay && data.autoplay == true)
			jQuery("#input_video_autoplay").prop("checked","checked");
		else
			jQuery("#input_video_autoplay").prop("checked","");
			
		if(data.nextslide && data.nextslide == true)
			jQuery("#input_video_nextslide").prop("checked","checked");
		else
			jQuery("#input_video_nextslide").prop("checked","");

		if(data.fullwidth && data.fullwidth == true)
			jQuery("#input_video_fullwidth").prop("checked","checked");
		else
			jQuery("#input_video_fullwidth").prop("checked","");
		
		//Change button text
		var buttonVideoAdd = jQuery("#button-video-add");
		buttonVideoAdd.text(buttonVideoAdd.data("textupdate"));
		
		//Search
		switch(data.video_type){
			case "youtube":
				jQuery("#button_youtube_search").trigger("click");
			break;
			case "vimeo":
				jQuery("#button_vimeo_search").trigger("click");
			break;
		}		
	}
	
	//Add params from textboxes to object
	var addTextboxParamsToObj = function(obj){
		obj.width = jQuery("#input_video_width").val();
		obj.height = jQuery("#input_video_height").val();
		obj.args = jQuery("#input_video_arguments").val();
		obj.autoplay = jQuery("#input_video_autoplay").is(":checked");
		obj.nextslide = jQuery("#input_video_nextslide").is(":checked");
		obj.fullwidth = jQuery("#input_video_fullwidth").is(":checked");
		return(obj);
	}	
	
	//Init video dialog buttons
	var initVideoDialog = function() {
		
		//Set youtube radio checked
		jQuery("#video_radio_youtube").prop("checked",true);
		
		//Set radio boxes
		jQuery("#video_radio_vimeo").click(function() {
			jQuery("#video_block_youtube").hide();
			jQuery("#video_block_html5").hide();
			jQuery("#video_hidden_controls").hide();
			jQuery("#video_content").show();
			jQuery("#video_block_vimeo").show();
		});
		
		jQuery("#video_radio_youtube").click(function() {
			jQuery("#video_block_vimeo").hide();
			jQuery("#video_block_html5").hide();			
			jQuery("#video_hidden_controls").hide();
			jQuery("#video_content").show();
			jQuery("#video_block_youtube").show();
		});
		
		jQuery("#video_radio_html5").click(function() {
			jQuery("#video_block_vimeo").hide();
			jQuery("#video_block_youtube").hide();
			jQuery("#video_block_html5").show();
			jQuery("#video_content").hide();
			jQuery("#video_hidden_controls").show();
		});
		
		//Set youtube search action
		jQuery("#button_youtube_search").click(function() {			
			//Init data
			setYoutubeDialogHtml(false);
			jQuery("#video_hidden_controls").hide();
			
			jQuery("#youtube_loader").show();
			var youtubeID = jQuery("#youtube_id").val();
			youtubeID = jQuery.trim(youtubeID);
			
			youtubeID = getYoutubeIDFromUrl(youtubeID);
			
			var urlAPI = "http://gdata.youtube.com/feeds/api/videos/"+youtubeID+"?v=2&alt=json-in-script&callback=UniteAdminBanner.onYoutubeCallback";
			
			jQuery.getScript(urlAPI);
			
			//Handle not found:
			setTimeout("UniteAdminBanner.videoDialogOnError()",2000);
		});
		
		
		//Add the selected video to the callback function
		jQuery("#button-video-add").click(function() {
			var html5Checked = jQuery("#video_radio_html5").prop("checked");
			
			if(html5Checked){	
				//In case of html5
				var obj = {};
				obj.video_type = "html5";
				obj.urlPoster = jQuery("#html5_url_poster").val();
				obj.urlMp4 = jQuery("#html5_url_mp4").val();
				obj.urlWebm = jQuery("#html5_url_webm").val();
				obj.urlOgv = jQuery("#html5_url_ogv").val();
				obj.width = jQuery("#input_video_width").val();
				obj.height = jQuery("#input_video_height").val();

				obj = addTextboxParamsToObj(obj);
				
				if(typeof lastVideoCallback == "function")
					lastVideoCallback(obj);
				
				jQuery("#dialog_video").dialog("close");				
			} else {		
				//In case of vimeo and youtube 
				if(!lastVideoData)
					return(false);
				
				lastVideoData = addTextboxParamsToObj(lastVideoData);
				
				if(typeof lastVideoCallback == "function")
					lastVideoCallback(lastVideoData);
				
				jQuery("#dialog_video").dialog("close");
			}			
		});		
		
		//Set vimeo search
		jQuery("#button_vimeo_search").click(function() {
			//Init data
			setYoutubeDialogHtml(false);
			jQuery("#video_hidden_controls").hide();
			
			jQuery("#vimeo_loader").show();
			
			var vimeoID = jQuery("#vimeo_id").val();
			vimeoID = jQuery.trim(vimeoID);
			vimeoID = getVimeoIDFromUrl(vimeoID);
			
			var urlAPI = 'http://www.vimeo.com/api/v2/video/' + vimeoID + '.json?callback=UniteAdminBanner.onVimeoCallback'; 
			jQuery.getScript(urlAPI);
		});		
		
		jQuery("#input_video_fullwidth").click(updateVideoSizeProps);
		
	}	
	
	//Init general settings dialog
	var initGeneralSettings = function() {		
		//Button general settings - open dialog
		jQuery("#button_general_settings").click(function() {			
			jQuery("#loader_general_settings").hide();			
			jQuery("#dialog_general_settings").dialog({
				minWidth:800,
				minHeight:500,
				modal:true
			});			
		});
		
		//Button save general settings
		jQuery("#button_save_general_settings").click(function() {
			var data = UniteSettingsBanner.getSettingsObject("form_general_settings");
			jQuery("#loader_general_settings").show();
			UniteAdminBanner.ajaxRequest("update_general_settings",data,function(response){
				jQuery("#loader_general_settings").hide();
				jQuery("#dialog_general_settings").dialog("close");
			});
		});		
	}	
	
	//Run the init function
	jQuery(document).ready(function() {
		initVideoDialog();
		
		//Init update dialog
		jQuery("#button_upload_plugin").click(function() {
			
			jQuery("#dialog_update_plugin").dialog({
				minWidth:600,
				minHeight:400,
				modal:true
			});
			
		});
		
		//Update text operation
		jQuery("#button_update_text").click(function() {
			UniteAdminBanner.ajaxRequest("update_text","",function(response){
				alert(response.message);
			});
		});
		
		initGeneralSettings();		
	});	
}

//User functions
function trace(data,clear){
	UniteAdminBanner.trace(data,clear);
}

function debug(data){
	UniteAdminBanner.debug(data);
}

