<?php
	class BannerRotatorAdmin extends UniteBaseAdminClassBanner {
		
		const DEFAULT_VIEW = "sliders";
		
		const VIEW_SLIDER = "slider";
		const VIEW_SLIDERS = "sliders";
		
		const VIEW_SLIDES = "slides";
		const VIEW_SLIDE = "slide";		
		
		//The constructor
		public function __construct($mainFilepath) {			
			parent::__construct($mainFilepath,$this,self::DEFAULT_VIEW);
			
			//Set table names
			GlobalsBannerRotator::$table_sliders = self::$table_prefix.GlobalsBannerRotator::TABLE_SLIDERS_NAME;
			GlobalsBannerRotator::$table_slides = self::$table_prefix.GlobalsBannerRotator::TABLE_SLIDES_NAME;
			GlobalsBannerRotator::$table_settings = self::$table_prefix.GlobalsBannerRotator::TABLE_SETTINGS_NAME;
			
			GlobalsBannerRotator::$filepath_captions = self::$path_plugin."css/caption.css";
			GlobalsBannerRotator::$filepath_captions_original = self::$path_plugin."css/caption-original.css";
			GlobalsBannerRotator::$urlCaptionsCSS = self::$url_plugin."css/caption.css";
			
			$this->init();
		}		
		
		//Init all actions
		private function init() {			
			$this->checkCopyCaptionsCSS();
			
			self::createDBTables();
			
			//Include general settings
			self::requireSettings("general_settings");
			
			//Set role
			$generalSettings = self::getSettings("general");
			$role = $generalSettings->getSettingValue("role",UniteBaseAdminClassBanner::ROLE_ADMIN);
			
			self::setMenuRole($role);
			
			self::addMenuPage('Banner Rotator', "adminPages", self::$url_plugin."images/icon.png");
			
			//Ajax response to save slider options.
			self::addActionAjax("ajax_action", "onAjaxAction");
		}
		
		
		//A must function. please don't remove it
		//Process activate event - install the db (with delta)
		public static function onActivate() {
			self::createDBTables();
		}
		
		//Create db tables 
		public static function createDBTables() {
			self::createTable(GlobalsBannerRotator::TABLE_SLIDERS_NAME);
			self::createTable(GlobalsBannerRotator::TABLE_SLIDES_NAME);
			self::createTable(GlobalsBannerRotator::TABLE_SETTINGS_NAME);
		}
		
		
		//If caption file don't exists - copy it from the original file.
		public static function checkCopyCaptionsCSS() {
			if(file_exists(GlobalsBannerRotator::$filepath_captions) == false)
				copy(GlobalsBannerRotator::$filepath_captions_original,GlobalsBannerRotator::$filepath_captions);
				
			if(!file_exists(GlobalsBannerRotator::$filepath_captions) == true) {
				self::setStartupError("Can't copy <b>caption-original.css </b> to <b>caption.css</b> in <b> plugins/bannerrotator/css </b> folder. Please try to copy the file by hand or turn to support.");
			}			
		}		
		
		//A must function. adds scripts on the page
		//Add all page scripts and styles here.
		//Please don't remove this function
		//Common scripts even if the plugin not load, use this function only if no choise.
		public static function onAddScripts() {
			self::addStyle("edit_layers","edit_layers");
			
			self::addScriptCommon("edit_layers","unite_layers");
			self::addScript("banner_admin");
			
			//Include all media upload scripts
			self::addMediaUploadIncludes();
			
			//Add Banner Rotator css
			self::addStyle("banner-rotator","banner-rotator","css");
			self::addStyle("caption","banner-rotator-caption","css");
		}		
		
		//Admin main page function
		public static function adminPages() {						
			parent::adminPages();
			
			//Require styles by view
			switch(self::$view) {
				case self::VIEW_SLIDERS:
				case self::VIEW_SLIDER:
					self::requireSettings("slider_settings");
				break;
				case self::VIEW_SLIDES:					
				break;
				case self::VIEW_SLIDE:
				break;
			}
			
			self::setMasterView("master_view");
			self::requireView(self::$view);
		}		
		
		//Craete tables
		public static function createTable($tableName) {
			global $wpdb;
						
			//If table exists - don't create it.
			$tableRealName = self::$table_prefix.$tableName;
			if(UniteFunctionsWPBanner::isDBTableExists($tableRealName))
				return(false);
			
			$charset_collate = '';
					
			if(method_exists($wpdb, "get_charset_collate"))
				$charset_collate = $wpdb->get_charset_collate();
			else{
				if ( ! empty($wpdb->charset) )
					$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
				if ( ! empty($wpdb->collate) )
					$charset_collate .= " COLLATE $wpdb->collate";
			}
				
			switch($tableName) {
				case GlobalsBannerRotator::TABLE_SLIDERS_NAME:					
				$sql = "CREATE TABLE " .self::$table_prefix.$tableName ." (
							  id int(9) NOT NULL AUTO_INCREMENT,
							  title tinytext NOT NULL,
							  alias tinytext,
							  params text NOT NULL,
							  PRIMARY KEY (id)
							)$charset_collate;";
				break;
				case GlobalsBannerRotator::TABLE_SLIDES_NAME:
					$sql = "CREATE TABLE " .self::$table_prefix.$tableName ." (
								  id int(9) NOT NULL AUTO_INCREMENT,
								  slider_id int(9) NOT NULL,
								  slide_order int not NULL,	
								  params text NOT NULL,
								  layers text NOT NULL,
								  PRIMARY KEY (id)
								)$charset_collate;";
				break;
				case GlobalsBannerRotator::TABLE_SETTINGS_NAME:
					$sql = "CREATE TABLE " .self::$table_prefix.$tableName ." (
								  id int(9) NOT NULL AUTO_INCREMENT,
								  general TEXT NOT NULL,
								  params TEXT NOT NULL,
								  PRIMARY KEY (id)
								)$charset_collate;";
				break;
				
				default:
					UniteFunctionsBanner::throwError("table: $tableName not found");
				break;
			}
			
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);
		}
		
		//Import slideer handle (not ajax response)
		private static function importSliderHandle($viewBack = null) {
			
			dmp(__("importing slider setings and data...",BANNERROTATOR_TEXTDOMAIN));
			
			$slider = new BannerRotator();
			$response = $slider->importSliderFromPost();
			$sliderID = $response["sliderID"];
			
			if(empty($viewBack)) {
				$viewBack = self::getViewUrl(self::VIEW_SLIDER,"id=".$sliderID);
				if(empty($sliderID))
					$viewBack = self::getViewUrl(self::VIEW_SLIDERS);
			}
			
			//Handle error
			if($response["success"] == false) {
				$message = $response["error"];
				dmp("<b>Error: ".$message."</b>");
				echo UniteFunctionsBanner::getHtmlLink($viewBack, __("Go Back",BANNERROTATOR_TEXTDOMAIN));
			}
			else{	//Handle success, js redirect
				dmp(__("Slider Import Success, redirecting...",BANNERROTATOR_TEXTDOMAIN));
				echo "<script>location.href='$viewBack'</script>"; 
			}
			exit();
		}		
		
		//onAjax action handler
		public static function onAjaxAction() {
			
			$slider = new BannerRotator();
			$slide = new BannerSlide();
			$operations = new BannerOperations();
			
			$action = self::getPostGetVar("client_action");
			$data = self::getPostGetVar("data");
			$nonce = self::getPostGetVar("nonce");
			
			try {
				//Verify the nonce
				$isVerified = wp_verify_nonce($nonce, "bannerrotator_actions");
				
				if($isVerified == false)
					UniteFunctionsBanner::throwError("Wrong request");
								
				switch($action) {
					case "export_slider":
						$sliderID = self::getGetVar("sliderid");
						$dummy = self::getGetVar("dummy");
						$slider->initByID($sliderID);
						$slider->exportSlider($dummy);
						break;
					case "import_slider":
						self::importSliderHandle();
						break;
					case "import_slider_slidersview":
						$viewBack = self::getViewUrl(self::VIEW_SLIDERS);
						self::importSliderHandle($viewBack);
						break;
					case "create_slider":
						$newSliderID = $slider->createSliderFromOptions($data);
						
						self::ajaxResponseSuccessRedirect(
						            __("The slider successfully created",BANNERROTATOR_TEXTDOMAIN), 
									self::getViewUrl("sliders"));
						
						break;
					case "update_slider":
						$slider->updateSliderFromOptions($data);
						self::ajaxResponseSuccess(__("Slider updated",BANNERROTATOR_TEXTDOMAIN));
						break;					
					case "delete_slider":						
						$slider->deleteSliderFromData($data);
						
						self::ajaxResponseSuccessRedirect(
						            __("The slider deleted",BANNERROTATOR_TEXTDOMAIN), 
									self::getViewUrl(self::VIEW_SLIDERS));
						break;
					case "duplicate_slider":
						
						$slider->duplicateSliderFromData($data);
						
						self::ajaxResponseSuccessRedirect(
						            __("The duplicate successfully, refreshing page...",BANNERROTATOR_TEXTDOMAIN), 
									self::getViewUrl(self::VIEW_SLIDERS));
						break;
					case "add_slide":
						$numSlides = $slider->createSlideFromData($data);
						$sliderID = $data["sliderid"];
						
						if($numSlides == 1) {
							$responseText = __("Slide Created",BANNERROTATOR_TEXTDOMAIN);
						}
						else
							$responseText = $numSlides . " ".__("Slides Created",BANNERROTATOR_TEXTDOMAIN);
						
						$urlRedirect = self::getViewUrl(self::VIEW_SLIDES,"id=$sliderID");													
						self::ajaxResponseSuccessRedirect($responseText,$urlRedirect);						
						break;
					case "add_slide_fromslideview":
						$slideID = $slider->createSlideFromData($data,true);
						$urlRedirect = self::getViewUrl(self::VIEW_SLIDE,"id=$slideID");
						$responseText = __("Slide Created, redirecting...",BANNERROTATOR_TEXTDOMAIN);																			
						self::ajaxResponseSuccessRedirect($responseText,$urlRedirect);
						break;
					case "update_slide":
						$slide->updateSlideFromData($data);
						self::ajaxResponseSuccess(__("Slide updated",BANNERROTATOR_TEXTDOMAIN));
						break;
					case "delete_slide":
						$slide->deleteSlideFromData($data);
						$sliderID = UniteFunctionsBanner::getVal($data, "sliderID");
						self::ajaxResponseSuccessRedirect(
						            __("Slide Deleted Successfully",BANNERROTATOR_TEXTDOMAIN), 
									self::getViewUrl(self::VIEW_SLIDES,"id=$sliderID"));					
						break;
					case "duplicate_slide":
						$sliderID = $slider->duplicateSlideFromData($data);
						self::ajaxResponseSuccessRedirect(
						            __("Slide Duplicated Successfully",BANNERROTATOR_TEXTDOMAIN), 
									self::getViewUrl(self::VIEW_SLIDES,"id=$sliderID"));
						break;
					case "copy_move_slide":
						$sliderID = $slider->copyMoveSlideFromData($data);
						
						self::ajaxResponseSuccessRedirect(
						            __("The operation successfully, refreshing page...",BANNERROTATOR_TEXTDOMAIN), 
									self::getViewUrl(self::VIEW_SLIDES,"id=$sliderID"));
						break;
					case "get_captions_css":
						$contentCSS = $operations->getCaptionsContent();
						self::ajaxResponseData($contentCSS);
						break;
					case "update_captions_css":
						$arrCaptions = $operations->updateCaptionsContentData($data);
						self::ajaxResponseSuccess(__("CSS file saved succesfully!",BANNERROTATOR_TEXTDOMAIN),array("arrCaptions"=>$arrCaptions));
						break;
					case "restore_captions_css":
						$operations->restoreCaptionsCss();
						$contentCSS = $operations->getCaptionsContent();
						self::ajaxResponseData($contentCSS);
						break;
					case "update_slides_order":
						$slider->updateSlidesOrderFromData($data);
						self::ajaxResponseSuccess(__("Order updated successfully",BANNERROTATOR_TEXTDOMAIN));
						break;
					case "change_slide_image":
						$slide->updateSlideImageFromData($data);
						$sliderID = UniteFunctionsBanner::getVal($data, "slider_id");						
						self::ajaxResponseSuccessRedirect(
						            __("Slide Changed Successfully",BANNERROTATOR_TEXTDOMAIN), 
									self::getViewUrl(self::VIEW_SLIDES,"id=$sliderID"));
						break;	
					case "preview_slide":
						$operations->putSlidePreviewByData($data);
						break;
					case "preview_slider":
						$sliderID = UniteFunctionsBanner::getPostVariable("sliderid");
						$operations->previewOutput($sliderID);
						break;
					case "toggle_slide_state":
						$currentState = $slide->toggleSlideStateFromData($data);
						self::ajaxResponseData(array("state"=>$currentState));
						break;
					case "slide_lang_operation":
						$responseData = $slide->doSlideLangOperation($data);
						self::ajaxResponseData($responseData);
						break;
					case "update_plugin":
						self::updatePlugin(self::DEFAULT_VIEW);
						break;
					case "update_text":
						self::updateSettingsText();
						self::ajaxResponseSuccess("All files successfully updated");
						break;
					case "update_general_settings":
						$operations->updateGeneralSettings($data);
						self::ajaxResponseSuccess(__("General settings updated"));
						break;
					default:
						self::ajaxResponseError("wrong ajax action: $action ");
						break;
				}
				
			} catch(Exception $e) {
				$message = $e->getMessage();				
				self::ajaxResponseError($message);
			}
			
			//It's an ajax action, so exit
			self::ajaxResponseError("No response output on <b> $action </b> action. please check with the developer.");
			exit();
		}
		
	}	
?>