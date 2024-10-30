<?php
	class UniteFunctionsWPBanner {

		const THUMB_SMALL = "thumbnail";
		const THUMB_MEDIUM = "medium";
		const THUMB_LARGE = "large";
		const THUMB_FULL = "full";
		
		
		//Get blog id
		public static function getBlogID() {
			global $blog_id;
			return($blog_id);
		}		
		
		//Check if multisite
		public static function isMultisite() {
			$isMultisite = is_multisite();
			return($isMultisite);
		}		
		
		//Check if some db table exists
		public static function isDBTableExists($tableName) {
			global $wpdb;
			
			if(empty($tableName))
				UniteFunctionsBanner::throwError("Empty table name!!!");
			
			$sql = "show tables like '$tableName'";
			
			$table = $wpdb->get_var($sql);
			
			if($table == $tableName)
				return(true);
				
			return(false);
		}		
		
		//Get wordpress base path
		public static function getPathBase() {
			return ABSPATH;
		}
		
		//Get wp-content path
		public static function getPathContent() {		
			if(self::isMultisite()) {
				if(!defined("BLOGUPLOADDIR")) {
					$pathBase = self::getPathBase();
					$pathContent = $pathBase."wp-content/";
				}else
				  $pathContent = BLOGUPLOADDIR;
			}else{
				$pathContent = WP_CONTENT_DIR;
				if(!empty($pathContent)) {
					$pathContent .= "/";
				}
				else{
					$pathBase = self::getPathBase();
					$pathContent = $pathBase."wp-content/";
				}
			}
			
			return($pathContent);
		}
		
		//Get content url
		public static function getUrlContent() {
		
			if(self::isMultisite() == false) {	
				//Without multisite
				$baseUrl = content_url()."/";
			} else {	
				//For multisite
				$arrUploadData = wp_upload_dir();
				$baseUrl = $arrUploadData["baseurl"]."/";
			}
			
			return($baseUrl);			
		}
		
		//Register widget (must be class)
		public static function registerWidget($widgetName) {
			add_action('widgets_init', create_function('', 'return register_widget("'.$widgetName.'");'));
		}

		//Get image relative path from image url (from upload)
		public static function getImagePathFromURL($urlImage) {
			
			$baseUrl = self::getUrlContent();
			$pathImage = str_replace($baseUrl, "", $urlImage);
			
			return($pathImage);
		}
		
		//Get image real path phisical on disk from url
		public static function getImageRealPathFromUrl($urlImage) {
			$filepath = self::getImagePathFromURL($urlImage);
			$realPath = UniteFunctionsWPBanner::getPathContent().$filepath;
			return($realPath);
		}		
		
		//Get image url from image path.
		public static function getImageUrlFromPath($pathImage) {
			//Protect from absolute url
			$pathLower = strtolower($pathImage);
			if(strpos($pathLower, "http://") !== false || strpos($pathLower, "www.") === 0)
				return($pathImage);
			
			$urlImage = self::getUrlContent().$pathImage;
			return($urlImage); 
		}

		//Write settings language file for wp automatic scanning
		public static function writeSettingLanguageFile($filepath) {
			$info = pathinfo($filepath);
			$path = UniteFunctionsBanner::getVal($info, "dirname")."/";
			$filename = UniteFunctionsBanner::getVal($info, "filename");
			$ext =  UniteFunctionsBanner::getVal($info, "extension");
			$filenameOutput = "{$filename}_{$ext}_lang.php";
			$filepathOutput = $path.$filenameOutput;
			
			//Load settings
			$settings = new UniteSettingsAdvancedBanner();	
			$settings->loadXMLFile($filepath);
			$arrText = $settings->getArrTextFromAllSettings();
			
			$str = "";
			$str .= "<?php \n";
			foreach($arrText as $text) {
				$text = str_replace('"', '\\"', $text);
				$str .= "_e(\"$text\",\"".BANNERROTATOR_TEXTDOMAIN."\"); \n";				
			}
			$str .= "?>";
			
			UniteFunctionsBanner::writeFile($str, $filepathOutput);
		}
		
		//Check the current post for the existence of a short code
		public static function hasShortcode($shortcode = '') {  
		      
		    $post = get_post(get_the_ID());  
		      
		    if (empty($shortcode))   
		        return $found;
		        		        
		    $found = false; 
		        
		    if (stripos($post->post_content, '[' . $shortcode) !== false )    
		        $found = true;  
		       
		    return $found;  
		}
		
		//Get attachment image url
		public static function getUrlAttachmentImage($thumbID,$size = self::THUMB_FULL) {
			$arrImage = wp_get_attachment_image_src($thumbID,$size);
			if(empty($arrImage))
				return(false);
			$url = UniteFunctionsBanner::getVal($arrImage, 0);
			return($url);
		}
		
		//Get attachment image array by id and size
		public static function getAttachmentImage($thumbID,$size = self::THUMB_FULL) {			
			$arrImage = wp_get_attachment_image_src($thumbID,$size);
			if(empty($arrImage))
				return(false);
			
			$output = array();
			$output["url"] = UniteFunctionsBanner::getVal($arrImage, 0);
			$output["width"] = UniteFunctionsBanner::getVal($arrImage, 1);
			$output["height"] = UniteFunctionsBanner::getVal($arrImage, 2);
			
			return($output);
		}		
		
		//Get post thumb id from post id
		public static function getPostThumbID($postID) {
			$thumbID = get_post_thumbnail_id( $postID );
			return($thumbID);
		}
		
		//Get url of post thumbnail
		public static function getUrlPostImage($postID,$size = self::THUMB_FULL) {			
			$post_thumbnail_id = get_post_thumbnail_id( $postID );
			if(empty($post_thumbnail_id))
				return("");
			
			$arrImage = wp_get_attachment_image_src($post_thumbnail_id,$size);
			if(empty($arrImage))
				return("");
			
			$urlImage = $arrImage[0];
			return($urlImage);
		}
		
		//Get current language code
		public static function getCurrentLangCode() {
			$langTag = get_bloginfo("language");
			$data = explode("-", $langTag);
			$code = $data[0];
			return($code);
		}		
		
	}	
?>