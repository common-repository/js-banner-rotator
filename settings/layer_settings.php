<?php 
	$operations = new BannerOperations();

	//Set Layer settings	
	$contentCSS = $operations->getCaptionsContent();
	$arrAnimations = $operations->getArrAnimations();
	$arrEndAnimations = $operations->getArrEndAnimations();
	
	$htmlButtonDown = '<div id="layer_captions_down" class="ui-state-default ui-corner-all"><span class="ui-icon ui-icon-arrowthick-1-s"></span></div>';
	$buttonEditStyles = UniteFunctionsBanner::getHtmlLink("javascript:void(0)", "<i class='icon-magic'></i>Edit CSS File","button_edit_css","button-primary btn-blue");
	$arrEasing = $operations->getArrEasing();
	$arrEndEasing = $operations->getArrEndEasing();
	
	$captionsAddonHtml = $htmlButtonDown.$buttonEditStyles;
	
	//Set Layer settings
	$layerSettings = new UniteSettingsAdvancedBanner();
	$layerSettings->addSection(__("Layer Params",BANNERROTATOR_TEXTDOMAIN),__("layer_params",BANNERROTATOR_TEXTDOMAIN));
	$layerSettings->addSap(__("Layer Params",BANNERROTATOR_TEXTDOMAIN),__("layer_params"));
	$layerSettings->addTextBox(__("layer_caption"), __("caption_green"), __("Style",BANNERROTATOR_TEXTDOMAIN),array(UniteSettingsBanner::PARAM_ADDTEXT=>$captionsAddonHtml,"class"=>"textbox-caption"));
	
	$addHtmlTextarea =  UniteFunctionsBanner::getHtmlLink("javascript:void(0)", "insert button","linkInsertButton","disabled");
	
	$layerSettings->addTextArea("layer_text", "",__("Text / Html",BANNERROTATOR_TEXTDOMAIN),array("class"=>"area-layer-params",UniteSettingsBanner::PARAM_ADDTEXT_BEFORE_ELEMENT=>$addHtmlTextarea));
	$layerSettings->addTextBox("layer_image_link", "",__("Image Link",BANNERROTATOR_TEXTDOMAIN),array("class"=>"text-sidebar-link","hidden"=>true));
	$layerSettings->addSelect("layer_link_open_in",array("same"=>__("Same Window",BANNERROTATOR_TEXTDOMAIN),"new"=>__("New Window",BANNERROTATOR_TEXTDOMAIN)),__("Link Open In",BANNERROTATOR_TEXTDOMAIN),"same",array("hidden"=>true));
		
	$layerSettings->addSelect("layer_animation",$arrAnimations,__("Animation",BANNERROTATOR_TEXTDOMAIN),"fade");	
	$layerSettings->addSelect("layer_easing", $arrEasing, __("Easing",BANNERROTATOR_TEXTDOMAIN),"easeOutExpo");
	$params = array("unit"=>__("ms",BANNERROTATOR_TEXTDOMAIN));
	$layerSettings->addTextBox("layer_speed", "","Speed",$params);
	$layerSettings->addCheckbox("layer_hidden", false,__("Hide Under Width",BANNERROTATOR_TEXTDOMAIN));
	
	//Put left top
	$textOffsetX = __("OffsetX",BANNERROTATOR_TEXTDOMAIN);
	$textX = __("X",BANNERROTATOR_TEXTDOMAIN);
	$params = array("attrib_text"=>"data-textoffset='{$textOffsetX}' data-textnormal='{$textX}'");	
	$layerSettings->addTextBox("layer_left", "",__("X",BANNERROTATOR_TEXTDOMAIN),$params);
	
	$textOffsetY = __("OffsetY",BANNERROTATOR_TEXTDOMAIN);
	$textY = __("Y",BANNERROTATOR_TEXTDOMAIN);	
	$params = array("attrib_text"=>"data-textoffset='{$textOffsetY}' data-textnormal='{$textY}'");
	$layerSettings->addTextBox("layer_top", "",__("Y",BANNERROTATOR_TEXTDOMAIN),$params);
	
	$layerSettings->addTextBox("layer_align_hor", "left","Hor Align",array("hidden"=>true));
	$layerSettings->addTextBox("layer_align_vert", "top","Vert Align",array("hidden"=>true));
	
	$layerSettings->addSelect("layer_slide_link", $arrSlideLinkLayers, __("Link To Slide",BANNERROTATOR_TEXTDOMAIN),"nothing");
	
	$params = array("unit"=>__("px",BANNERROTATOR_TEXTDOMAIN),"hidden"=>true);
	$layerSettings->addTextBox("layer_scrolloffset", "0",__("Scroll Under Slider Offset",BANNERROTATOR_TEXTDOMAIN),$params);
	
	$layerSettings->addButton("button_edit_video", __("Edit Video",BANNERROTATOR_TEXTDOMAIN),array("hidden"=>true,"class"=>"button-primary btn-blue"));
	$layerSettings->addButton("button_change_image_source", __("Change Image Source",BANNERROTATOR_TEXTDOMAIN),array("hidden"=>true,"class"=>"button-primary btn-blue"));
	
	$params = array("unit"=>__("ms",BANNERROTATOR_TEXTDOMAIN));
	$layerSettings->addTextBox("layer_endtime", "",__("End Time",BANNERROTATOR_TEXTDOMAIN),$params);
	$layerSettings->addTextBox("layer_endspeed", "",__("End Speed",BANNERROTATOR_TEXTDOMAIN),$params);
	$layerSettings->addSelect("layer_endanimation",$arrEndAnimations,__("Animation",BANNERROTATOR_TEXTDOMAIN),"auto");
	$layerSettings->addSelect("layer_endeasing", $arrEndEasing, __("Easing",BANNERROTATOR_TEXTDOMAIN),"nothing");
	$params = array("unit"=>__("ms",BANNERROTATOR_TEXTDOMAIN));
	
	//Advanced params
	$arrCorners = array("nothing"=>__("No Corner",BANNERROTATOR_TEXTDOMAIN),
						"curved"=>__("Sharp",BANNERROTATOR_TEXTDOMAIN),
						"reverced"=>__("Sharp Reversed",BANNERROTATOR_TEXTDOMAIN));	
	$params = array();
	$layerSettings->addSelect("layer_cornerleft", $arrCorners, __("Left Corner",BANNERROTATOR_TEXTDOMAIN),"nothing",$params);
	$layerSettings->addSelect("layer_cornerright", $arrCorners, __("Right Corner",BANNERROTATOR_TEXTDOMAIN),"nothing",$params);
	$layerSettings->addCheckbox("layer_resizeme", false,__("Responsive Through All Levels",BANNERROTATOR_TEXTDOMAIN),$params);
	
	self::storeSettings("layer_settings",$layerSettings);
	
	//Store settings of content css for editing on the client.
	self::storeSettings("css_captions_content",$contentCSS);	
?>