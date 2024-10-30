<?php
	class BannerRotatorOutput {
		
		private static $sliderSerial = 0;
		
		private $sliderHtmlID;
		private $sliderHtmlID_wrapper;
		private $slider;
		private $oneSlideMode = false;
		private $oneSlideData;
		private $previewMode = false;	//Admin preview mode
		private $slidesNumIndex;
		
		
		//Check the put in string
		//Return true / false if the put in string match the current page.
		public static function isPutIn($putIn,$emptyIsFalse = false) {			
			$putIn = strtolower($putIn);
			$putIn = trim($putIn);
			
			if($emptyIsFalse && empty($putIn))
				return(false);
			
			if($putIn == "homepage") {		//Filter by homepage
				if(is_front_page() == false)
					return(false);
			}				
			else		//Case filter by pages	
			if(!empty($putIn)) {
				$arrPutInPages = array();
				$arrPagesTemp = explode(",", $putIn);
				foreach($arrPagesTemp as $page) {
					if(is_numeric($page) || $page == "homepage")
						$arrPutInPages[] = $page;
				}
				if(!empty($arrPutInPages)) {					
					//Get current page id
					$currentPageID = "";
					if(is_front_page() == true)
						$currentPageID = "homepage";
					else{
						global $post;
						if(isset($post->ID))
							$currentPageID = $post->ID;
					}
						
					//Do the filter by pages
					if(array_search($currentPageID, $arrPutInPages) === false) 
						return(false);
				}
			}
			
			return(true);
		}		
		
		//Put the banner rotator slider on the html page
		//@param $data - mixed, can be ID or Alias
		public static function putSlider($sliderID,$putIn="") {
			
			$isPutIn = self::isPutIn($putIn);
			if($isPutIn == false)
				return(false);
			
			$output = new BannerRotatorOutput();
			$output->putSliderBase($sliderID);
			
			$slider = $output->getSlider();
			return($slider);
		}		
		
		//Set one slide mode for preview
		public function setOneSlideMode($data) {
			$this->oneSlideMode = true;
			$this->oneSlideData = $data;
		}
		
		//Set preview mode
		public function setPreviewMode() {
			$this->previewMode = true;
		}
		
		//Get the last slider after the output
		public function getSlider() {
			return($this->slider);
		}
		
		//Get slide full width video data
		private function getSlideFullWidthVideoData(BannerSlide $slide) {			
			$response = array("found"=>false);
			
			//Deal full width video
			$enableVideo = $slide->getParam("enable_video","false");
			if($enableVideo != "true")
				return($response);
				
			$videoID = $slide->getParam("video_id","");
			$videoID = trim($videoID);
			
			if(empty($videoID))
				return($response);
				
			$response["found"] = true;
			
			$videoType = is_numeric($videoID)? "vimeo" : "youtube";
			$videoAutoplay = $slide->getParam("video_autoplay");
			$videoNextslide = $slide->getParam("video_nextslide");
			
			$response["type"] = $videoType;
			$response["videoID"] = $videoID;
			$response["autoplay"] = UniteFunctionsBanner::strToBool($videoAutoplay);
			$response["nextslide"] = UniteFunctionsBanner::strToBool($videoNextslide);
			
			return($response);
		}
		
		//Put full width video layer
		private function putFullWidthVideoLayer($videoData) {
			if($videoData["found"] == false)
				return(false);
			
			$autoplay = UniteFunctionsBanner::boolToStr($videoData["autoplay"]);
			$nextslide = UniteFunctionsBanner::boolToStr($videoData["nextslide"]);
			
			$htmlParams = 'data-x="0" data-y="0" data-speed="500" data-start="10" data-easing="easeOutBack"';
			
			$videoID = $videoData["videoID"];
			
			$setBase = (is_ssl()) ? "https://" : "http://";
			
			if($videoData["type"] == "youtube"):	//youtube
				?>	
				<div class="caption fade fullscreenvideo" data-nextslideatend="<?php echo $nextslide?>" data-autoplay="<?php echo $autoplay?>" <?php echo $htmlParams?>><iframe src="<?php echo $setBase; ?>www.youtube.com/embed/<?php echo $videoID?>?enablejsapi=1&amp;html5=1&amp;hd=1&amp;wmode=opaque&amp;controls=1&amp;autoplay=1&amp;showinfo=0;rel=0;" width="100%" height="100%"></iframe></div>				
				<?php 
			else:									//vimeo
				?>
				<div class="caption fade fullscreenvideo" data-nextslideatend="<?php echo $nextslide?>" data-autoplay="<?php echo $autoplay?>" <?php echo $htmlParams?>><iframe src="<?php echo $setBase; ?>player.vimeo.com/video/<?php echo $videoID?>?title=0&amp;byline=0&amp;portrait=0;api=1" width="100%" height="100%"></iframe></div>
				<?php
			endif;
		}
		
		//Filter the slides for one slide preview
		private function filterOneSlide($slides) {			
			$oneSlideID = $this->oneSlideData["slideid"];
			$oneSlideParams = UniteFunctionsBanner::getVal($this->oneSlideData, "params");		 	
			$oneSlideLayers = UniteFunctionsBanner::getVal($this->oneSlideData, "layers");
			
			if(gettype($oneSlideParams) == "object")
				$oneSlideParams = (array)$oneSlideParams;

			if(gettype($oneSlideLayers) == "object")
				$oneSlideLayers = (array)$oneSlideLayers;
				
			if(!empty($oneSlideLayers))
				$oneSlideLayers = UniteFunctionsBanner::convertStdClassToArray($oneSlideLayers);
			
			$newSlides = array();
			foreach($slides as $slide) {				
				$slideID = $slide->getID();
				
				if($slideID == $oneSlideID) {
										
					if(!empty($oneSlideParams))
						$slide->setParams($oneSlideParams);
					
					if(!empty($oneSlideLayers))
						$slide->setLayers($oneSlideLayers);
					
					$newSlides[] = $slide;	//add 2 slides
					$newSlides[] = $slide;
				}
			}
			
			return($newSlides);
		}
		
		//Put the slider slides
		private function putSlides() {			
			$sliderType = $this->slider->getParam("sliderType");
			
			$publishedOnly = true;
			if($this->previewMode == true && $this->oneSlideMode == true) {
				$publishedOnly = false;
			}
						
			$slides = $this->slider->getSlides($publishedOnly);
			
			$this->slidesNumIndex = $this->slider->getSlidesNumbersByIDs(true);
			
			if(empty($slides)):
				?>
				<div class="no-slides-text">
					No slides found, please add some slides
				</div>
				<?php 
			endif;
			
			$thumbWidth = $this->slider->getParam("thumbWidth",72);
			$thumbHeight = $this->slider->getParam("thumbHeight",54);
			
			$slideWidth = $this->slider->getParam("width",1170);
			$slideHeight = $this->slider->getParam("height",500);
			
			//For one slide preview
			if($this->oneSlideMode == true)				
				$slides = $this->filterOneSlide($slides);
			?>
				<ul>
			<?php
						
			foreach($slides as $index => $slide) {				
				$params = $slide->getParams();
				
				//Check if date is set
				$date_from = $slide->getParam("date_from","");
				$date_to = $slide->getParam("date_to","");
				
				if($date_from != "") {
					$date_from = strtotime($date_from);
					if(time() < $date_from) continue;
				}
				
				if($date_to != "") {
					$date_to = strtotime($date_to);
					if(time() > $date_to) continue;
				}
				
				$transition = $slide->getParam("slide_transition","random");
					
				$urlSlideImage = $slide->getImageUrl();
				
				//Get image alt
				$imageFilename = $slide->getImageFilename();
				$info = pathinfo($imageFilename);
				$alt = $info["filename"];				
				
				//Get thumb url
				$urlThumb = $slide->getParam("slide_thumb","");
				
				if(empty($urlThumb)) {	//try to get resized thumb
					$pathThumb = $slide->getImageFilepath();
					if(!empty($pathThumb))
						$urlThumb = UniteBaseClassBanner::getImageUrl($pathThumb,$thumbWidth,$thumbHeight,true);
				}
				
				//If not - put regular image:
				if(empty($urlThumb))						
					$urlThumb = $slide->getImageUrl();
				
				$htmlThumb = 'data-thumb="'.$urlThumb.'" ';
				
				//Get link
				$htmlLink = "";
				$enableLink = $slide->getParam("enable_link","false");
				if($enableLink == "true") {
					$linkType = $slide->getParam("link_type","regular");
					switch($linkType) {
						
						//Normal link						
						default:		
						case "regular":
							$link = $slide->getParam("link","");
							$linkOpenIn = $slide->getParam("link_open_in","same");
							$htmlTarget = "";
							if($linkOpenIn == "new")
								$htmlTarget = ' data-target="_blank"';
							$htmlLink = "data-link=\"$link\" $htmlTarget ";	
						break;		
						
						//Link to slide						
						case "slide":
							$slideLink = UniteFunctionsBanner::getVal($params, "slide_link");
							if(!empty($slideLink) && $slideLink != "nothing") {
								//Get slide index from id
								if(is_numeric($slideLink))
									$slideLink = UniteFunctionsBanner::getVal($this->slidesNumIndex, $slideLink);
								
								if(!empty($slideLink))
									$htmlLink = "data-link=\"slide\" data-linktoslide=\"$slideLink\" ";
							}
						break;
					}
					
					//Set link position
					$linkPos = UniteFunctionsBanner::getVal($params, "link_pos","front");
					if($linkPos == "back")
						$htmlLink .= 'data-slideindex="back" ';	
				}
				
				//Set delay
				$htmlDelay = "";
				$delay = $slide->getParam("delay","");
				if(!empty($delay) && is_numeric($delay))
					$htmlDelay = "data-delay=\"$delay\" ";
				
				//Get duration
				$htmlDuration = "";
				$duration = $slide->getParam("transition_duration","");
				if(!empty($duration) && is_numeric($duration))
					$htmlDuration = "data-masterspeed=\"$duration\" ";
				
				//Get rotation
				$htmlRotation = "";
				$rotation = $slide->getParam("transition_rotation","");
				if(!empty($rotation)) {
					$rotation = (int)$rotation;
					if($rotation != 0) {
						if($rotation > 720 && $rotation != 999)
							$rotation = 720;
						if($rotation < -720)
							$rotation = -720;
					}
					$htmlRotation = "data-rotate=\"$rotation\" ";
				}
				
				$fullWidthVideoData = $this->getSlideFullWidthVideoData($slide);
				
				//Set full width centering
				$htmlImageCentering = "";
				$fullWidthCentering = $slide->getParam("fullwidth_centering","false");
				if($sliderType == "fullwidth" && $fullWidthCentering == "true")
					$htmlImageCentering = ' data-fullwidthcentering="on"';
					
				//Set first item
				$currentItem = $this->slider->getCurrentItemSetting();
				
				$htmlParams = $htmlDuration.$htmlLink.$htmlThumb.$htmlDelay.$htmlRotation;
				
				$bgType = $slide->getParam("background_type","image");
				
				$styleImage = "";
				$urlImageTransparent = UniteBaseClassBanner::$url_plugin."images/transparent.png";
				
				switch($bgType) {
					case "trans":
						$urlSlideImage = $urlImageTransparent;
					break;
					case "solid":
						$urlSlideImage = $urlImageTransparent;
						$slideBGColor = $slide->getParam("slide_bg_color","#d0d0d0");
						$styleImage = "style='background-color:{$slideBGColor}'";
					break;
				}
				
				//Additional params
				$imageAddParams = "";
				$lazyLoad = $this->slider->getParam("lazyLoad","false");
				if($lazyLoad == "true") {
					$imageAddParams .= "data-lazyload=\"$urlSlideImage\"";
					$urlSlideImage = UniteBaseClassBanner::$url_plugin."images/dummy.png";
				}
				
				$imageAddParams .= $htmlImageCentering;
				
				//Html
				?>
					<li data-transition="<?php echo $transition?>" <?php echo $htmlParams?>>
						<img src="<?php echo $urlSlideImage?>" <?php echo $styleImage?> alt="<?php echo $alt?>" <?php echo $imageAddParams?>>
						<?php	//Put video
							if($fullWidthVideoData["found"] == true)	//Backward compatability
								$this->putFullWidthVideoLayer($fullWidthVideoData);
								
							$this->putCreativeLayer($slide)
						?>
					</li>
				<?php 
			}
			
			?>
				</ul>
			<?php
		}		
		
		//Get html5 layer html from data
		private function getHtml5LayerHtml($data) {			
			$urlPoster = UniteFunctionsBanner::getVal($data, "urlPoster");
			$urlMp4 = UniteFunctionsBanner::getVal($data, "urlMp4");
			$urlWebm = UniteFunctionsBanner::getVal($data, "urlWebm");
			$urlOgv = UniteFunctionsBanner::getVal($data, "urlOgv");
			$width = UniteFunctionsBanner::getVal($data, "width");
			$height = UniteFunctionsBanner::getVal($data, "height");
			
			$fullwidth = UniteFunctionsBanner::getVal($data, "fullwidth");
			$fullwidth = UniteFunctionsBanner::strToBool($fullwidth);
			
			if($fullwidth == true) {
				$width = "100%";
				$height = "100%";
			}
			
			$htmlPoster = "";
			if(!empty($urlPoster))
				$htmlPoster = "poster='{$urlPoster}'";
				
			$htmlMp4 = "";
			if(!empty($urlMp4))
				$htmlMp4 = "<source src='{$urlMp4}' type='video/mp4' />";

			$htmlWebm = "";
			if(!empty($urlWebm))
				$htmlWebm = "<source src='{$urlWebm}' type='video/webm' />";
				
			$htmlOgv = "";
			if(!empty($urlOgv))
				$htmlOgv = "<source src='{$urlOgv}' type='video/ogg' />";
					
			$html =	"<video class=\"video-js vjs-default-skin\" controls preload=\"none\" width=\"{$width}\" height=\"{$height}\" \n";
	   		$html .=  $htmlPoster ." data-setup=\"{}\"> \n";
	        $html .=  $htmlMp4."\n";
	        $html .=  $htmlWebm."\n";
	        $html .=  $htmlOgv."\n";
			$html .=  "</video>\n";
			
			return($html);
		}		
		
		//Put creative layer
		private function putCreativeLayer(BannerSlide $slide) {
			$layers = $slide->getLayers();
						
			if(empty($layers))
				return(false);
			?>
				<?php foreach($layers as $layer):
						
					$type = UniteFunctionsBanner::getVal($layer, "type","text");
					
					//Set if video full screen
					$isFullWidthVideo = false;
					if($type == "video") {
						$videoData = UniteFunctionsBanner::getVal($layer, "video_data");
						if(!empty($videoData)) {
							$videoData = (array)$videoData;
							$isFullWidthVideo = UniteFunctionsBanner::getVal($videoData, "fullwidth");
							$isFullWidthVideo = UniteFunctionsBanner::strToBool($isFullWidthVideo);
						}else
							$videoData = array();
					}
					
					
					$class = UniteFunctionsBanner::getVal($layer, "style");
					$animation = UniteFunctionsBanner::getVal($layer, "animation","fade");
					
					//Set output class
					$outputClass = "caption ". trim($class);
						$outputClass = trim($outputClass) . " ";
						
					$outputClass .= trim($animation);
					
					$left = UniteFunctionsBanner::getVal($layer, "left",0);
					$top = UniteFunctionsBanner::getVal($layer, "top",0);
					$speed = UniteFunctionsBanner::getVal($layer, "speed",300);
					$time = UniteFunctionsBanner::getVal($layer, "time",0);
					$easing = UniteFunctionsBanner::getVal($layer, "easing","easeOutExpo");
					$randomRotate = UniteFunctionsBanner::getVal($layer, "random_rotation","false");
					$randomRotate = UniteFunctionsBanner::boolToStr($randomRotate);
					
					$text = UniteFunctionsBanner::getVal($layer, "text");
					
					$htmlVideoAutoplay = "";
					$htmlVideoNextSlide = "";
					
					//Set html
					$html = "";
					switch($type) {
						default:
						case "text":						
							$html = $text;
							$html = do_shortcode($html);
						break;
						case "image":
							$urlImage = UniteFunctionsBanner::getVal($layer, "image_url");
							$html = '<img src="'.$urlImage.'" alt="'.$text.'">';
							$imageLink = UniteFunctionsBanner::getVal($layer, "link","");
							if(!empty($imageLink)) {
								$openIn = UniteFunctionsBanner::getVal($layer, "link_open_in","same");

								$target = "";
								if($openIn == "new")
									$target = ' target="_blank"';
									
								$html = '<a href="'.$imageLink.'"'.$target.'>'.$html.'</a>';
							}								
						break;
						case "video":
							$videoType = trim(UniteFunctionsBanner::getVal($layer, "video_type"));
							$videoID = trim(UniteFunctionsBanner::getVal($layer, "video_id"));
							$videoWidth = trim(UniteFunctionsBanner::getVal($layer, "video_width"));
							$videoHeight = trim(UniteFunctionsBanner::getVal($layer, "video_height"));	
							$videoArgs = trim(UniteFunctionsBanner::getVal($layer, "video_args"));
							
							if($isFullWidthVideo==true) {
								$videoWidth = "100%";
								$videoHeight = "100%";
							}
							
							$setBase = (is_ssl()) ? "https://" : "http://";
							
							switch($videoType) {
								case "youtube":
									if(empty($videoArgs))
										$videoArgs = GlobalsBannerRotator::DEFAULT_YOUTUBE_ARGUMENTS;
										
									$videoArgs .= ';origin='.$setBase.$_SERVER['SERVER_NAME'].';';
										
									$html = "<iframe src='http://www.youtube.com/embed/{$videoID}?{$videoArgs}' width='{$videoWidth}' height='{$videoHeight}' style='width:{$videoWidth}px;height:{$videoHeight}px;'></iframe>";
									
								break;
								case "vimeo":
									if(empty($videoArgs))
										$videoArgs = GlobalsBannerRotator::DEFAULT_VIMEO_ARGUMENTS;
										
									$html = "<iframe src='http://player.vimeo.com/video/{$videoID}?{$videoArgs}' width='{$videoWidth}' height='{$videoHeight}' style='width:{$videoWidth}px;height:{$videoHeight}px;'></iframe>";
								break;
								case "html5":
									$html = $this->getHtml5LayerHtml($videoData);									
								break;
								default:
									UniteFunctionsBanner::throwError("wrong video type: $videoType");
								break;
							}							
							
							//Set video autoplay, with backward compatability
							if(array_key_exists("autoplay", $videoData))
								$videoAutoplay = UniteFunctionsBanner::getVal($videoData, "autoplay");
							else	//Backward compatability
								$videoAutoplay = UniteFunctionsBanner::getVal($layer, "video_autoplay");
							
							$videoAutoplay = UniteFunctionsBanner::strToBool($videoAutoplay);
							
							if($videoAutoplay == true)
								$htmlVideoAutoplay = ' data-autoplay="true"';								
							
							$videoNextSlide = UniteFunctionsBanner::getVal($videoData, "nextslide");
							$videoNextSlide = UniteFunctionsBanner::strToBool($videoNextSlide);
							
							if($videoNextSlide == true)
								$htmlVideoNextSlide = ' data-nextslideatend="true"';								
								
						break;
					}
					
					//Handle end transitions
					$endTime = trim(UniteFunctionsBanner::getVal($layer, "endtime"));
					$htmlEnd = "";
					if(!empty($endTime)) {
						$htmlEnd = "data-end=\"$endTime\"";
						$endSpeed = trim(UniteFunctionsBanner::getVal($layer, "endspeed"));
						if(!empty($endSpeed))
							 $htmlEnd .= " data-endspeed=\"$endSpeed\"";
							 
						$endEasing = trim(UniteFunctionsBanner::getVal($layer, "endeasing"));
						if(!empty($endSpeed) && $endEasing != "nothing")
							 $htmlEnd .= " data-endeasing=\"$endEasing\"";
						
						//Add animation to class
						$endAnimation = trim(UniteFunctionsBanner::getVal($layer, "endanimation"));
						if(!empty($endAnimation) && $endAnimation != "auto")
							$outputClass .= " ".$endAnimation;	
					}
					
					//Slide link
					$htmlLink = "";
					$slideLink = UniteFunctionsBanner::getVal($layer, "link_slide");
					if(!empty($slideLink) && $slideLink != "nothing" && $slideLink != "scroll_under") {
						//Get slide index from id
						if(is_numeric($slideLink))
							$slideLink = UniteFunctionsBanner::getVal($this->slidesNumIndex, $slideLink);
						
						if(!empty($slideLink))
							$htmlLink = " data-linktoslide=\"$slideLink\"";
					}
					
					//Scroll under the slider
					if($slideLink == "scroll_under") {
						$outputClass .= " fb-scrollbelowslider";
						$scrollUnderOffset = UniteFunctionsBanner::getVal($layer, "scrollunder_offset");
						if(!empty($scrollUnderOffset))
							$htmlLink .= " data-scrolloffset=\"{$scrollUnderOffset}\"";
					}					
					
					//Hidden under resolution
					$htmlHidden = "";
					$layerHidden = UniteFunctionsBanner::getVal($layer, "hiddenunder");
					if($layerHidden == "true" || $layerHidden == "1")
						$htmlHidden = ' data-captionhidden="on"';
					
					$htmlParams = $htmlEnd.$htmlLink.$htmlVideoAutoplay.$htmlVideoNextSlide.$htmlHidden;
					
					//Set positioning options					
					$alignHor = UniteFunctionsBanner::getVal($layer,"align_hor","left");
					$alignVert = UniteFunctionsBanner::getVal($layer, "align_vert","top");
					
					$htmlPosX = "";
					$htmlPosY = "";
					switch($alignHor) {
						default:
						case "left":
							$htmlPosX = "data-x=\"{$left}\" \n";
						break;
						case "center":
							$htmlPosX = "data-x=\"center\" data-hoffset=\"{$left}\" \n";
						break;
						case "right":
							$left = (int)$left*-1;
							$htmlPosX = "data-x=\"right\" data-hoffset=\"{$left}\" \n";
						break;
					}
					
					switch($alignVert) {
						default:
						case "top":
							$htmlPosY = "data-y=\"{$top}\" ";
						break;
						case "middle":
							$htmlPosY = "data-y=\"center\" data-voffset=\"{$top}\" ";
						break;
						case "bottom":
							$top = (int)$top*-1;
							$htmlPosY = "data-y=\"bottom\" data-voffset=\"{$top}\" ";
						break;						
					}
					
					//Set corners
					$htmlCorners = "";
					
					if($type == "text") {
						$cornerLeft = UniteFunctionsBanner::getVal($layer, "corner_left");
						$cornerRight = UniteFunctionsBanner::getVal($layer, "corner_right");
						switch($cornerLeft) {
							case "curved":
								$htmlCorners .= "<div class='frontcorner'></div>";
							break;
							case "reverced":
								$htmlCorners .= "<div class='frontcornertop'></div>";							
							break;
						}
						
						switch($cornerRight) {
							case "curved":
								$htmlCorners .= "<div class='backcorner'></div>";
							break;
							case "reverced":
								$htmlCorners .= "<div class='backcornertop'></div>";							
							break;
						}
					
					//Add resizeme class
					$resizeme = UniteFunctionsBanner::getVal($layer, "resizeme");
					if($resizeme == "true" || $resizeme == "1")
						$outputClass .= ' fb-resizeme';
						
					}
					
					//Make some modifications for the full screen video
					if($isFullWidthVideo == true) {
						$htmlPosX = "data-x=\"0\""."\n";
						$htmlPosY = "data-y=\"0\""."\n";
						$outputClass .= " fullscreenvideo";
					}
					
				?>
				<div class="<?php echo $outputClass?>"
					 <?php echo $htmlPosX?>
					 <?php echo $htmlPosY?>
					 data-speed="<?php echo $speed?>" 
					 data-start="<?php echo $time?>" 
					 data-easing="<?php echo $easing?>" <?php echo $htmlParams?> ><?php echo $html?>
					 <?php echo $htmlCorners?>
					 </div>
				
				<?php 
				
			endforeach;
		}
		
		//Put slider javascript
		private function putJS() {			
			$params = $this->slider->getParams();
			$sliderType = $this->slider->getParam("sliderType");
			$fullWidth = ($sliderType=="fullwidth") ? "true" : "false";
			
			$fullScreen = "false";
			if($sliderType=="fullscreen") {
				$fullWidth = "true";
				$fullScreen = "true";
			}
			
			$noConflict = $this->slider->getParam("jquery_noconflict","true");
			
			//Set thumb amount
			$numSlides = $this->slider->getNumSlides(true);
			$thumbAmount = (int)$this->slider->getParam("thumb_amount","5");
			if($thumbAmount > $numSlides) {
				$thumbAmount = $numSlides;
			}
			
			//Get stop slider options
			$stopSlider = $this->slider->getParam("stopSlider","false");
			$stopAfterLoops = $this->slider->getParam("stopAfterLoops","0");
			$stopAtSlide = $this->slider->getParam("stopAtSlide","3");
			 
			if($stopSlider=="false") {
				$stopAfterLoops = "-1";
				$stopAtSlide = "-1";
			}
			
			//Slider ID
			$sliderID = $this->slider->getID();
			
			//Current item
			$currentItem = $this->slider->getCurrentItemSetting();
			
			//VideoJS path
	 	   	$videoJsPath = UniteBaseClassBanner::$url_plugin."videojs/";		
			?>
			
			<script type="text/javascript">
				<?php if($noConflict == "true"):?>
					jQuery.noConflict();
				<?php endif;?>
				
				var bannerapi<?php echo $sliderID?>;
				
				jQuery(document).ready(function() {				
					if (jQuery.fn.cssOriginal != undefined) {
						jQuery.fn.css = jQuery.fn.cssOriginal;
					}
				
					if(jQuery('#<?php echo $this->sliderHtmlID?>').bannerRotator == undefined) {
						bannerrotator_showDoubleJqueryError('#<?php echo $this->sliderHtmlID?>');
					} else {
					   bannerapi<?php echo $sliderID?> = jQuery('#<?php echo $this->sliderHtmlID?>').show().bannerRotator({
							startWidth:<?php echo $this->slider->getParam("width","1170")?>,
							startHeight:<?php echo $this->slider->getParam("height","500")?>,
							
							//Transition
							delay:<?php echo $this->slider->getParam("delay","5000",BannerRotator::FORCE_NUMERIC)?>,			
							
							//Lazy load
							lazyLoad:<?php echo $this->slider->getParam("lazyLoad","false")?>,						
							
							//Current item
							currentItem:<?php echo $currentItem?>,
							
							fullWidth:<?php echo $fullWidth?>,					   
							
							//Full screen
							fullScreen:<?php echo $fullScreen?>,
							fullScreenOffsetContainer:"<?php echo $this->slider->getParam("fullScreenOffsetContainer","")?>",			
	
							//Video
							videoJsPath:"<?php echo $videoJsPath?>"						
						});
					}
				});				
			</script>
			
			<?php			
		}		
		
		//Put inline error message in a box.
		public function putErrorMessage($message) {
			?>
			<div style="width:800px;height:300px;margin-bottom:10px;border:1px solid black;margin:0px auto;">
				<div style="padding-left:20px;padding-right:20px;line-height:1.5;padding-top:40px;color:red;font-size:16px;text-align:left;">
					<?php _e("Banner Rotator Error",BANNERROTATOR_TEXTDOMAIN)?>: <?php echo $message?> 
				</div>
			</div>
			<?php 
		}
		
		//Fill the responsitive slider values for further output
		private function getResponsitiveValues() {
			$sliderWidth = (int)$this->slider->getParam("width");
			$sliderHeight = (int)$this->slider->getParam("height");
			
			$percent = $sliderHeight / $sliderWidth;
			
			$w1 = (int) $this->slider->getParam("responsitive_w1",0);
			$w2 = (int) $this->slider->getParam("responsitive_w2",0);
			$w3 = (int) $this->slider->getParam("responsitive_w3",0);
			$w4 = (int) $this->slider->getParam("responsitive_w4",0);
			$w5 = (int) $this->slider->getParam("responsitive_w5",0);
			$w6 = (int) $this->slider->getParam("responsitive_w6",0);
			
			$sw1 = (int) $this->slider->getParam("responsitive_sw1",0);
			$sw2 = (int) $this->slider->getParam("responsitive_sw2",0);
			$sw3 = (int) $this->slider->getParam("responsitive_sw3",0);
			$sw4 = (int) $this->slider->getParam("responsitive_sw4",0);
			$sw5 = (int) $this->slider->getParam("responsitive_sw5",0);
			$sw6 = (int) $this->slider->getParam("responsitive_sw6",0);
			
			$arrItems = array();
			
			//Add main item
			$arr = array();				
			$arr["maxWidth"] = -1;
			$arr["minWidth"] = $w1;
			$arr["sliderWidth"] = $sliderWidth;
			$arr["sliderHeight"] = $sliderHeight;
			$arrItems[] = $arr;
			
			//Add item 1
			if(empty($w1))
				return($arrItems);
				
			$arr = array();				
			$arr["maxWidth"] = $w1-1;
			$arr["minWidth"] = $w2;
			$arr["sliderWidth"] = $sw1;
			$arr["sliderHeight"] = floor($sw1 * $percent);
			$arrItems[] = $arr;
			
			//Add item 2
			if(empty($w2))
				return($arrItems);
			
			$arr["maxWidth"] = $w2-1;
			$arr["minWidth"] = $w3;
			$arr["sliderWidth"] = $sw2;
			$arr["sliderHeight"] = floor($sw2 * $percent);
			$arrItems[] = $arr;
			
			//Add item 3
			if(empty($w3))
				return($arrItems);
			
			$arr["maxWidth"] = $w3-1;
			$arr["minWidth"] = $w4;
			$arr["sliderWidth"] = $sw3;
			$arr["sliderHeight"] = floor($sw3 * $percent);
			$arrItems[] = $arr;
			
			//Add item 4
			if(empty($w4))
				return($arrItems);
			
			$arr["maxWidth"] = $w4-1;
			$arr["minWidth"] = $w5;
			$arr["sliderWidth"] = $sw4;
			$arr["sliderHeight"] = floor($sw4 * $percent);
			$arrItems[] = $arr;

			//Add item 5
			if(empty($w5))
				return($arrItems);
			
			$arr["maxWidth"] = $w5-1;
			$arr["minWidth"] = $w6;
			$arr["sliderWidth"] = $sw5;
			$arr["sliderHeight"] = floor($sw5 * $percent);
			$arrItems[] = $arr;
			
			//Add item 6
			if(empty($w6))
				return($arrItems);
			
			$arr["maxWidth"] = $w6-1;
			$arr["minWidth"] = 0;
			$arr["sliderWidth"] = $sw6;
			$arr["sliderHeight"] = floor($sw6 * $percent);
			$arrItems[] = $arr;
			
			return($arrItems);
		}		
		
		//Put responsitive inline styles
		private function putResponsitiveStyles() {

			$bannerWidth = $this->slider->getParam("width");
			$bannerHeight = $this->slider->getParam("height");
			
			$arrItems = $this->getResponsitiveValues();
			
			?>
			<style type='text/css'>
				#<?php echo $this->sliderHtmlID?>, #<?php echo $this->sliderHtmlID_wrapper?> { width:<?php echo $bannerWidth?>px; height:<?php echo $bannerHeight?>px;}
			<?php
			foreach($arrItems as $item):			
				$strMaxWidth = "";
				
				if($item["maxWidth"] >= 0)
					$strMaxWidth = "and (max-width: {$item["maxWidth"]}px)";
				
			?>
			
			   @media only screen and (min-width: <?php echo $item["minWidth"]?>px) <?php echo $strMaxWidth?> {
			 		  #<?php echo $this->sliderHtmlID?>, #<?php echo $this->sliderHtmlID_wrapper?> { width:<?php echo $item["sliderWidth"]?>px; height:<?php echo $item["sliderHeight"]?>px;}	
			   }
			
			<?php 
			endforeach;
			echo "</style>";
		}
		
		//Modify slider settings for preview mode
		private function modifyPreviewModeSettings() {
			$params = $this->slider->getParams();
			$params["js_to_body"] = "false";
			
			$this->slider->setParams($params);
		}		
		
		//put html slider on the html page.
		//@param $data - mixed, can be ID ot Alias.
		public function putSliderBase($sliderID) {			
			try{
				self::$sliderSerial++;
				
				$this->slider = new BannerRotator();
				$this->slider->initByMixed($sliderID);
				
				//Modify settings for admin preview mode
				if($this->previewMode == true)
					$this->modifyPreviewModeSettings();
				
				//Set slider language
				$isWpmlExists = UniteWpmlBanner::isWpmlExists();
				$useWpml = $this->slider->getParam("useWpml","false");
				if($isWpmlExists && $useWpml=="true") {
					if($this->previewMode == false)
						$this->sliderLang = UniteFunctionsWPBanner::getCurrentLangCode();
				}
				
				//Edit html before slider
				$htmlBeforeSlider = "";
				if($this->slider->getParam("loadGoogleFont","false") == "true") {
					$googleFont = $this->slider->getParam("googleFont");
					$htmlBeforeSlider = BannerOperations::getCleanFontImport($googleFont);
				}
				
				//Put js to body handle
				if($this->slider->getParam("js_to_body","false") == "true") {
					$urlIncludeJS1 = UniteBaseClassBanner::$url_plugin."js/jquery.flashblue-plugins.js";
					$urlIncludeJS2 = UniteBaseClassBanner::$url_plugin."js/jquery.banner-rotator.js";
					$htmlBeforeSlider .= "<script type='text/javascript' src='../inc_php/$urlIncludeJS1'></script>";
					$htmlBeforeSlider .= "<script type='text/javascript' src='../inc_php/$urlIncludeJS2'></script>";
				}
				
				//The initial id can be alias
				$sliderID = $this->slider->getID();
				
				$bannerWidth = $this->slider->getParam("width",null,BannerRotator::VALIDATE_NUMERIC,"Slider Width");
				$bannerHeight = $this->slider->getParam("height",null,BannerRotator::VALIDATE_NUMERIC,"Slider Height");
				
				$sliderType = $this->slider->getParam("sliderType");
				
				//Slider id
				$this->sliderHtmlID = "banner_rotator_".$sliderID."_".self::$sliderSerial;
				
				//Slider wrapper
				$this->sliderHtmlID_wrapper = $this->sliderHtmlID."_wrapper";
				
				$containerStyle = "";
				
				$sliderPosition = $this->slider->getParam("position","center");
				
				//Set position
				if($sliderType != "fullscreen") {					
					switch($sliderPosition) {
						case "center":
						default:
							$containerStyle .= "margin:0px auto;";
						break;
						case "left":
							$containerStyle .= "float:left;";
						break;
						case "right":
							$containerStyle .= "float:right;";
						break;
					}					
				}
					
				//Add background color
				$backgrondColor = trim($this->slider->getParam("backgroundColor"));
				if(!empty($backgrondColor))
					$containerStyle .= "background-color:$backgrondColor;";
				
				//Set padding			
				$containerStyle .= "padding:".$this->slider->getParam("padding","0")."px;";
				
				//Set margin
				if($sliderType!="fullscreen") {									
					if($sliderPosition != "center") {
						$containerStyle .= "margin-left:".$this->slider->getParam("marginLeft","0")."px;"
										 . "margin-right:".$this->slider->getParam("marginRight","0")."px;";
					}
					
					$containerStyle .= "margin-top:".$this->slider->getParam("marginTop","0")."px;"
									 . "margin-bottom:".$this->slider->getParam("marginBottom","0")."px;";
				}
				
				//Set height and width
				$bannerStyle = "display:none;";	
				
				//Add background image (to banner style)
				$showBackgroundImage = $this->slider->getParam("showBackgroundImage","false");
				if($showBackgroundImage == "true") {					
					$backgroundImage = $this->slider->getParam("backgroundImage");					
					if(!empty($backgroundImage))
						$bannerStyle .= "background-image:url($backgroundImage);background-repeat:no-repeat;";
				}
				
				//Set wrapper and slider class
				$sliderWrapperClass = "banner-rotator-wrapper";
				$sliderClass = "banner-rotator";
				
				$putResponsiveStyles = false;
				
				switch($sliderType) {
					default:
					case "fixed":
						$bannerStyle .= "height:{$bannerHeight}px;width:{$bannerWidth}px;";
						$containerStyle .= "height:{$bannerHeight}px;width:{$bannerWidth}px;";
					break;
					case "responsitive":
						$putResponsiveStyles = true;						
					break;
					case "fullwidth":
						$sliderWrapperClass .= " fullwidthbanner-container";
						$sliderClass .= " fullwidthbanner";
						$bannerStyle .= "max-height:{$bannerHeight}px;height:{$bannerHeight};";
						$containerStyle .= "max-height:{$bannerHeight}px;";						
					break;
					case "fullscreen":
						$sliderWrapperClass .= " fullscreen-container";
						$sliderClass .= " fullscreenbanner";
					break;
				}
				
				//Check inner / outer border
				$paddingType = $this->slider->getParam("padding_type","outter");
				if($paddingType == "inner") $sliderWrapperClass .= " tp_inner_padding"; 
				
				global $bannerRotatorVersion;				
				?>
				
				<!-- START BANNER ROTATOR <?php echo $bannerRotatorVersion?> <?php echo $sliderType?> mode -->
				
				<?php 
					if($putResponsiveStyles == true)
						$this->putResponsitiveStyles(); ?>
				
				<?php echo $htmlBeforeSlider?>
				<div id="<?php echo $this->sliderHtmlID_wrapper?>" class="<?php echo $sliderWrapperClass?>" style="<?php echo $containerStyle?>">
					<div id="<?php echo $this->sliderHtmlID ?>" class="<?php echo $sliderClass?>" style="<?php echo $bannerStyle?>">						
						<?php $this->putSlides()?>
					</div>
				</div>				
				<?php 
				
				$this->putJS();
				?>
				<!-- END BANNER ROTATOR -->
				<?php 
				
			}catch(Exception $e) {
				$message = $e->getMessage();
				$this->putErrorMessage($message);
			}
			
		}		
		
	}
?>