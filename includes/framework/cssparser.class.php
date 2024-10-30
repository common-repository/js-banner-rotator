<?php
	class UniteCssParserBanner {
		
		private $cssContent;
		
		public function __construct(){
			
		}
		
		//Init the parser, set css content
		public function initContent($cssContent){
			$this->cssContent = $cssContent;
		}		
		
		//Get array of slide classes, between two sections
		public function getArrClasses($startText = "",$endText="") {			
			$content = $this->cssContent;
			
			//Trim from top
			if(!empty($startText)){
				$posStart = strpos($content, $startText);
				if($posStart !== false)
					$content = substr($content, $posStart,strlen($content)-$posStart);
			}
			
			//Trim from bottom
			if(!empty($endText)){
				$posEnd = strpos($content, $endText);
				if($posEnd !== false)
					$content = substr($content,0,$posEnd);
			}
			
			//Get styles
			$lines = explode("\n",$content);
			$arrClasses = array();
			foreach($lines as $key=>$line){
				$line = trim($line);
				
				if(strpos($line, "{") === false)
					continue;

				//Skip unnessasary links
				if(strpos($line, ".caption a") !== false)
					continue;
					
				if(strpos($line, ".caption a") !== false)
					continue;
					
				//Get style out of the line
				$class = str_replace("{", "", $line);
				$class = trim($class);
				
				//Skip captions like this: .caption.imageclass img
				if(strpos($class," ") !== false)
					continue;
				
				$class = str_replace(".caption.", ".", $class);
				$class = str_replace(".caption.", ".", $class);
				
				$class = str_replace(".", "", $class);
				$class = trim($class);
				$arrWords = explode(" ", $class);
				$class = $arrWords[count($arrWords)-1];
				$class = trim($class);
				
				$arrClasses[] = $class;	
			}
			
			return($arrClasses);
		}
		
	}
?>