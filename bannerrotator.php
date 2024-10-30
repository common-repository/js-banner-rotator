<?php 
	/*
	Plugin Name: jQuery Banner Rotator WordPress Plugin
	Plugin URI: http://www.codegrape.com/item/jquery-banner-rotator-wordpress-plugin/1695
	Description: jQuery banner rotator plugin featuring multiple transitions that supports text effects for captions. 
	Author: flashblue
	Version: 1.0.0
	Author URI: http://www.codegrape.com/user/flashblue
	*/

	$bannerRotatorVersion = "1.0.0";
	$currentFile = __FILE__;
	$currentFolder = dirname($currentFile);
	
	//Include framework files
	require_once $currentFolder . '/includes/framework/include_framework.php';
	
	//Include bases
	require_once $folderIncludes . 'base.class.php';
	require_once $folderIncludes . 'elements_base.class.php';
	require_once $folderIncludes . 'base_admin.class.php';
	require_once $folderIncludes . 'base_front.class.php';
	
	//Include product files
	require_once $currentFolder . '/includes/bannerrotator_settings_product.class.php';
	require_once $currentFolder . '/includes/bannerrotator_globals.class.php';
	require_once $currentFolder . '/includes/bannerrotator_operations.class.php';
	require_once $currentFolder . '/includes/bannerrotator_slider.class.php';
	require_once $currentFolder . '/includes/bannerrotator_output.class.php';
	require_once $currentFolder . '/includes/bannerrotator_slide.class.php';
	require_once $currentFolder . '/includes/bannerrotator_widget.class.php';
	require_once $currentFolder . '/includes/bannerrotator_params.class.php';	
	
	try {
		
		//Register the slider widget	
		UniteFunctionsWPBanner::registerWidget("BannerRotator_Widget");
		
		//Add shortcode
		function banner_rotator_shortcode($args) {					
			$sliderAlias = UniteFunctionsBanner::getVal($args,0);
			ob_start();
			$slider = BannerRotatorOutput::putSlider($sliderAlias);
			$content = ob_get_contents();
			ob_clean();
			ob_end_clean();
			
			//Handle slider output types
			if(!empty($slider)) {
				$outputType = $slider->getParam("output_type","");
				switch($outputType) {
					case "compress":
						$content = str_replace("\n", "", $content);
						$content = str_replace("\r", "", $content);
						return($content);
					break;
					case "echo":
						echo $content;		//bypass the filters
					break;
					default:
						return($content);
					break;
				}
			} else
				return($content);		//normal output
				
		}
		
		add_shortcode( 'banner_rotator', 'banner_rotator_shortcode' );		
		
		if(is_admin()) {		//Load admin part
			require_once $currentFolder."/bannerrotator_admin.php";		
			
			$productAdmin = new BannerRotatorAdmin($currentFile);
			
		} else {
			//Load front part			
			//Put Banner Rotator on the page. The data can be slider ID or slider alias.
			function putBannerRotator($data,$putIn = "") {
				$operations = new BannerOperations();
				$arrValues = $operations->getGeneralSettingsValues();
				$includesGlobally = UniteFunctionsBanner::getVal($arrValues, "includes_globally","on");
				$strPutIn = UniteFunctionsBanner::getVal($arrValues, "pages_for_includes");
				$isPutIn = BannerRotatorOutput::isPutIn($strPutIn,true);
				
				if($isPutIn == false && $includesGlobally == "off") {
					$output = new BannerRotatorOutput();
					$option1Name = "Include BannerRotator libraries globally (all pages/posts)";
					$option2Name = "Pages to include BannerRotator libraries";
					$output->putErrorMessage(__("If you want to use the PHP function \"putBannerRotator\" in your code please make sure to check \" ",BANNERROTATOR_TEXTDOMAIN).$option1Name.__(" \" in the backend's \"General Settings\" (top right panel). <br> <br> Or add the current page to the \"",BANNERROTATOR_TEXTDOMAIN).$option2Name.__("\" option box."));
					return(false);
				}
				
				BannerRotatorOutput::putSlider($data,$putIn);
			}
			
			require_once $currentFolder."/bannerrotator_front.php";
			$productFront = new BannerRotatorFront($currentFile);
		}
	
		
	} catch(Exception $e) {
		$message = $e->getMessage();
		$trace = $e->getTraceAsString();
		echo _e("Banner Rotator Error:",BANNERROTATOR_TEXTDOMAIN)."<b>".$message."</b>";
	}
	
