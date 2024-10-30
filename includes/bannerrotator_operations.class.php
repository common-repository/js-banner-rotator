<?php
	class BannerOperations extends UniteElementsBaseBanner {
		
		//Get button classes
		public function getButtonClasses() {
			
			$arrButtons = array(
				"red"=>"Red Button",
				"green"=>"Green Button",
				"blue"=>"Blue Button",
				"orange"=>"Orange Button",
				"darkgrey"=>"Darkgrey Button",
				"lightgrey"=>"Lightgrey Button",
			);
			
			return($arrButtons);
		}		
		
		//Get easing functions array
		public function getArrEasing($toAssoc = true) {			
			$arrEasing = array(
				"linear",
				"ease",
				"in",
				"out",
				"in-out",
				"snap",
				"easeOutCubic",
				"easeInOutCubic",
				"easeInCirc",
				"easeOutCirc",
				"easeInOutCirc",
				"easeInExpo",
				"easeOutExpo",
				"easeInOutExpo",
				"easeInQuad",
				"easeOutQuad",
				"easeInOutQuad",
				"easeInQuart",
				"easeOutQuart",
				"easeInOutQuart",
				"easeInQuint",
				"easeOutQuint",
				"easeInOutQuint",
				"easeInSine",
				"easeOutSine",
				"easeInOutSine",
				"easeInBack",
				"easeOutBack",
				"easeInOutBack"
			);
			
			if($toAssoc)
				$arrEasing = UniteFunctionsBanner::arrayToAssoc($arrEasing);
			
			return($arrEasing);
		}
		
		//Get arr end easing
		public function getArrEndEasing() {
			$arrEasing = $this->getArrEasing(false);
			$arrEasing = array_merge(array("nothing"),$arrEasing);
			$arrEasing = UniteFunctionsBanner::arrayToAssoc($arrEasing);
			$arrEasing["nothing"] = "No Change";
			
			return($arrEasing);
		}
		
		//Get transition array
		public function getArrTransition() {
			
			$arrTransition = array(
				"random"=>"Random",
				"block"=>"Block",
				"cube"=>"Cube",
				"cubeRandom"=>"Cube Random",
				"cubeShow"=>"Cube Show",
				"cubeStop"=>"Cube Stop",
				"cubeStopRandom"=>"Cube Stop Random",
				"cubeHide"=>"Cube Hide",
				"cubeSize"=>"Cube Size",
				"cubeSpread"=>"Cube Spread",
				"horizontal"=>"Horizontal",
				"showBars"=>"Show Bars",
				"showBarsRandom"=>"Show Bars Random",
				"tube"=>"Tube",
				"fade"=>"Fade",
				"fadeFour"=>"Fade Four",
				"parallel"=>"Parallel",
				"blind"=>"Blind",
				"blindHeight"=>"Blind Height",
				"blindWidth"=>"Blind Width",
				"directionTop"=>"Direction Top",
				"directionBottom"=>"Direction Bottom",
				"directionRight"=>"Direction Right",
				"directionLeft"=>"Direction Left",
				"glassCube"=>"Glass Cube",
				"glassBlock"=>"Glass Block",
				"circles"=>"Circles",
				"circlesInside"=>"Circles Inside",
				"circlesRotate"=>"Circles Rotate",
				"upBars"=>"Up Bars",
				"downBars"=>"Down Bars",
				"hideBars"=>"Hide Bars",
				"swapBars"=>"Swap Bars",
				"swapBarsBack"=>"Swap Bars Back",
				"swapBlocks"=>"Swap Blocks",
				"cut"=>"Cut"
			);
						
			return($arrTransition);
		}		
		
		//Get random transition
		public static function getRandomTransition() {
			$arrTrans = self::getArrTransition();
			unset($arrTrans["random"]);
			$trans = array_rand($arrTrans);
			
			return($trans);
		}		
		
		//Get animations array
		public function getArrAnimations() {
			
			$arrAnimations = array(
				"fade"=>"Fade",
				"sft"=>"Short from Top",
				"sfb"=>"Short from Bottom",
				"sfr"=>"Short from Right",
				"sfl"=>"Short from Left",
				"lft"=>"Long from Top",
				"lfb"=>"Long from Bottom",
				"lfr"=>"Long from Right",
				"lfl"=>"Long from Left",
				"skewfromright"=>"Skew From Long Right",
				"skewfromleft"=>"Skew From Long Left",
				"skewfromleftshort"=>"Skew From Short Right",
				"skewfromrightshort"=>"Skew From Short Left",
				"customin"=>"Custom Rotate In",
				"randomrotate"=>"Random Rotate"
			);
			
			return($arrAnimations);
		}
		
		//Get "end" animations array
		public function getArrEndAnimations() {
			$arrAnimations = array(
				"auto"=>"Choose Automatic",
				"fadeout"=>"Fade Out",
				"stt"=>"Short to Top",
				"stb"=>"Short to Bottom",
				"stl"=>"Short to Left",
				"str"=>"Short to Right",
				"ltt"=>"Long to Top",
				"ltb"=>"Long to Bottom",
				"ltl"=>"Long to Left",
				"ltr"=>"Long to Right",
				"skewtoright"=>"Skew To Right",
				"skewtoleft"=>"Skew To Left",
				"skewtorightshort"=>"Skew To Right Short",
				"skewtoleftshort"=>"Skew To Left Short",
				"customout"=>"Custom Rotate Out",
				"randomrotateout"=>"Random Rotate Out"
			);
			
			return($arrAnimations);
		}		
		
		//Parse css file and get the classes from there.
		public function getArrCaptionClasses($contentCSS) {
			//parse css captions file
			$parser = new UniteCssParserBanner();
			$parser->initContent($contentCSS);
			$arrCaptionClasses = $parser->getArrClasses();
			return($arrCaptionClasses);
		}
		
		//Get the select classes html for putting in the html by ajax 
		private function getHtmlSelectCaptionClasses($contentCSS) {
			$arrCaptions = $this->getArrCaptionClasses($contentCSS);
			$htmlSelect = UniteFunctionsBanner::getHTMLSelect($arrCaptions,"","id='layer_caption' name='layer_caption'",true);
			return($htmlSelect);
		}
		
		//Get contents of the css file
		public function getCaptionsContent() {
			$contentCSS = file_get_contents(GlobalsBannerRotator::$filepath_captions);
			return($contentCSS);
		}		
		
		//Update captions css file content
		//@return new captions html select 
		public function updateCaptionsContentData($content) {
			$content = stripslashes($content);
			$content = trim($content);
			UniteFunctionsBanner::writeFile($content, GlobalsBannerRotator::$filepath_captions);
			
			//output captions array 
			$arrCaptions = $this->getArrCaptionClasses($content);
			return($arrCaptions);
		}
		
		//Copy from original css file to the captions css.
		public function restoreCaptionsCss() {			
			if(!file_exists(GlobalsBannerRotator::$filepath_captions_original))
				UniteFunctionsBanner::throwError("The original css file: captions_original.css doesn't exists.");
			
			$success = @copy(GlobalsBannerRotator::$filepath_captions_original, GlobalsBannerRotator::$filepath_captions);
			
			if($success==false)
				UniteFunctionsBanner::throwError("Failed to restore from the original captions file.");
		}
		
		//Preview slider output
		//If output object is null - create object
		public function previewOutput($sliderID,$output = null) {			
			if($sliderID == "empty_output") {
				$this->loadingMessageOutput();
				exit();
			}
			
			if($output == null)
				$output = new BannerRotatorOutput();
			
			$output->setPreviewMode();
			
			$slider = new BannerRotator();
			$slider->initByID($sliderID);
			$isWpmlExists = UniteWpmlBanner::isWpmlExists();
			$useWpml = $slider->getParam("useWpml","false");
			$wpmlActive = false;
			
			if($isWpmlExists && $useWpml=="true") {
				$wpmlActive = true;
				$arrLanguages = UniteWpmlBanner::getArrLanguages(false);
				
				//Set current lang to output
				$currentLang = UniteFunctionsBanner::getPostGetVariable("lang");
				
				if(empty($currentLang))
					$currentLang = UniteWpmlBanner::getCurrentLang();
				
				if(empty($currentLang))
					$currentLang = $arrLanguages[0];
					
				$output->setLang($currentLang);
				
				$selectLangChoose = UniteFunctionsBanner::getHTMLSelect($arrLanguages,$currentLang,"id='select_langs'",true);
			}
			
			//Put the output html
			$urlPlugin = BannerRotatorAdmin::$url_plugin;
			$urlPreviewPattern = UniteBaseClassBanner::$url_ajax_actions."&client_action=preview_slider&sliderid={$sliderID}&lang=[lang]&nonce=[nonce]";	
			$nonce = wp_create_nonce("bannerrotator_actions");
			
			$setBase = (is_ssl()) ? "https://" : "http://";
			?>
				<html>
					<head>
						<link rel='stylesheet' href='<?php echo $urlPlugin?>css/banner-rotator.css' type='text/css' media='all' />
						<link rel='stylesheet' href='<?php echo $urlPlugin?>css/caption.css' type='text/css' media='all' />
						<script type='text/javascript' src='<?php echo $urlPlugin?>js/jquery.js'></script>
						<script type='text/javascript' src='<?php echo $urlPlugin?>js/jquery.flashblue-plugins.js'></script>
						<script type='text/javascript' src='<?php echo $urlPlugin?>js/jquery.banner-rotator.js'></script>
					</head>
					<body style="padding:0px;margin:0px;">
						<?php if($wpmlActive) {?>
							<div style="margin-bottom:10px;text-align:center;">
							<?php _e("Choose language")?>: <?php echo $selectLangChoose?>
							</div>
							
							<script type="text/javascript">
								var g_previewPattern = '<?php echo $urlPreviewPattern?>';
								jQuery("#select_langs").change(function(){
									var lang = this.value;
									var nonce = "<?php echo $nonce; ?>";
									var pattern = g_previewPattern;
									var urlPreview = pattern.replace("[lang]",lang).replace("[nonce]",nonce);
									location.href = urlPreview;
								});
							</script>
						<?php }?>
						
						<?php
							$output->putSliderBase($sliderID);		 
						?>
					</body>
				</html>
			<?php 
			exit();
		}
		
		//Output loading message
		public function loadingMessageOutput() {
			?>
			<div class="message_loading_preview">Loading Preview...</div>
			<?php 
		}
		
		//Put slide preview by data
		public function putSlidePreviewByData($data) {			
			if($data=="empty_output") {
				$this->loadingMessageOutput();
				exit();
			}
				
			$data = UniteFunctionsBanner::jsonDecodeFromClientSide($data);
			
			$slideID = $data["slideid"];
			$slide = new BannerSlide();
			$slide->initByID($slideID);
			$sliderID = $slide->getSliderID();
			
			$output = new BannerRotatorOutput();
			$output->setOneSlideMode($data);
			
			$this->previewOutput($sliderID,$output);
		}		
		
		//Update general settings
		public function updateGeneralSettings($data) {
			$strSettings = serialize($data);
			$params = new BannerRotatorParams();
			$params->updateFieldInDB("general", $strSettings);
		}		
		
		//Get general settigns values.
		public function getGeneralSettingsValues() {			
			$params = new BannerRotatorParams();
			$strSettings = $params->getFieldFromDB("general");
			
			$arrValues = array();
			if(!empty($strSettings))
				$arrValues = unserialize($strSettings);
			
			return($arrValues);
		}
		
		//Get html font import 
		public static function getCleanFontImport($font){
			$setBase = (is_ssl()) ? "https://" : "http://";
			
			if(strpos($font, "href=") === false) { //Fallback for old versions
				return '<link href="'.$setBase.'fonts.googleapis.com/css?family='.$font.'" rel="stylesheet" type="text/css" media="all" />';
			} else {
				$font = str_replace(array('http://', 'https://'), array($setBase, $setBase), $font);
				return stripslashes($font);
			}
		}
		
	}
?>