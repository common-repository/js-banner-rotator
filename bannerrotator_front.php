<?php
	class BannerRotatorFront extends UniteBaseFrontClassBanner {		
		
		//Constructor
		public function __construct($mainFilepath) {			
			parent::__construct($mainFilepath,$this);
			
			//Set table names
			GlobalsBannerRotator::$table_sliders = self::$table_prefix.GlobalsBannerRotator::TABLE_SLIDERS_NAME;
			GlobalsBannerRotator::$table_slides = self::$table_prefix.GlobalsBannerRotator::TABLE_SLIDES_NAME;
			GlobalsBannerRotator::$table_settings = self::$table_prefix.GlobalsBannerRotator::TABLE_SETTINGS_NAME;
		}		
		
		//Add scripts and styles	
		public static function onAddScripts() {			
			$operations = new BannerOperations();
			$arrValues = $operations->getGeneralSettingsValues();
			
			$includesGlobally = UniteFunctionsBanner::getVal($arrValues, "includes_globally","on");
			$strPutIn = UniteFunctionsBanner::getVal($arrValues, "pages_for_includes");
			$isPutIn = BannerRotatorOutput::isPutIn($strPutIn,true);
			
			//Put the includes only on pages with active widget or shortcode
			//If the put in match, then include them always (ignore this if)			
			if($isPutIn == false && $includesGlobally == "off") {
				$isWidgetActive = is_active_widget(false, false, "banner-rotator-widget", true);
				$hasShortcode = UniteFunctionsWPBanner::hasShortcode("banner_rotator");
				
				if($isWidgetActive == false && $hasShortcode == false) {
					return(false);
				}
			}
			
			//Banner Rotator CSS settings
			self::addStyle("banner-rotator","banner-rotator","css");
			self::addStyle("caption","banner-rotator-caption","css");

			//Banner Rotator JS
			self::addScript("jquery.flashblue-plugins","js","flashblue.plugins");
			self::addScript("jquery.banner-rotator","js");
		}
		
	}
?>