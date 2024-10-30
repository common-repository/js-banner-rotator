<?php	
	//Set "slider_main" settings
	$sliderMainSettings = new UniteSettingsAdvancedBanner();	
	
	$sliderMainSettings->addTextBox("title", "",__("Slider Title",BANNERROTATOR_TEXTDOMAIN),array("description"=>__("The title of the slider. Example: Slider1",BANNERROTATOR_TEXTDOMAIN),"required"=>"true"));	
	$sliderMainSettings->addTextBox("alias", "",__("Slider Alias",BANNERROTATOR_TEXTDOMAIN),array("description"=>__("The alias that used for embedding the slider. Example: slider1",BANNERROTATOR_TEXTDOMAIN),"required"=>"true"));
	$sliderMainSettings->addTextBox("shortcode", "",__("Slider Shortcode",BANNERROTATOR_TEXTDOMAIN), array("readonly"=>true,"class"=>"code"));
	$sliderMainSettings->addHr();	
	
	//Set slider type / texts
	$sliderMainSettings->addRadio("sliderType", array(
		"responsitive"=>__("Custom",BANNERROTATOR_TEXTDOMAIN),
		"fullwidth"=>__("Responsive",BANNERROTATOR_TEXTDOMAIN),
		"fullscreen"=>__("Full Screen",BANNERROTATOR_TEXTDOMAIN),
		"fixed"=>__("Fixed",BANNERROTATOR_TEXTDOMAIN)
		),
	__("Slider Layout",BANNERROTATOR_TEXTDOMAIN),		
	"fullwidth");

	$arrParams = array("class"=>"regular-text","description"=>__("Example: #header or .header, .footer, #somecontainer | The height of fullscreen slider will be decreased with the height of these Containers to fit perfect in the screen",BANNERROTATOR_TEXTDOMAIN));
	$sliderMainSettings->addTextBox("fullScreenOffsetContainer", "",__("Offset Containers",BANNERROTATOR_TEXTDOMAIN), $arrParams);

	$sliderMainSettings->addControl("sliderType", "fullScreenOffsetContainer", UniteSettingsBanner::CONTROL_TYPE_SHOW, "fullscreen");
	
	$paramsSize = array("width"=>1170,"height"=>500);	
	$sliderMainSettings->addCustom("sliderSize", "sliderSize","",__("Grid Settings",BANNERROTATOR_TEXTDOMAIN),$paramsSize);
	
	$paramsResponsitive = array("w1"=>1300,"sw1"=>960,"w2"=>1100,"sw2"=>760,"w3"=>768,"sw3"=>480,"w4"=>480,"sw4"=>320);
	$sliderMainSettings->addCustom("responsitiveSettings", "responsitive","",__("Responsive Sizes"),$paramsResponsitive);
	$sliderMainSettings->addHr();
	
	self::storeSettings("slider_main",$sliderMainSettings);
	
	//Set "slider_params" settings
	$sliderParamsSettings = new UniteSettingsAdvancedBanner();	
	$sliderParamsSettings->loadXMLFile(self::$path_settings."/slider_settings.xml");
	
	//Store params
	self::storeSettings("slider_params",$sliderParamsSettings); 
?>