<?php
	//Advanced settings class. adds some advanced features
	class UniteSettingsAdvancedBanner extends UniteSettingsBanner{
		
		//add boolean true/false select with custom names
		public function addSelect_boolean($name,$text,$bValue=true,$firstItem="Enable",$secondItem="Disable",$arrParams=array()) {
			$arrItems = array("true"=>$firstItem,"false"=>$secondItem);
			$defaultText = "true";
			if($bValue == false) $defaultText = "false";
			$this->addSelect($name,$arrItems,$text,$defaultText,$arrParams);
		}

		//Add float select
		public function addSelect_float($name,$defaultValue,$text,$arrParams=array()) {
			$this->addSelect($name,array("left"=>"Left","right"=>"Right"),$text,$defaultValue,$arrParams);
		}
		
		//Add align select
		public function addSelect_alignX($name,$defaultValue,$text,$arrParams=array()) {
			$this->addSelect($name,array("left"=>"Left","center"=>"Center","right"=>"Right"),$text,$defaultValue,$arrParams);
		}

		//Add align select
		public function addSelect_alignY($name,$defaultValue,$text,$arrParams=array()) {
			$this->addSelect($name,array("top"=>"Top","middle"=>"Middle","bottom"=>"Bottom"),$text,$defaultValue,$arrParams);
		}
		
		//Add transitions select
		public function addSelect_border($name,$defaultValue,$text,$arrParams=array()) {
			$arrItems = array();
			$arrItems["solid"] = "Solid";
			$arrItems["dashed"] = "Dashed";
			$arrItems["dotted"] = "Dotted";
			$arrItems["double"] = "Double";
			$arrItems["groove"] = "Groove";
			$arrItems["ridge"] = "Ridge";
			$arrItems["inset"] = "Inset";
			$arrItems["outset"] = "Outset";
			$this->addSelect($name,$arrItems,$text,$defaultValue,$arrParams);			
		}
		
		//Add transitions select
		public function addSelect_textDecoration($name,$defaultValue,$text,$arrParams=array()) {
			$arrItems = array();
			$arrItems["none"] = "None";
			$arrItems["underline"] = "Underline";
			$arrItems["overline"] = "Overline";
			$arrItems["line-through"] = "Line-through";
			$this->addSelect($name,$arrItems,$text,$defaultValue,$arrParams);			
		}
		
		//Add transitions select - arrExtensions may be string, and lower case
		public function addSelect_filescan($name,$path,$arrExtensions,$defaultValue,$text,$arrParams=array()) {
			
			if(getType($arrExtensions) == "string")
				$arrExtensions = array($arrExtensions);
			elseif(getType($arrExtensions) != "array")
				$this->throwError("The extensions array is not array and not string in setting: $name, please check.");
			
			//Make items array
			if(!is_dir($path))
				$this->throwError("path: $path not found");
			
			$arrItems = array();
			$files = scandir($path);
			foreach($files as $file) {
				//General filter
				if($file == ".." || $file == "." || $file == ".svn")
					continue;
					
				$info = pathinfo($file);
				$ext = UniteFunctionsBanner::getVal($info,"extension");
				$ext = strtolower($ext);
				
				if(array_search($ext,$arrExtensions) === FALSE)
					continue;
					
				$arrItems[$file] = $file;
			}
			
			//Handle add data array
			if(isset($arrParams["addData"])) {
				foreach($arrParams["addData"] as $key=>$value)
					$arrItems[$key] = $value;
			}
			
			if(empty($defaultValue) && !empty($arrItems))
				$defaultValue = current($arrItems);
			
			$this->addSelect($name,$arrItems,$text,$defaultValue,$arrParams);
		}		
		
		//Add transitions select
		public function addSelect_transitions($name,$defaultValue,$text,$arrParams=array()) {
			$arrItems = array();
			$arrItems["linear"] = "Linear";
			$arrItems["easeOutQuint"] = "EaseOut";
			$arrItems["easeInQuint"] = "EaseIn";
			$arrItems["easeInOutQuad"] = "EaseInOut";
			
			$arrItems["easeOutElastic"] = "EaseIn - Elastic";
			$arrItems["easeOutBounce"] = "EaseIn - Bounce";
			$arrItems["easeOutBack"] = "EaseIn - Back";
			$arrItems["easeOutQuart"] = "EaseIn - Quart";
			$arrItems["easeOutExpo"] = "EaseIn - Expo";
			
			$arrItems["easeInElastic"] = "EaseOut - Elastic";
			$arrItems["easeInBounce"] = "EaseOut - Bounce";
			$arrItems["easeInBack"] = "EaseOut - Back";
			$arrItems["easeInQuart"] = "EaseOut - Quart";
			$arrItems["easeInExpo"] = "EaseOut - Expo";
			
			$arrItems["easeInOutElastic"] = "EaseInOut - Elastic";
			$arrItems["easeInOutBounce"] = "EaseInOut - Bounce";
			$arrItems["easeInOutBack"] = "EaseInOut - Back";
			$arrItems["easeInOutQuart"] = "EaseInOut - Quart";
			$arrItems["easeInOutExpo"] = "EaseInOut - Expo";
			
			$this->addSelect($name,$arrItems,$text,$defaultValue,$arrParams);			
		}
		
	}	
?>