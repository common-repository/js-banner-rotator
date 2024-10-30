<?php 
 	class UniteBaseAdminClassBanner extends UniteBaseClassBanner {
 	
		const ACTION_ADMIN_MENU = "admin_menu";
		const ACTION_ADMIN_INIT = "admin_init";	
		const ACTION_ADD_SCRIPTS = "admin_enqueue_scripts";
		
		const ROLE_ADMIN = "admin";
		const ROLE_EDITOR = "editor";
		const ROLE_AUTHOR = "author";
		
		protected static $master_view;
		protected static $view;
		
		private static $arrSettings = array();
		private static $arrMenuPages = array();
		private static $tempVars = array();
		private static $startupError = "";
		private static $menuRole = self::ROLE_ADMIN;
		
		
		//Main constructor		 
		public function __construct($mainFile,$t,$defaultView) {						
			parent::__construct($mainFile,$t);
			
			//Set view
			self::$view = self::getGetVar("view");
			if(empty(self::$view))
				self::$view = $defaultView;
				
			//Add internal hook for adding a menu in arrMenus
			self::addAction(self::ACTION_ADMIN_MENU, "addAdminMenu");
			
			//If not inside plugin don't continue
			if($this->isInsidePlugin() == true) {
				self::addAction(self::ACTION_ADD_SCRIPTS, "addCommonScripts");
				self::addAction(self::ACTION_ADD_SCRIPTS, "onAddScripts");
			}
			
			//A must event for any admin. call onActivate function.
			$this->addEvent_onActivate();
			self::addActionAjax("show_image", "onShowImage");
		}		
		
		//Set the menu role - for viewing menus
		public static function setMenuRole($menuRole) {
			self::$menuRole = $menuRole;
		}
		
		//Set startup error to be shown in master view
		public static function setStartupError($errorMessage) {
			self::$startupError = $errorMessage;
		}		
		
		//Tells if the the current plugin opened is this plugin or not in the admin side
		private function isInsidePlugin() {
			$page = self::getGetVar("page");
			if($page == self::$dir_plugin) return(true);
			return(false);
		} 		
		
		//Add common used scripts
		public static function addCommonScripts() {
			$prefix = (is_ssl()) ? "https://" : "http://";
			
			//Include jquery ui
			if(GlobalsBannerRotator::$isNewVersion) {	
				//Load new jquery ui library				
				$urlJqueryUI = $prefix."ajax.googleapis.com/ajax/libs/jqueryui/1.10.1/jquery-ui.min.js";
				self::addScriptAbsoluteUrl($urlJqueryUI,"jquery-ui");
				wp_enqueue_style("jui-smoothness", esc_url_raw($prefix."ajax.googleapis.com/ajax/libs/jqueryui/1.10.1/themes/base/jquery-ui.css"), array(), null);
				
				if(function_exists("wp_enqueue_media")) wp_enqueue_media();				
			}else{	
				//Load old jquery ui library				
				$urlJqueryUI = $prefix."ajax.googleapis.com/ajax/libs/jqueryui/1.8.18/jquery-ui.min.js";
				self::addScriptAbsoluteUrl($urlJqueryUI,"jquery-ui");
				wp_enqueue_style("jui-smoothness", esc_url_raw($prefix."ajax.googleapis.com/ajax/libs/jqueryui/1.8.18/themes/base/jquery-ui.css"), array(), null);
			}
						
			self::addScriptCommon("settings","unite_settings");
			self::addScriptCommon("admin","unite_admin");
			self::addScriptCommon("jquery.tipsy","tipsy");
			self::addScriptCommon("jquery.iphone-style-checkboxes","iphone_style_checkbox");
			
			//Add styles			
			self::addStyleCommon("admin","unite_admin");
			
			//Add tipsy
			self::addStyleCommon("tipsy","tipsy");
			
			//Add fontello
			self::addStyleCommon("fontello","fontello");
			
			//Include farbtastic
			self::addScriptCommon("my-farbtastic","my-farbtastic","js/farbtastic");
			self::addStyleCommon("farbtastic","farbtastic","js/farbtastic");
			
			//Include codemirror
			self::addScriptCommon("codemirror","codemirror_js","js/codemirror");
			self::addScriptCommon("css","codemirror_js_css","js/codemirror");
			self::addStyleCommon("codemirror","codemirror_css","js/codemirror");
			
			//Include dropdown checklist
			self::addScriptCommon("ui.dropdownchecklist-1.4-min","dropdownchecklist_js","js/dropdownchecklist");
		}		
		
		//Admin pages parent, includes all the admin files by default
		public static function adminPages() {
			self::validateAdminPermissions();
		}		
		
		//Validate permission that the user is admin, and can manage options.
		protected static function isAdminPermissions() {			
			if( is_admin() &&  current_user_can("manage_options") )
				return(true);
				
			return(false);
		}
		
		//Validate admin permissions, if no pemissions - exit
		protected static function validateAdminPermissions() {
			if(!self::isAdminPermissions()) {
				echo "access denied";
				return(false);
			}			
		}
		
		//Set view that will be the master
		protected static function setMasterView($masterView) {
			self::$master_view = $masterView;
		}
		
		//Inlcude some view file
		protected static function requireView($view) {
			try{
				//Require master view file 
				if(!empty(self::$master_view) && !isset(self::$tempVars["is_masterView"]) ) {
					$masterViewFilepath = self::$path_views.self::$master_view.".php";
					UniteFunctionsBanner::validateFilepath($masterViewFilepath,"Master View");
					
					self::$tempVars["is_masterView"] = true;
					require $masterViewFilepath;
				}
				else{		
					//Simple require the view file
					$viewFilepath = self::$path_views.$view.".php";
					
					UniteFunctionsBanner::validateFilepath($viewFilepath,"View");
					require $viewFilepath;
				}				
			}catch (Exception $e) {
				echo "<br><br>View ($view) Error: <b>".$e->getMessage()."</b>";
				
				if(self::$debugMode == true)
					dmp($e->getTraceAsString());
			}
		}
		
		//Require some template from "templates" folder
		protected static function getPathTemplate($templateName) {			
			$pathTemplate = self::$path_templates.$templateName.".php";
			UniteFunctionsBanner::validateFilepath($pathTemplate,"Template");
			
			return($pathTemplate);
		}
		
		//Require settings file, the filename without .php
		protected static function requireSettings($settingsFile) {			
			try {
				require self::$path_plugin."settings/$settingsFile.php";
			} catch (Exception $e) {
				echo "<br><br>Settings ($settingsFile) Error: <b>".$e->getMessage()."</b>";
				dmp($e->getTraceAsString());
			}
		}
		
		//Get path to settings file
		protected static function getSettingsFilePath($settingsFile) {			
			$filepath = self::$path_plugin."settings/$settingsFile.php";
			return($filepath);
		}		
		
		//Add all js and css needed for media upload
		protected static function addMediaUploadIncludes() {			
			self::addWPScript("thickbox");
			self::addWPStyle("thickbox");
			self::addWPScript("media-upload");			
		}
		
		
		//Add admin menus from the list.
		public static function addAdminMenu() {			
			$role = "manage_options";
			
			switch(self::$menuRole) {
				case self::ROLE_AUTHOR:
					$role = "edit_published_posts";
				break;
				case self::ROLE_EDITOR:
					$role = "edit_pages";
				break;		
				default:		
				case self::ROLE_ADMIN:
					$role = "manage_options";
				break;
			}
			
			foreach(self::$arrMenuPages as $menu) {
				$title = $menu["title"];
				$pageFunctionName = $menu["pageFunction"];
				$icon = $menu["icon"];
				add_menu_page( $title, $title, $role, self::$dir_plugin, array(self::$t, $pageFunctionName), $icon );			
			}			
		}		
		
		//Add menu page
		protected static function addMenuPage($title,$pageFunctionName,$icon="") {						
			self::$arrMenuPages[] = array("title"=>$title,"pageFunction"=>$pageFunctionName,"icon"=>$icon);			
		}

		//Get url to some view
		public static function getViewUrl($viewName,$urlParams="") {
			$params = "&view=".$viewName;
			if(!empty($urlParams))
				$params .= "&".$urlParams;
			
			$link = admin_url( "admin.php?page=".self::$dir_plugin.$params);
			return($link);
		}
		
		//Register the "onActivate" event
		protected function addEvent_onActivate($eventFunc = "onActivate") {
			register_activation_hook( self::$mainFile, array(self::$t, $eventFunc) );
		}
		
		//Store settings in the object
		protected static function storeSettings($key,$settings) {
			self::$arrSettings[$key] = $settings;
		}
		
		//Get settings object
		protected static function getSettings($key) {
			if(!isset(self::$arrSettings[$key]))
				UniteFunctionsBanner::throwError("Settings $key not found");
			$settings = self::$arrSettings[$key];
			return($settings);
		}		
		
		//Add ajax back end callback, on some action to some function
		protected static function addActionAjax($ajaxAction,$eventFunction) {
			self::addAction('wp_ajax_'.self::$dir_plugin."_".$ajaxAction, $eventFunction);
			self::addAction('wp_ajax_nopriv_'.self::$dir_plugin."_".$ajaxAction, $eventFunction);
		}
		
		//Echo json ajax response
		private static function ajaxResponse($success,$message,$arrData = null) {			
			$response = array();			
			$response["success"] = $success;				
			$response["message"] = $message;
			
			if(!empty($arrData)) {				
				if(gettype($arrData) == "string")
					$arrData = array("data"=>$arrData);				
				
				$response = array_merge($response,$arrData);
			}
				
			$json = json_encode($response);
			
			echo $json;
			exit();
		}

		//Echo json ajax response, without message, only data
		protected static function ajaxResponseData($arrData) {
			if(gettype($arrData) == "string")
				$arrData = array("data"=>$arrData);
			
			self::ajaxResponse(true,"",$arrData);
		}
		
		//Echo json ajax response
		protected static function ajaxResponseError($message,$arrData = null) {
			
			self::ajaxResponse(false,$message,$arrData,true);
		}
		
		//Echo ajax success response
		protected static function ajaxResponseSuccess($message,$arrData = null) {			
			self::ajaxResponse(true,$message,$arrData,true);			
		}
		
		//Echo ajax success response
		protected static function ajaxResponseSuccessRedirect($message,$url) {
			$arrData = array("is_redirect"=>true,"redirect_url"=>$url);			
			self::ajaxResponse(true,$message,$arrData,true);
		}		

		//Update plugin
		protected static function updatePlugin($viewBack = false) {
			$linkBack = self::getViewUrl($viewBack);
			$htmlLinkBack = UniteFunctionsBanner::getHtmlLink($linkBack, "Go Back");
			
			$zip = new UniteZipBanner();
						
			try {				
				if(function_exists("unzip_file") == false) {					
					if( UniteZipBanner::isZipExists() == false)
						UniteFunctionsBanner::throwError("The ZipArchive php extension not exists, can't extract the update file. Please turn it on in php ini.");
				}
				
				dmp("Update in progress...");
				
				$arrFiles = UniteFunctionsBanner::getVal($_FILES, "update_file");
				if(empty($arrFiles))
					UniteFunctionsBanner::throwError("Update file don't found.");
					
				$filename = UniteFunctionsBanner::getVal($arrFiles, "name");
				
				if(empty($filename))
					UniteFunctionsBanner::throwError("Update filename not found.");
				
				$fileType = UniteFunctionsBanner::getVal($arrFiles, "type");
				
				/*				
				$fileType = strtolower($fileType);
				
				if($fileType != "application/zip")
					UniteFunctionsBanner::throwError("The file uploaded is not zip.");
				*/
				
				$filepathTemp = UniteFunctionsBanner::getVal($arrFiles, "tmp_name");
				if(file_exists($filepathTemp) == false)
					UniteFunctionsBanner::throwError("Can't find the uploaded file.");	

				//Crate temp folder
				UniteFunctionsBanner::checkCreateDir(self::$path_temp);

				//Create the update folder
				$pathUpdate = self::$path_temp."update_extract/";				
				UniteFunctionsBanner::checkCreateDir($pathUpdate);
								
				//Remove all files in the update folder
				if(is_dir($pathUpdate)) { 
					$arrNotDeleted = UniteFunctionsBanner::deleteDir($pathUpdate,false);
					if(!empty($arrNotDeleted)) {
						$strNotDeleted = print_r($arrNotDeleted,true);
						UniteFunctionsBanner::throwError("Could not delete those files from the update folder: $strNotDeleted");
					}
				}
				
				//Copy the zip file
				$filepathZip = $pathUpdate.$filename;
				
				$success = move_uploaded_file($filepathTemp, $filepathZip);
				if($success == false)
					UniteFunctionsBanner::throwError("Can't move the uploaded file here: {$filepathZip}.");
				
				if(function_exists("unzip_file") == true) {
					WP_Filesystem();
					$response = unzip_file($filepathZip, $pathUpdate);
				}
				else					
					$zip->extract($filepathZip, $pathUpdate);
				
				//Get extracted folder
				$arrFolders = UniteFunctionsBanner::getFoldersList($pathUpdate);
				if(empty($arrFolders))
					UniteFunctionsBanner::throwError("The update folder is not extracted");
				
				if(count($arrFolders) > 1)
					UniteFunctionsBanner::throwError("Extracted folders are more then 1. Please check the update file.");
					
				//Get product folder
				$productFolder = $arrFolders[0];
				if(empty($productFolder))
					UniteFunctionsBanner::throwError("Wrong product folder.");
					
				if($productFolder != self::$dir_plugin)
					UniteFunctionsBanner::throwError("The update folder don't match the product folder, please check the update file.");
				
				$pathUpdateProduct = $pathUpdate.$productFolder."/";				
				
				//Check some file in folder to validate it's the real one:
				$checkFilepath = $pathUpdateProduct.$productFolder.".php";
				if(file_exists($checkFilepath) == false)
					UniteFunctionsBanner::throwError("Wrong update extracted folder. The file: {$checkFilepath} not found.");
				
				//Copy the plugin without the captions file
				$pathOriginalPlugin = self::$path_plugin;
				
				$arrBlackList = array();
				$arrBlackList[] = "css/caption.css";
				
				UniteFunctionsBanner::copyDir($pathUpdateProduct, $pathOriginalPlugin,"",$arrBlackList);
				
				//Delete the update
				UniteFunctionsBanner::deleteDir($pathUpdate);
				
				dmp("Updated Successfully, redirecting...");
				echo "<script>location.href='$linkBack'</script>";
				
			}catch(Exception $e) {
				$message = $e->getMessage();
				$message .= " <br> Please update the plugin manually via the ftp";
				echo "<div style='color:#B80A0A;font-size:18px;'><b>Update Error: </b> $message</div><br>";
				echo $htmlLinkBack;
				exit();
			}			
		} 	
 	
	} 
?>