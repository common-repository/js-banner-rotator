<?php	
	//Set Slide settings
	$arrTransitions = $operations->getArrTransition();
	
	$arrSlideNames = $slider->getArrSlideNames();
	
	$slideSettings = new UniteSettingsAdvancedBanner();

	//Title
	$params = array("description"=>__("The title of the slide, will be shown in the slides list.",BANNERROTATOR_TEXTDOMAIN),"class"=>"medium-text");
	$slideSettings->addTextBox("title",__("Slide",BANNERROTATOR_TEXTDOMAIN),__("Slide Title",BANNERROTATOR_TEXTDOMAIN), $params);

	//State
	$params = array("description"=>__("The state of the slide. The unpublished slide will be excluded from the slider.",BANNERROTATOR_TEXTDOMAIN));
	$slideSettings->addSelect("state",array("published"=>__("Published",BANNERROTATOR_TEXTDOMAIN),"unpublished"=>__("Unpublished",BANNERROTATOR_TEXTDOMAIN)),__("State",BANNERROTATOR_TEXTDOMAIN),"published",$params);
	
	//Visible from
	$params = array("description"=>__("If set, slide will be visible after the date is reached",BANNERROTATOR_TEXTDOMAIN));
	$slideSettings->addDatePicker("date_from","",__("Visible from",BANNERROTATOR_TEXTDOMAIN), $params);
	
	//Visible until
	$params = array("description"=>__("If set, slide will be visible till the date is reached",BANNERROTATOR_TEXTDOMAIN));
	$slideSettings->addDatePicker("date_to","",__("Visible until",BANNERROTATOR_TEXTDOMAIN), $params);
	
	$slideSettings->addHr("");	
	
	//WPML
	$isWpmlExists = UniteWpmlBanner::isWpmlExists();
	$useWpml = $slider->getParam("useWpml","false");
	
	if($isWpmlExists && $useWpml=="true"){
		$arrLangs = UniteWpmlBanner::getArrLanguages();
		$params = array("description"=>__("The language of the slide (uses WPML plugin).",BANNERROTATOR_TEXTDOMAIN));
		$slideSettings->addSelect("lang",$arrLangs,__("Language",BANNERROTATOR_TEXTDOMAIN),"all",$params);
	}
	
	//Transition
	$params = array("description"=>"The appearance transition of this slide.");
	$slideSettings->addSelect("slide_transition",$arrTransitions,"Transition","random",$params);
	
	//Delay	
	$params = array("description"=>__("A new delay value for the Slide. If no delay defined per slide, the delay defined via Options (",BANNERROTATOR_TEXTDOMAIN). $sliderDelay .__("ms) will be used",BANNERROTATOR_TEXTDOMAIN)
		,"class"=>"small"
	);
	$slideSettings->addTextBox("delay","",__("Delay",BANNERROTATOR_TEXTDOMAIN), $params);
	
	//Enable link
	$slideSettings->addSelect_boolean("enable_link", __("Enable Link",BANNERROTATOR_TEXTDOMAIN), false, __("Enable",BANNERROTATOR_TEXTDOMAIN),__("Disable",BANNERROTATOR_TEXTDOMAIN));
	
	$slideSettings->startBulkControl("enable_link", UniteSettingsBanner::CONTROL_TYPE_SHOW, "true");
	
	//Link type
	$slideSettings->addRadio("link_type", array("regular"=>__("Regular",BANNERROTATOR_TEXTDOMAIN),"slide"=>__("To Slide",BANNERROTATOR_TEXTDOMAIN)), __("Link Type",BANNERROTATOR_TEXTDOMAIN),"regular");
	
	//Link	
	$params = array("description"=>__("A link on the whole slide pic",BANNERROTATOR_TEXTDOMAIN));
	$slideSettings->addTextBox("link","",__("Slide Link",BANNERROTATOR_TEXTDOMAIN), $params);
	
	//Link target
	$params = array("description"=>__("The target of the slide link",BANNERROTATOR_TEXTDOMAIN));
	$slideSettings->addSelect("link_open_in",array("same"=>__("Same Window",BANNERROTATOR_TEXTDOMAIN),"new"=>__("New Window")),__("Link Open In",BANNERROTATOR_TEXTDOMAIN),"same",$params);
	
	//Slide link
	$arrSlideLink = array();
	$arrSlideLink["nothing"] = __("-- Not Chosen --",BANNERROTATOR_TEXTDOMAIN);
	$arrSlideLink["next"] = __("-- Next Slide --",BANNERROTATOR_TEXTDOMAIN);
	$arrSlideLink["prev"] = __("-- Previous Slide --",BANNERROTATOR_TEXTDOMAIN);
	
	$arrSlideLinkLayers = $arrSlideLink;
	$arrSlideLinkLayers["scroll_under"] = __("-- Scroll Below Slider --");
	
	foreach($arrSlideNames as $slideNameID=>$slideName) {		
		$name = $slideName["name"];		
		$arrSlideLink[$slideNameID] = $name;
		$arrSlideLinkLayers[$slideNameID] = $name;
	}
	
	$slideSettings->addSelect("slide_link", $arrSlideLink, __("Link To Slide",BANNERROTATOR_TEXTDOMAIN),"nothing");
	
	$params = array("description"=>"The position of the link related to layers");
	$slideSettings->addRadio("link_pos", array("front"=>"Front","back"=>"Back"), "Link Position","front",$params);
	
	$slideSettings->addHr("link_sap");
		
	$slideSettings->endBulkControl();
		
	$slideSettings->addControl("link_type", "slide_link", UniteSettingsBanner::CONTROL_TYPE_ENABLE, "slide");
	$slideSettings->addControl("link_type", "link", UniteSettingsBanner::CONTROL_TYPE_DISABLE, "slide");
	$slideSettings->addControl("link_type", "link_open_in", UniteSettingsBanner::CONTROL_TYPE_DISABLE, "slide");
		
	//Full width centering
	$params = array("description"=>__("Apply to full width mode only. Centering vertically slide images.",BANNERROTATOR_TEXTDOMAIN));
	$slideSettings->addCheckbox("fullwidth_centering", false, __("Full Width Centering",BANNERROTATOR_TEXTDOMAIN), $params);
	
	$slideSettings->addHr("");
	
	//Thumbnail
	$params = array("description"=>__("Slide Thumbnail. If not set - it will be taken from the slide image.",BANNERROTATOR_TEXTDOMAIN));
	$slideSettings->addImage("slide_thumb", "",__("Thumbnail",BANNERROTATOR_TEXTDOMAIN), $params);
	
	//Add background type (hidden)
	$slideSettings->addTextBox("background_type","image",__("Background Type",BANNERROTATOR_TEXTDOMAIN), array("hidden"=>true));
	
	//Store settings
	self::storeSettings("slide_settings",$slideSettings);
?>
