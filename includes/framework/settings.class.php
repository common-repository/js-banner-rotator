<?php
	class UniteSettingsBanner {
		
		const COLOR_OUTPUT_FLASH = "flash";
		const COLOR_OUTPUT_HTML = "html";		
		
		const RELATED_NONE = "";
		const TYPE_TEXT = "text";
		const TYPE_COLOR = "color";
		const TYPE_DATE = "date";
		const TYPE_SELECT = "list";
		const TYPE_CHECKBOX = "checkbox";
		const TYPE_RADIO = "radio";
		const TYPE_TEXTAREA = "textarea";
		const TYPE_STATIC_TEXT = "statictext";
		const TYPE_HR = "hr";
		const TYPE_CUSTOM = "custom";
		const ID_PREFIX = "";
		const TYPE_CONTROL = "control";
		const TYPE_BUTTON = "button";
		const TYPE_IMAGE = "image";
		const TYPE_CHECKLIST = "checklist";
		
		//Set data types  
		const DATATYPE_NUMBER = "number";
		const DATATYPE_STRING = "string";
		const DATATYPE_BOOLEAN = "boolean";
		
		const CONTROL_TYPE_ENABLE = "enable";
		const CONTROL_TYPE_DISABLE = "disable";
		const CONTROL_TYPE_SHOW = "show";
		const CONTROL_TYPE_HIDE = "hide";
		
		//Additional parameters that can be added to settings.
		const PARAM_TEXTSTYLE = "textStyle";		
		const PARAM_ADDPARAMS = "addparams";	//Additional text after the field
		const PARAM_ADDTEXT = "addtext";	//Additional text after the field
		const PARAM_ADDTEXT_BEFORE_ELEMENT = "addtext_before_element";	//Additional text after the field
		const PARAM_CELLSTYLE = "cellStyle";	//Additional text after the field
		
		//View defaults
		protected $defaultText = "Enter value";
		protected $sap_size = 5;
		
		//Other variables
		protected $HRIdCounter = 0;	//Counter of hr id
		
		protected $arrSettings = array();
		protected $arrSections = array();
		protected $arrIndex = array();	//Index of name->index of the settings.
		protected $arrSaps = array();
		
		//controls:
		protected $arrControls = array();		//Array of items that controlling others (hide/show or enabled/disabled) 
		protected $arrBulkControl = array();	//Bulk cotnrol array. if not empty, every settings will be connected with control
		 
		//Custom functions
		protected $customFunction_afterSections = null;
		protected $colorOutputType = self::COLOR_OUTPUT_HTML;
		
		//Constructor
	    public function __construct() {
	    	
	    }
		
		//Get where query according relatedTo and relatedID. 
		private function getWhereQuery() {
			$where = "relatedTo='".$this->relatedTo."' and relatedID='".$this->relatedID."'";
			return($where);
		}		
		
		//Set type of color output
		public function setColorOutputType($type) {
			$this->colorOutputType = $type;
		}
		
		//Set the related to/id for saving/restoring settings.
		public function setRelated($relatedTo,$relatedID) {
			$this->relatedTo = $relatedTo;
			$this->relatedID = $relatedID;
		}		
		
		//Modify the data before save
		private function modifySettingsData($arrSettings) {			
			foreach($arrSettings as $key=>$content) {
				switch(getType($content)) {
					case "string":
						//Replace the unicode line break (sometimes left after json)
						$content = str_replace("u000a","\n",$content);
						$content = str_replace("u000d","",$content);						
					break;
					case "object":
					case "array":
						$content = UniteFunctionsBanner::convertStdClassToArray($content);
					break;					
				}
				
				$arrSettings[$key] = $content;												
			}
			
			return($arrSettings);
		}				
				
		//Add the section value to the setting
		private function checkAndAddSectionAndSap($setting) {
			//Add section
			if(!empty($this->arrSections)) {
				$sectionKey = count($this->arrSections)-1;
				$setting["section"] = $sectionKey;
				$section = $this->arrSections[$sectionKey];
				$sapKey = count($section["arrSaps"])-1;
				$setting["sap"] = $sapKey;
			} else {
				//Please impliment add sap normal!!! - without sections
			}
			return($setting);
		}
		
		//Validate items parameter. throw exception on error
		private function validateParamItems($arrParams) {
			if(!isset($arrParams["items"])) throw new Exception("no select items presented");
			if(!is_array($arrParams["items"])) throw new Exception("the items parameter should be array");
			if(empty($arrParams["items"])) throw new Exception("the items array should not be empty");			
		}
		

		//Add this setting to index
		private function addSettingToIndex($name) {
			$this->arrIndex[$name] = count($this->arrSettings)-1;
		}
		
		//Get types array from all the settings:
		protected function getArrTypes() {
			$arrTypesAssoc = array();
			$arrTypes = array();
			foreach($this->arrSettings as $setting) {	
				$type = $setting["type"];
				if(!isset($arrTypesAssoc[$type])) $arrTypes[] = $type;
				$arrTypesAssoc[$type] = "";				
			}			
			return($arrTypes);
		}				

		//Get json client object for javascript
		public function getJsonClientString() {
			$arrSettingTypes = array();
			foreach($this->arrSettings as $setting) {
				if(isset($setting["name"]))
					$arrSettingTypes[$setting["name"]] = $setting["datatype"]; 
			}
			$strJson = json_encode($arrSettingTypes);
			return($strJson);
		}		
		
		//Get settings array
		public function getArrSettings() {
			return($this->arrSettings);
		}		
		
		//Get sections
		public function getArrSections() {
			return($this->arrSections);
		}		
		
		//Get controls
		public function getArrControls() {
			return($this->arrControls);
		}
		
		//Set settings array
		public function setArrSettings($arrSettings) {
			$this->arrSettings = $arrSettings;
		}		
		
		//Get number of settings
		public function getNumSettings() {
			$counter = 0;
			foreach($this->arrSettings as $setting) {
				switch($setting["type"]) {
					case self::TYPE_HR:
					case self::TYPE_STATIC_TEXT:
					break;
					default:
						$counter++;
					break;
				}
			}
			return($counter);
		}
		
		//Add radio group
		public function addRadio($name,$arrItems,$text = "",$defaultItem="",$arrParams = array()) {
			$params = array("items"=>$arrItems);
			$params = array_merge($params,$arrParams);
			$this->add($name,$defaultItem,$text,self::TYPE_RADIO,$params);
		}
		
		//Add textarea control
		public function addTextArea($name,$defaultValue,$text,$arrParams = array()) {
			$this->add($name,$defaultValue,$text,self::TYPE_TEXTAREA,$arrParams);
		}

		//Add button control
		public function addButton($name,$value,$arrParams = array()) {
			$this->add($name,$value,"",self::TYPE_BUTTON,$arrParams);
		}		
		
		//Add checkbox element
		public function addCheckbox($name,$defaultValue = false,$text = "",$arrParams = array()) {
			$this->add($name,$defaultValue,$text,self::TYPE_CHECKBOX,$arrParams);
		}
		
		//Add textbox element
		public function addTextBox($name,$defaultValue = "",$text = "",$arrParams = array()) {
			$this->add($name,$defaultValue,$text,self::TYPE_TEXT,$arrParams);
		}

		//Add image selector
		public function addImage($name,$defaultValue = "",$text = "",$arrParams = array()) {
			$this->add($name,$defaultValue,$text,self::TYPE_IMAGE,$arrParams);
		}
		
		//Add color picker setting
		public function addColorPicker($name,$defaultValue = "",$text = "",$arrParams = array()) {
			$this->add($name,$defaultValue,$text,self::TYPE_COLOR,$arrParams);
		}
		
		//Add date picker setting
		public function addDatePicker($name,$defaultValue = "",$text = "",$arrParams = array()) {
			$this->add($name,$defaultValue,$text,self::TYPE_DATE,$arrParams);
		}
		
		//Add custom setting
		public function addCustom($customType,$name,$defaultValue = "",$text = "",$arrParams = array()) {
			$params = array();
			$params["custom_type"] = $customType;
			$params = array_merge($params,$arrParams);
			
			$this->add($name,$defaultValue,$text,self::TYPE_CUSTOM,$params);
		}		
		
		//Add horizontal sap
		public function addHr($name="",$params=array()) {
			$setting = array();
			$setting["type"] = self::TYPE_HR;
			
			//Set item name
			$itemName = "";
			if($name != "") $itemName = $name;
			else{	//generate hr id
			  $this->HRIdCounter++;
			  $itemName = "hr".$this->HRIdCounter;
			}
			
			$setting["id"] = self::ID_PREFIX.$itemName;
			$setting["id_row"] = $setting["id"]."_row";
			
			//Add section and sap keys
			$setting = $this->checkAndAddSectionAndSap($setting);
			
			$this->checkAddBulkControl($itemName);
			
			$setting = array_merge($params,$setting);
			$this->arrSettings[] = $setting;
			
			//Add to settings index
			$this->addSettingToIndex($itemName);
		}
		
		//Add static text
		public function addStaticText($text,$name="",$params=array()) {
			$setting = array();
			$setting["type"] = self::TYPE_STATIC_TEXT;
			
			//Set item name
			$itemName = "";
			if($name != "") $itemName = $name;
			else{	//Generate hr id
			  $this->HRIdCounter++;
			  $itemName = "textitem".$this->HRIdCounter;
			}
			
			$setting["id"] = self::ID_PREFIX.$itemName;
			$setting["id_row"] = $setting["id"]."_row";
			$setting["text"] = $text;
			
			$this->checkAddBulkControl($itemName);
			
			$params = array_merge($params,$setting);
			
			//Add section and sap keys
			$setting = $this->checkAndAddSectionAndSap($setting);
			
			$this->arrSettings[] = $setting;
			
			//Add to settings index
			$this->addSettingToIndex($itemName);
		}

		//Add select setting
		public function addSelect($name,$arrItems,$text,$defaultItem="",$arrParams=array()) {
			$params = array("items"=>$arrItems);
			$params = array_merge($params,$arrParams);
			$this->add($name,$defaultItem,$text,self::TYPE_SELECT,$params);
		}	
		
		//Add select setting
		public function addChecklist($name,$arrItems,$text,$defaultItem="",$arrParams=array()) {
			$params = array("items"=>$arrItems);
			$params = array_merge($params,$arrParams);
			$this->add($name,$defaultItem,$text,self::TYPE_CHECKLIST,$params);
		}		
		
		//Add saporator
		public function addSap($text, $name="", $opened = false) {			
			if(empty($text))
				UniteFunctionsBanner::throwError("sap $name must have a text");
			
			//Create sap array
			$sap = array();
			$sap["name"] = $name; 
			$sap["text"] = $text; 
			
			if($opened == true) $sap["opened"] = true;
			
			//Add sap to current section
			if(!empty($this->arrSections)) {
				$lastSection = end($this->arrSections);
				$section_keys = array_keys($this->arrSections);
				$lastSectionKey = end($section_keys);
				$arrSaps = $lastSection["arrSaps"];
				$arrSaps[] = $sap;
				$this->arrSections[$lastSectionKey]["arrSaps"] = $arrSaps; 				
				$sap_keys = array_keys($arrSaps);
				$sapKey = end($sap_keys);
			}
			else{
				$this->arrSaps[] = $sap;
			}
		}
		
		//Get sap data
		public function getSap($sapKey,$sectionKey=-1) {
			//Get sap without sections:
			if($sectionKey == -1) return($this->arrSaps[$sapKey]);
			if(!isset($this->arrSections[$sectionKey])) throw new Exception("Sap on section:".$sectionKey." doesn't exists");
			$arrSaps = $this->arrSections[$sectionKey]["arrSaps"];
			if(!isset($arrSaps[$sapKey])) throw new Exception("Sap with key:".$sapKey." doesn't exists");
			$sap = $arrSaps[$sapKey];
			return($sap);
		}
		
		//Add a new section. Every settings from now on will be related to this section
		public function addSection($label,$name="") {
						
			if(!empty($this->arrSettings) && empty($this->arrSections))
				UniteFunctionsBanner::throwError("You should add first section before begin to add settings. (section: $text)");
				
			if(empty($label)) 
				UniteFunctionsBanner::throwError("You have some section without text");

			$arrSection = array(
				"text"=>$label,
				"arrSaps"=>array(),
				"name"=>$name
			);
			
			$this->arrSections[] = $arrSection;
		}
		
		//Add setting, may be in different type, of values
		protected function add($name,$defaultValue = "",$text = "",$type = self::TYPE_TEXT,$arrParams = array()) {			
			//Validation
			if(empty($name)) throw new Exception("Every setting should have a name!");
			
			switch($type) {
				case self::TYPE_RADIO:
				case self::TYPE_SELECT:
					$this->validateParamItems($arrParams);
				break;
				case self::TYPE_CHECKBOX:
					if(!is_bool($defaultValue)) throw new Exception("The checkbox value should be boolean");
				break;
			}
			
			//Validate name
			if(isset($this->arrIndex[$name])) throw new Exception("Duplicate setting name:".$name);
			
			$this->checkAddBulkControl($name);
						
			//Set defaults
			if($text == "") $text = $this->defaultText;
			
			$setting = array();
			$setting["name"] = $name;
			$setting["id"] = self::ID_PREFIX.$name;
			$setting["id_service"] = $setting["id"]."_service";
			$setting["id_row"] = $setting["id"]."_row";
			$setting["type"] = $type;
			$setting["text"] = $text;
			$setting["value"] = $defaultValue;

			//Set data type
			switch($setting["type"]) {
				case self::TYPE_COLOR:
				case self::TYPE_DATE:
					$dataType = self::DATATYPE_STRING;
				break;
				default:
					switch(getType($defaultValue)) {
						case "integer":							
						case "double":
							$dataType = self::DATATYPE_NUMBER;
						break;
						case "boolean":
							$dataType = self::DATATYPE_BOOLEAN;
						break;
						case "string":							
						default:
							$dataType = self::DATATYPE_STRING;
						break;
					}
				break;
			} 			
			$setting["datatype"] = $dataType;
			
			$setting = array_merge($setting,$arrParams);
			
			//Add section and sap keys
			$setting = $this->checkAndAddSectionAndSap($setting);
			
			$this->arrSettings[] = $setting;
			
			//Add to settings index
			$this->addSettingToIndex($name);
		}		
		
		//Add an item that controlling visibility of enabled/disabled of other.
		public function addControl($control_item_name,$controlled_item_name,$control_type,$value) {
			
			UniteFunctionsBanner::validateNotEmpty($control_item_name,"control parent");
			UniteFunctionsBanner::validateNotEmpty($controlled_item_name,"control child");
			UniteFunctionsBanner::validateNotEmpty($control_type,"control type");
			UniteFunctionsBanner::validateNotEmpty($value,"control value");
			
			$arrControl = array();
			if(isset($this->arrControls[$control_item_name]))
				 $arrControl = $this->arrControls[$control_item_name];
			$arrControl[] = array("name"=>$controlled_item_name,"type"=>$control_type,"value"=>$value);
			$this->arrControls[$control_item_name] = $arrControl;
		}
		
		//Start control of all settings that comes after this function (between startBulkControl and endBulkControl)
		public function startBulkControl($control_item_name,$control_type,$value) {
			$this->arrBulkControl = array("control_name"=>$control_item_name,"type"=>$control_type,"value"=>$value);
		}	
			
		//End bulk control
		public function endBulkControl() {
			$this->arrBulkControl = array();
		}
		
		//Build name->(array index) of the settings. 
		private function buildArrSettingsIndex() {
			$this->arrIndex = array();
			foreach($this->arrSettings as $key=>$value)
				if(isset($value["name"])) $this->arrIndex[$value["name"]] = $key;
		}
		
		//Set sattes of the settings (enabled/disabled, visible/invisible) by controls
		public function setSettingsStateByControls() {
			
			foreach($this->arrControls as $control_name => $arrControlled) {
				//Take the control value
				if(!isset($this->arrIndex[$control_name])) throw new Exception("There is not sutch control setting: '$control_name'");
				$index = $this->arrIndex[$control_name];
				$parentValue = strtolower($this->arrSettings[$index]["value"]);
				
				//Set child (controlled) attributes
				foreach($arrControlled as $controlled) {
					if(!isset($this->arrIndex[$controlled["name"]])) throw new Exception("There is not sutch controlled setting: '".$controlled["name"]."'");
					$indexChild = $this->arrIndex[$controlled["name"]];
					$child = $this->arrSettings[$indexChild];					
					$value = strtolower($controlled["value"]);
					switch($controlled["type"]) {
						case self::CONTROL_TYPE_ENABLE:
							if($value != $parentValue) $child["disabled"] = true;
						break;
						case self::CONTROL_TYPE_DISABLE:
							if($value == $parentValue) $child["disabled"] = true;
						break;
						case self::CONTROL_TYPE_SHOW:
							if($value != $parentValue) $child["hidden"] = true;
						break;
						case self::CONTROL_TYPE_HIDE:
							if($value == $parentValue) $child["hidden"] = true;
						break;
					}
					$this->arrSettings[$indexChild] = $child;					
				}								
			}
		}
		
		//Check that bulk control is available , and add some element to it. 
		private function checkAddBulkControl($name) {
			//Add control
			if(!empty($this->arrBulkControl)) 
				$this->addControl($this->arrBulkControl["control_name"],$name,$this->arrBulkControl["type"],$this->arrBulkControl["value"]);			
		}
		
		//Set custom function that will be run after sections will be drawen
		public function setCustomDrawFunction_afterSections($func) {
			$this->customFunction_afterSections = $func;
		}		
		
		//Parse options from xml field
		private function getOptionsFromXMLField($field,$fieldName) {
			$arrOptions = array();
			
			$arrField = (array)$field;
			$options = UniteFunctionsBanner::getVal($arrField, "option");
			
			if(empty($options))
				return($arrOptions);
				
			foreach($options as $option) {
				
				if(gettype($option) == "string")
					UniteFunctionsBanner::throwError("Wrong options type: ".$option." in field: $fieldName");
				
				$attribs = $option->attributes();
				
				$optionValue = (string)UniteFunctionsBanner::getVal($attribs, "value");							
				$optionText = (string)UniteFunctionsBanner::getVal($attribs, "text");
				
				//validate options:
				UniteFunctionsBanner::validateNotEmpty($optionValue,"option value");
				UniteFunctionsBanner::validateNotEmpty($optionText,"option text");
				
				$arrOptions[$optionValue] = $optionText;				 
			}
			
			return($arrOptions);
		}		
		
		//Load settings from xml file
		public function loadXMLFile($filepath) {			
			if(!file_exists($filepath))
				UniteFunctionsBanner::throwError("File: '$filepath' not exists!!!");
			
			$obj = simplexml_load_file($filepath);
			
			if(empty($obj))
				UniteFunctionsBanner::throwError("Wrong xml file format: $filepath");
			
			$fieldsets = $obj->fieldset;
            if(!@count($obj->fieldset)) {
                $fieldsets = array($fieldsets);
            }
			
			$this->addSection("Xml Settings");
			
			foreach($fieldsets as $fieldset) {				
				//Add Section
				$attribs = $fieldset->attributes();
				
				$sapName = (string)UniteFunctionsBanner::getVal($attribs, "name");
				$sapLabel = (string)UniteFunctionsBanner::getVal($attribs, "label");
				
				UniteFunctionsBanner::validateNotEmpty($sapName,"sapName");
				UniteFunctionsBanner::validateNotEmpty($sapLabel,"sapLabel");
				
				$this->addSap($sapLabel,$sapName);
				
				//Add fields
				$fieldset = (array)$fieldset;				
				$fields = $fieldset["field"];
								
				if(is_array($fields) == false)
					$fields = array($fields);
				
				foreach($fields as $field) {
					$attribs = $field->attributes();
					$fieldType = (string)UniteFunctionsBanner::getVal($attribs, "type");
					$fieldName = (string)UniteFunctionsBanner::getVal($attribs, "name");
					$fieldLabel = (string)UniteFunctionsBanner::getVal($attribs, "label");
					$fieldDefaultValue = (string)UniteFunctionsBanner::getVal($attribs, "default");
					
					//All other params will be added to "params array"
					$arrMustParams = array("type","name","label","default"); 
					
					$arrParams = array();
					
					foreach($attribs as $key=>$value) {
						$key = (string)$key;
						$value = (string)$value;
						
						//Skip must params
						if(in_array($key, $arrMustParams))
							continue;
							
						$arrParams[$key] = $value;
					}
					
					$options = $this->getOptionsFromXMLField($field,$fieldName);
					
					//Validate must fields
					UniteFunctionsBanner::validateNotEmpty($fieldType,"type");
					
					//Validate name
					if($fieldType != self::TYPE_HR && $fieldType != self::TYPE_CONTROL &&
						$fieldType != "bulk_control_start" && $fieldType != "bulk_control_end")
						UniteFunctionsBanner::validateNotEmpty($fieldName,"name");		
					switch ($fieldType) {
						case self::TYPE_CHECKBOX:
							$fieldDefaultValue = UniteFunctionsBanner::strToBool($fieldDefaultValue);
							$this->addCheckbox($fieldName,$fieldDefaultValue,$fieldLabel,$arrParams);
						break;
						case self::TYPE_COLOR:
							$this->addColorPicker($fieldName,$fieldDefaultValue,$fieldLabel,$arrParams);
						break;
						case self::TYPE_HR:
							$this->addHr();
						break;
						case self::TYPE_TEXT:
							$this->addTextBox($fieldName,$fieldDefaultValue,$fieldLabel,$arrParams);
						break;
						case self::TYPE_STATIC_TEXT:
							$this->addStaticText($fieldLabel, $fieldName, $arrParams);
						break;
						case self::TYPE_IMAGE:
							$this->addImage($fieldName,$fieldDefaultValue,$fieldLabel,$arrParams);
						break;						
						case self::TYPE_SELECT:	
							$this->addSelect($fieldName, $options, $fieldLabel,$fieldDefaultValue,$arrParams);
						break;
						case self::TYPE_CHECKBOX:
							$this->addChecklist($fieldName, $options, $fieldLabel,$fieldDefaultValue,$arrParams);
						break;
						case self::TYPE_RADIO:
							$this->addRadio($fieldName, $options, $fieldLabel,$fieldDefaultValue,$arrParams);
						break;
						case self::TYPE_TEXTAREA:
							$this->addTextArea($fieldName, $fieldDefaultValue, $fieldLabel, $arrParams);
						break;
						case self::TYPE_CUSTOM:
							$this->add($fieldName, $fieldDefaultValue, $fieldLabel, self::TYPE_CUSTOM, $arrParams);
						break;						
						case self::TYPE_CONTROL:
							$parent = UniteFunctionsBanner::getVal($arrParams, "parent");
							$child =  UniteFunctionsBanner::getVal($arrParams, "child");
							$ctype =  UniteFunctionsBanner::getVal($arrParams, "ctype");
							$value =  UniteFunctionsBanner::getVal($arrParams, "value");
							$this->addControl($parent, $child, $ctype, $value);
						break;			
						case "bulk_control_start":
							$parent = UniteFunctionsBanner::getVal($arrParams, "parent");
							$ctype =  UniteFunctionsBanner::getVal($arrParams, "ctype");
							$value =  UniteFunctionsBanner::getVal($arrParams, "value");
							
							$this->startBulkControl($parent, $ctype, $value);
						break;
						case "bulk_control_end":
							$this->endBulkControl();
						break;			
						default:
							UniteFunctionsBanner::throwError("wrong type: $fieldType");
						break;						
					}
					
				}
			}
		}		
		
		//Get titles and descriptions array
		public function getArrTextFromAllSettings() {
			$arr = array();
			$arrUnits = array();
			
			//Get saps array
			foreach($this->arrSections as $section) {
				$arrSaps = UniteFunctionsBanner::getVal($section, "arrSaps");
				if(empty($arrSaps))
					continue;
				foreach($arrSaps as $sap) {
					$text = $sap["text"];
					if(!empty($text))
						$arr[] = $text;
				}
			}
			
			foreach($this->arrSettings as $setting) {				
				$text = UniteFunctionsBanner::getVal($setting, "text");				
				$desc = UniteFunctionsBanner::getVal($setting, "description");
				$unit = UniteFunctionsBanner::getVal($setting, "unit");
								
				if(!empty($text))
					$arr[] = $text;
					
				if(!empty($desc))
					$arr[] = $desc;
					
				if(!empty($unit)) {
					if(!isset($arrUnits[$unit]))
						$arr[] = $unit;	
					$arrUnits[$unit] = true;
				}

				$items = UniteFunctionsBanner::getVal($setting, "items");
				if(!empty($items)) {
					foreach($items as $item) {
						if(!isset($arrUnits[$item]))
							$arr[] = $item;	
						$arrUnits[$item] = true;
					}
				}				
			}
			
			return($arr);
		}
		
		//Get setting array by name
		public function getSettingByName($name) {
			
			//If index present
			if(!empty($this->arrIndex)) {
				if(array_key_exists($name, $this->arrIndex) == false)
					UniteFunctionsBanner::throwError("setting $name not found");
				$index = $this->arrIndex[$name];
				$setting = $this->arrSettings[$index];
				return($setting);
			}
			
			//If no index
			foreach($this->arrSettings as $setting) {
				$settingName = UniteFunctionsBanner::getVal($setting, "name");
				if($settingName == $name)
					return($setting);
			}
			
			UniteFunctionsBanner::throwError("Setting with name: $name don't exists");
		}		
		
		//Get value of some setting
		public function getSettingValue($name,$default="") {
			$setting = $this->getSettingByName($name);
			$value = UniteFunctionsBanner::getVal($setting, "value",$default);

			return($value);
		}		
		
		//Update setting array by name
		public function updateArrSettingByName($name,$setting) {
			
			foreach($this->arrSettings as $key => $settingExisting) {
				$settingName = UniteFunctionsBanner::getVal($settingExisting,"name");
				if($settingName == $name) {
					$this->arrSettings[$key] = $setting;
					return(false);
				}
			}
			
			UniteFunctionsBanner::throwError("Setting with name: $name don't exists");
		}		
		
		//Update default value in the setting
		public function updateSettingValue($name,$value) {
			$setting = $this->getSettingByName($name);
			$setting["value"] = $value;
			
			$this->updateArrSettingByName($name, $setting);
		}		
		
		//Set values from array of stored settings elsewhere.
		public function setStoredValues($arrValues) {
			foreach($this->arrSettings as $key=>$setting) {				
				$name = UniteFunctionsBanner::getVal($setting, "name");
				
				//Type consolidation
				$type = UniteFunctionsBanner::getVal($setting, "type");
				
				$customType = UniteFunctionsBanner::getVal($setting, "custom_type");
				if(!empty($customType))
					$type .= ".".$customType;
				
				switch($type) {
					case "custom.kenburns_position":
						$name = $setting["name"];
						if(array_key_exists($name."_hor", $arrValues)) {
							$value_vert = UniteFunctionsBanner::getVal($arrValues, $name."_vert","random");
							$value_hor = UniteFunctionsBanner::getVal($arrValues, $name."_hor","random");						
							$this->arrSettings[$key]["value"] = "$value_vert,$value_hor";
						}
					break;
					default:
						if(array_key_exists($name, $arrValues)) {
							$this->arrSettings[$key]["value"] = $arrValues[$name];
						}
					break;
				}
			}			
		}
		
		//Get setting values. replace from stored ones if given
		public function getArrValues() {			
			$arrSettingsOutput = array();
			
			//Modify settings by type
			foreach($this->arrSettings as $setting) {
				if($setting["type"] == self::TYPE_HR 
				  ||$setting["type"] == self::TYPE_STATIC_TEXT)
					continue;
					
				$value = $setting["value"];
				
				//Modify value by type
				switch($setting["type"]) {
					case self::TYPE_COLOR:
							$value = strtolower($value);
							//set color output type 
							if($this->colorOutputType == self::COLOR_OUTPUT_FLASH)
								$value = str_replace("#","0x",$value);
					break;
					case self::TYPE_CHECKBOX:
						if($value == true) $value = "true";
						else $value = "false";
					break;
				}
				
				//Remove lf
				if(isset($setting["remove_lf"])) {
					$value = str_replace("\n","",$value);
					$value = str_replace("\r\n","",$value);
				}
				
				$arrSettingsOutput[$setting["name"]] = $value;
			}
			
			return($arrSettingsOutput);
		}		
		
	}	
?>