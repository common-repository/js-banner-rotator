<?php
	//Get input
	$slideID = UniteFunctionsBanner::getGetVar("id");
	
	//Init slide object
	$slide = new BannerSlide();
	$slide->initByID($slideID);
	$slideParams = $slide->getParams();
	
	$operations = new BannerOperations();
	
	//Init slider object
	$sliderID = $slide->getSliderID();
	$slider = new BannerRotator();
	$slider->initByID($sliderID);
	$sliderParams = $slider->getParams();
	
	$arrSlideNames = $slider->getArrSlideNames();
	
	//Set slide delay
	$sliderDelay = $slider->getParam("delay","9000");
	$slideDelay = $slide->getParam("delay","");
	if(empty($slideDelay))
		$slideDelay = $sliderDelay;
	
	require self::getSettingsFilePath("slide_settings");
	require self::getSettingsFilePath("layer_settings");
	
	$settingsLayerOutput = new UniteSettingsProductSidebarBanner();
	$settingsSlideOutput = new UniteSettingsBannerProductBanner();
		
	$arrLayers = $slide->getLayers();
	
	//Get settings objects
	$settingsLayer = self::getSettings("layer_settings");	
	$settingsSlide = self::getSettings("slide_settings");
	
	$cssContent = self::getSettings("css_captions_content");
	$arrCaptionClasses = $operations->getArrCaptionClasses($cssContent);
	
	$arrButtonClasses = $operations->getButtonClasses();
	
	//Set layer caption as first caption class
	$firstCaption = !empty($arrCaptionClasses)?$arrCaptionClasses[0]:"";
	$settingsLayer->updateSettingValue("layer_caption",$firstCaption);
	
	//Set stored values from "slide params"
	$settingsSlide->setStoredValues($slideParams);
		
	//Init the settings output object
	$settingsLayerOutput->init($settingsLayer);
	$settingsSlideOutput->init($settingsSlide);
	
	//Set various parameters needed for the page
	$width = $sliderParams["width"];
	$height = $sliderParams["height"];
	$imageUrl = $slide->getImageUrl();
	$imageID = $slide->getImageID();
	
	$imageFilename = $slide->getImageFilename();
	$urlCaptionsCSS = GlobalsBannerRotator::$urlCaptionsCSS;
	
	$style = "width:{$width}px;height:{$height}px;";
	
	//Set iframe parameters
	$iframeWidth = $width+60;
	$iframeHeight = $height+50;
	
	$iframeStyle = "width:{$iframeWidth}px;height:{$iframeHeight}px;";
	
	$closeUrl = self::getViewUrl(BannerRotatorAdmin::VIEW_SLIDES,"id=".$sliderID);
	
	$jsonLayers = UniteFunctionsBanner::jsonEncodeForClientSide($arrLayers);
	$jsonCaptions = UniteFunctionsBanner::jsonEncodeForClientSide($arrCaptionClasses);
	
	$loadGoogleFont = $slider->getParam("loadGoogleFont","false");
	
	//Bg type params
	$bgType = UniteFunctionsBanner::getVal($slideParams, "background_type","image");
	$slideBGColor = UniteFunctionsBanner::getVal($slideParams, "slide_bg_color","#E7E7E7");
	$divLayersClass = "slide_layers";
	$bgSolidPickerProps = 'class="inputColorPicker slide_bg_color disabled" disabled="disabled"';
	
	switch($bgType) {
		case "trans":
			$divLayersClass = "slide_layers trans_bg";
		break;
		case "solid":
			$style .= "background-color:{$slideBGColor};";
			$bgSolidPickerProps = 'class="inputColorPicker slide_bg_color" style="background-color:'.$slideBGColor.'"';
		break;
		case "image":
			$style .= "background-image:url('{$imageUrl}');";
		break;
	}
	
	$slideTitle = $slide->getParam("title","Slide");
	$slideOrder = $slide->getOrder();
	
	//Treat multilanguage
	$isWpmlExists = UniteWpmlBanner::isWpmlExists();
	$useWpml = $slider->getParam("useWpml","false");
	$wpmlActive = false;
	if($isWpmlExists && $useWpml=="true") {
		$wpmlActive = true;
		$parentSlide = $slide->getParentSlide();
		$arrChildLangs = $parentSlide->getArrChildrenLangs();
	}
	
	require self::getPathTemplate("slide");
?>
	
