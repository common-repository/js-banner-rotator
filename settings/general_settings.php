<?php
	$generalSettings = new UniteSettingsBanner();
	
	$generalSettings->addSelect("role", 
								array(UniteBaseAdminClassBanner::ROLE_ADMIN => __("To Admin",BANNERROTATOR_TEXTDOMAIN),
									  UniteBaseAdminClassBanner::ROLE_EDITOR =>__("To Editor, Admin",BANNERROTATOR_TEXTDOMAIN),
									  UniteBaseAdminClassBanner::ROLE_AUTHOR =>__("Author, Editor, Admin",BANNERROTATOR_TEXTDOMAIN)),									  
									  __("View Plugin Permission",BANNERROTATOR_TEXTDOMAIN), 
									  UniteBaseAdminClassBanner::ROLE_ADMIN, 
									  array("description"=>"<br>".__("The role of user that can view and edit the plugin",BANNERROTATOR_TEXTDOMAIN)));

	$generalSettings->addRadio("includes_globally", 
							   array("on"=>__("On",BANNERROTATOR_TEXTDOMAIN),"off"=>__("Off",BANNERROTATOR_TEXTDOMAIN)),
							   __("Include BannerRotator libraries globally",BANNERROTATOR_TEXTDOMAIN),
							   "on",
							   array("description"=>"<br>".__("Add css and js includes only on all pages. Id turned to off they will added to pages where the banner_rotator shortcode exists only. This will work only when the slider added by a shortcode.",BANNERROTATOR_TEXTDOMAIN)));
	
	$generalSettings->addTextBox("pages_for_includes", "",__("Pages to include BannerRotator libraries",BANNERROTATOR_TEXTDOMAIN),
								  array("description"=>"<br>".__("Specify the page id's that the front end includes will be included in. Example: 2,3,5 also: homepage,3,4",BANNERROTATOR_TEXTDOMAIN)));
									  
	//Get stored values
	$operations = new BannerOperations();
	$arrValues = $operations->getGeneralSettingsValues();
	$generalSettings->setStoredValues($arrValues);
	
	self::storeSettings("general", $generalSettings);
?>