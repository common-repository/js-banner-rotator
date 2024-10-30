<?php
	$settingsMain = self::getSettings("slider_main");
	$settingsParams = self::getSettings("slider_params");

	$settingsSliderMain = new BannerRotatorSettingsProduct();
	$settingsSliderParams = new UniteSettingsProductSidebarBanner();
	
	//Check existing slider data
	$sliderID = self::getGetVar("id");
	
	if(!empty($sliderID)) {
		$slider = new BannerRotator();
		$slider->initByID($sliderID);
		
		//Get setting fields
		$settingsFields = $slider->getSettingsFields();
		$arrFieldsMain = $settingsFields["main"];
		$arrFieldsParams = $settingsFields["params"];		

		//Set setting values from the slider
		$settingsMain->setStoredValues($arrFieldsParams);
		
		//Set custom type params values:
		$settingsMain = BannerRotatorSettingsProduct::setSettingsCustomValues($settingsMain, $arrFieldsParams);		
		
		$settingsParams->setStoredValues($arrFieldsParams);
		
		//Update short code setting
		$shortcode = $slider->getShortcode();
		$settingsMain->updateSettingValue("shortcode",$shortcode);
		
		$linksEditSlides = self::getViewUrl(BannerRotatorAdmin::VIEW_SLIDES,"id=$sliderID");
		
		$settingsSliderParams->init($settingsParams);	
		$settingsSliderMain->init($settingsMain);
		
		$settingsSliderParams->isAccordion(true);
		
		require self::getPathTemplate("slider_edit");
	} else {		
		$settingsSliderParams->init($settingsParams);	
		$settingsSliderMain->init($settingsMain);
		
		$settingsSliderParams->isAccordion(true);
		
		require self::getPathTemplate("slider_new");		
	}	
?>


	