<?php
	class BannerSlide extends UniteElementsBaseBanner {
		
		private $id;
		private $sliderID;
		private $slideOrder;
		
		private $imageUrl;
		private $imageID;		
		private $imageThumb;		
		private $imageFilepath;
		private $imageFilename;
		
		private $params;
		private $arrLayers;
		
		public function __construct() {
			parent::__construct();
		}
		
		//Init slide by db record
		public function initByData($record) {			
			$this->id = $record["id"];
			$this->sliderID = $record["slider_id"];
			$this->slideOrder = $record["slide_order"];
			
			$params = $record["params"];
			$params = (array)json_decode($params);
			
			$layers = $record["layers"];
			$layers = (array)json_decode($layers);
			$layers = UniteFunctionsBanner::convertStdClassToArray($layers);

			$imageID = UniteFunctionsBanner::getVal($params, "image_id");
			
			//Get image url and thumb url
			if(!empty($imageID)) {
				$this->imageID = $imageID;
				
				$imageUrl = UniteFunctionsWPBanner::getUrlAttachmentImage($imageID);
				if(empty($imageUrl))
					$imageUrl = UniteFunctionsBanner::getVal($params, "image");
				
				$this->imageThumb = UniteFunctionsWPBanner::getUrlAttachmentImage($imageID,UniteFunctionsWPBanner::THUMB_MEDIUM);
				
			} else {
				$imageUrl = UniteFunctionsBanner::getVal($params, "image");
			}
			
			//Set image path, file and url
			$this->imageUrl = $imageUrl;
			
			$this->imageFilepath = UniteFunctionsWPBanner::getImagePathFromURL($this->imageUrl);
		    $realPath = UniteFunctionsWPBanner::getPathContent().$this->imageFilepath;
		    
		    if(file_exists($realPath) == false || is_file($realPath) == false)
		    	$this->imageFilepath = "";
		    
			$this->imageFilename = basename($this->imageUrl);
			
			$this->params = $params;
			$this->arrLayers = $layers;	
		}		
		
		//Init the slider by id
		public function initByID($slideid) {
			UniteFunctionsBanner::validateNumeric($slideid,"Slide ID");
			$slideid = $this->db->escape($slideid);
			$record = $this->db->fetchSingle(GlobalsBannerRotator::$table_slides,"id=$slideid");
			
			$this->initByData($record);
		}
		
		//Set children array
		public function setArrChildren($arrChildren) {
			$this->arrChildren = $arrChildren;
		}
		
		//Get children array
		public function getArrChildren() {			
			$this->validateInited();
			
			if($this->arrChildren === null) {
				$slider = new BannerRotator();
				$slider->initByID($this->sliderID);
				$this->arrChildren = $slider->getArrSlideChildren($this->id);
			}
			
			return($this->arrChildren);				
		}
		
		//Return if the slide is parent slide
		public function isParent() {
			$parentID = $this->getParam("parentid","");
			return(!empty($parentID));
		}
		
		//Get slide language
		public function getLang() {
			$lang = $this->getParam("lang","all");
			return($lang);
		}
		
		//Return parent slide. If the slide is parent, return this slide.
		public function getParentSlide() {
			$parentID = $this->getParam("parentid","");
			if(empty($parentID))
				return($this);
				
			$parentSlide = new BannerSlide();
			$parentSlide->initByID($parentID);
			return($parentSlide);
		}
		
		//Get array of children ids
		public function getArrChildrenIDs() {
			$arrChildren = $this->getArrChildren();
			$arrChildrenIDs = array();
			foreach($arrChildren as $child) {
				$childID = $child->getID();
				$arrChildrenIDs[] = $childID;
			}
			
			return($arrChildrenIDs);
		}
		
		//Get array of children array and languages, the first is current language
		public function getArrChildrenLangs($includeParent = true) {			
			$this->validateInited();
			$slideID = $this->id;
			
			if($includeParent == true) {
				$lang = $this->getParam("lang","all");
				$arrOutput = array();
				$arrOutput[] = array("slideid"=>$slideID,"lang"=>$lang,"isparent"=>true);
			}
			
			$arrChildren = $this->getArrChildren();
			
			foreach($arrChildren as $child) {
				$childID = $child->getID();
				$childLang = $child->getParam("lang","all");
				$arrOutput[] = array("slideid"=>$childID,"lang"=>$childLang,"isparent"=>false);
			}
			
			return($arrOutput);
		}
		
		//Get children language codes (including current slide lang code)
		public function getArrChildLangCodes($includeParent = true) {
			$arrLangsWithSlideID = $this->getArrChildrenLangs($includeParent);
			$arrLangCodes = array();
			foreach($arrLangsWithSlideID as $item) {
				$lang = $item["lang"];
				$arrLangCodes[$lang] = $lang;
			}
			
			return($arrLangCodes);
		}
		
		//Get slide ID
		public function getID() {
			return($this->id);
		}		
		
		//Get slide order
		public function getOrder() {
			$this->validateInited();
			return($this->slideOrder);
		}		
		
		//Get layers in json format
		public function getLayers() {
			$this->validateInited();
			return($this->arrLayers);
		}
		
		//Modify layer links for export
		public function getLayersForExport() {
			$this->validateInited();
			$arrLayersNew = array();
			foreach($this->arrLayers as $key=>$layer) {
				$imageUrl = UniteFunctionsBanner::getVal($layer, "image_url");
				if(!empty($imageUrl))
					$layer["image_url"] = UniteFunctionsWPBanner::getImagePathFromURL($layer["image_url"]);
					
				$arrLayersNew[] = $layer;
			}
			
			return($arrLayersNew);
		}
		
		//Get params for export
		public function getParamsForExport() {
			$arrParams = $this->getParams();
			$urlImage = UniteFunctionsBanner::getVal($arrParams, "image");
			if(!empty($urlImage))
				$arrParams["image"] = UniteFunctionsWPBanner::getImagePathFromURL($urlImage);
			
			return($arrParams);
		}		
		
		//Normalize layers text, and get layers
		public function getLayersNormalizeText() {
			$arrLayersNew = array();
			foreach ($this->arrLayers as $key=>$layer) {
				$text = $layer["text"];
				$text = addslashes($text);
				$layer["text"] = $text;
				$arrLayersNew[] = $layer;
			}
			
			return($arrLayersNew);
		}		

		//Get slide params
		public function getParams() {
			$this->validateInited();
			return($this->params);
		}
		
		//Get parameter from params array. if no default, then the param is a must!
		function getParam($name,$default=null) {			
			if($default === null) {
				if(!array_key_exists($name, $this->params))
					UniteFunctionsBanner::throwError("The param <b>$name</b> not found in slide params.");
				$default = "";
			}
				
			return UniteFunctionsBanner::getVal($this->params, $name,$default);
		}		
		
		//Get image filename
		public function getImageFilename() {
			return($this->imageFilename);
		}		
		
		//Get image filepath
		public function getImageFilepath() {
			return($this->imageFilepath);
		}		
		
		//Get image url
		public function getImageUrl() {
			return($this->imageUrl);
		}		
		
		//Get image id
		public function getImageID() {
			return($this->imageID);
		}
		
		//Get thumb url
		public function getThumbUrl() {
			$thumbUrl = $this->imageUrl;
			if(!empty($this->imageThumb))
				$thumbUrl = $this->imageThumb;
				
			return($thumbUrl);
		}		
		
		//Get the slider id
		public function getSliderID() {
			return($this->sliderID);
		}
		
		//Validate that the slider exists
		private function validateSliderExists($sliderID) {
			$slider = new BannerRotator();
			$slider->initByID($sliderID);
		}
		
		//Validate that the slide is inited and the id exists.
		private function validateInited() {
			if(empty($this->id))
				UniteFunctionsBanner::throwError("The slide is not inited!!!");
		}		
		
		//Create the slide (from image)
		public function createSlide($sliderID,$obj="") {			
			$imageID = null;
			
			if(is_array($obj)) {
				$urlImage = UniteFunctionsBanner::getVal($obj, "url");
				$imageID = UniteFunctionsBanner::getVal($obj, "id");
			}else{
				$urlImage = $obj;
			}
			
			//Get max order
			$slider = new BannerRotator();
			$slider->initByID($sliderID);
			$maxOrder = $slider->getMaxOrder();
			$order = $maxOrder+1;
			
			$params = array();
			if(!empty($urlImage)) {
				$params["background_type"] = "image";
				$params["image"] = $urlImage;
				if(!empty($imageID))
					$params["image_id"] = $imageID;
					
			} else {	
				//Create transparent slide				
				$params["background_type"] = "trans";
			}
			
			$jsonParams = json_encode($params);
			
			$arrInsert = array("params"=>$jsonParams,
			           		   "slider_id"=>$sliderID,
								"slide_order"=>$order,
								"layers"=>""
						);
			
			$slideID = $this->db->insert(GlobalsBannerRotator::$table_slides, $arrInsert);
			
			return($slideID);
		}
		
		//Update slide image from data
		public function updateSlideImageFromData($data) {			
			$slideID = UniteFunctionsBanner::getVal($data, "slide_id");			
			$this->initByID($slideID);
			
			$urlImage = UniteFunctionsBanner::getVal($data, "url_image");
			UniteFunctionsBanner::validateNotEmpty($urlImage);
			$imageID = UniteFunctionsBanner::getVal($data, "image_id");
			
			$arrUpdate = array();
			$arrUpdate["image"] = $urlImage;			
			$arrUpdate["image_id"] = $imageID;
			
			$this->updateParamsInDB($arrUpdate);
			
			return($urlImage);
		}
		
		//Update slide parameters in db
		private function updateParamsInDB($arrUpdate) {			
			$this->validateInited();		
			$this->params = array_merge($this->params,$arrUpdate);
			$jsonParams = json_encode($this->params);
			
			$arrDBUpdate = array("params"=>$jsonParams);
			
			$this->db->update(GlobalsBannerRotator::$table_slides,$arrDBUpdate,array("id"=>$this->id));
		}
		
		//Update parent slideID 
		public function updateParentSlideID($parentID) {
			$arrUpdate = array();
			$arrUpdate["parentid"] = $parentID;
			$this->updateParamsInDB($arrUpdate);
		}
		
		//Sort layers by order
		private function sortLayersByOrder($layer1,$layer2) {
			$layer1 = (array)$layer1;
			$layer2 = (array)$layer2;
			
			$order1 = UniteFunctionsBanner::getVal($layer1, "order",1);
			$order2 = UniteFunctionsBanner::getVal($layer2, "order",2);
			if($order1 == $order2)
				return(0);
			
			return($order1 > $order2);
		}		
		
		//Go through the layers and fix small bugs if exists
		private function normalizeLayers($arrLayers) {			
			usort($arrLayers,array($this,"sortLayersByOrder"));
			
			$arrLayersNew = array();
			foreach ($arrLayers as $key=>$layer) {
				
				$layer = (array)$layer;
				
				//Set type
				$type = UniteFunctionsBanner::getVal($layer, "type","text");
				$layer["type"] = $type;
				
				//Normalize position:
				$layer["left"] = round($layer["left"]);
				$layer["top"] = round($layer["top"]);
				
				//Unset order
				unset($layer["order"]);
				
				//Modify text
				$layer["text"] = stripcslashes($layer["text"]);
				
				$arrLayersNew[] = $layer;
			}
			
			return($arrLayersNew);
		}  		
		
		//Normalize params
		private function normalizeParams($params) {			
			$urlImage = UniteFunctionsBanner::getVal($params, "image_url");
			
			//Init the id if absent
			$params["image_id"] = UniteFunctionsBanner::getVal($params, "image_id");
			
			$params["image"] = $urlImage;
			unset($params["image_url"]);
			
			if(isset($params["video_description"]))
				$params["video_description"] = UniteFunctionsBanner::normalizeTextareaContent($params["video_description"]);
			
			return($params);
		}		
		
		//Update slide from data
		public function updateSlideFromData($data) {			
			$slideID = UniteFunctionsBanner::getVal($data, "slideid");
			$this->initByID($slideID);
			
			//Treat params
			$params = UniteFunctionsBanner::getVal($data, "params");
			$params = $this->normalizeParams($params);
			
			//Preserve old data that not included in the given data
			$params = array_merge($this->params,$params);
			
			//Treat layers
			$layers = UniteFunctionsBanner::getVal($data, "layers");
			
			if(gettype($layers) == "string"){
				$layersStrip = stripslashes($layers);
				$layersDecoded = json_decode($layersStrip);
				if(empty($layersDecoded))
					$layersDecoded = json_decode($layers);
				
				$layers = UniteFunctionsBanner::convertStdClassToArray($layersDecoded);
			}
			
			if(empty($layers) || gettype($layers) != "array")
				$layers = array();
			
			$layers = $this->normalizeLayers($layers);
			
			$arrUpdate = array();
			$arrUpdate["layers"] = json_encode($layers);
			$arrUpdate["params"] = json_encode($params);
			
			$this->db->update(GlobalsBannerRotator::$table_slides,$arrUpdate,array("id"=>$this->id));
		}	
		
		//Delete slide by slideid
		public function deleteSlide() {
			$this->validateInited();
			
			$this->db->delete(GlobalsBannerRotator::$table_slides,"id='{$this->id}'");
		}
		
		
		//Delete slide children
		public function deleteChildren() {
			$this->validateInited();
			$arrChildren = $this->getArrChildren();
			foreach($arrChildren as $child)
				$child->deleteSlide();
		}		
		
		//Delete slide from data
		public function deleteSlideFromData($data) {
			$slideID = UniteFunctionsBanner::getVal($data, "slideID");
			$this->initByID($slideID);
			$this->deleteChildren();
			$this->deleteSlide();
		}
		
		//Set params from client
		public function setParams($params) {
			$params = $this->normalizeParams($params);
			$this->params = $params;
		}
		
		//Set layers from client
		public function setLayers($layers) {
			$layers = $this->normalizeLayers($layers);
			$this->arrLayers = $layers;
		}		
		
		//Toggle slide state from data
		public function toggleSlideStateFromData($data) {			
			$slideID = UniteFunctionsBanner::getVal($data, "slide_id");
			$this->initByID($slideID);
			
			$state = $this->getParam("state","published");
			$newState = ($state == "published")?"unpublished":"published";
			
			$arrUpdate = array();
			$arrUpdate["state"] = $newState;
			
			$this->updateParamsInDB($arrUpdate);
			
			return($newState);
		}	
		
		//Updatye slide language from data
		private function updateLangFromData($data) {						
			$slideID = UniteFunctionsBanner::getVal($data, "slideid");
			$this->initByID($slideID);
			
			$lang = UniteFunctionsBanner::getVal($data, "lang");
			
			$arrUpdate = array();
			$arrUpdate["lang"] = $lang;
			$this->updateParamsInDB($arrUpdate);
			
			$response = array();
			$response["url_icon"] = UniteWpmlBanner::getFlagUrl($lang);
			$response["title"] = UniteWpmlBanner::getLangTitle($lang);
			$response["operation"] = "update";
			
			return($response);
		}		
		
		//Add language (add slide that connected to current slide) from data
		private function addLangFromData($data) {
			$sliderID = UniteFunctionsBanner::getVal($data, "sliderid");
			$slideID = UniteFunctionsBanner::getVal($data, "slideid");
			$lang = UniteFunctionsBanner::getVal($data, "lang");
			
			//duplicate slide
			$slider = new BannerRotator();
			$slider->initByID($sliderID);
			$newSlideID = $slider->duplicateSlide($slideID);
					
			//update new slide
			$this->initByID($newSlideID);
			
			$arrUpdate = array();
			$arrUpdate["lang"] = $lang;
			$arrUpdate["parentid"] = $slideID;
			$this->updateParamsInDB($arrUpdate);
						
			$urlIcon = UniteWpmlBanner::getFlagUrl($lang);
			$title = UniteWpmlBanner::getLangTitle($lang);
			
			$newSlide = new BannerSlide();
			$newSlide->initByID($slideID);
			$arrLangCodes = $newSlide->getArrChildLangCodes();
			$isAll = UniteWpmlBanner::isAllLangsInArray($arrLangCodes);
			
			$html = "<li>
						<img id=\"icon_lang_{$newSlideID}\" class=\"icon_slide_lang\" src=\"{$urlIcon}\" title=\"{$title}\" data-slideid=\"{$newSlideID}\" data-lang=\"{$lang}\">
						<div class=\"icon_lang_loader loader_round\" style=\"display:none\"></div>								
					</li>";
			
			$response = array();
			$response["operation"] = "add";
			$response["isAll"] = $isAll;
			$response["html"] = $html;
			
			return($response);
		}		
		
		//Delete slide from language menu data
		private function deleteSlideFromLangData($data) {			
			$slideID = UniteFunctionsBanner::getVal($data, "slideid");
			$this->initByID($slideID);
			$this->deleteSlide();
			
			$response = array();
			$response["operation"] = "delete";
			return($response);
		}		
		
		//Add or update language from data
		public function doSlideLangOperation($data) {			
			$operation = UniteFunctionsBanner::getVal($data, "operation");
			switch($operation) {
				case "add":
					$response = $this->addLangFromData($data);	
				break;
				case "delete":
					$response = $this->deleteSlideFromLangData($data);
				break;
				case "update":
				default:
					$response = $this->updateLangFromData($data);
				break;
			}
			
			return($response);
		}	
		
	}	
?>