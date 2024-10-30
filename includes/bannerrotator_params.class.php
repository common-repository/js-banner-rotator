<?php
	//Get / update params in db
	class BannerRotatorParams extends UniteElementsBaseBanner {
		
		
		//Update setting in db
		public function updateFieldInDB($name,$value) {
						
			$arr = $this->db->fetch(GlobalsBannerRotator::$table_settings);
			if(empty($arr)) {	
				//Insert to db
				$arrInsert = array();
				$arrInsert["general"] = "";
				$arrInsert["params"] = "";
				$arrInsert[$name] = $value;
				
				$this->db->insert(GlobalsBannerRotator::$table_settings,$arrInsert);
			}else{	
				//Update db
				$arrUpdate = array();
				$arrUpdate[$name] = $value;
				
				$id = $arr[0]["id"];
				$this->db->update(GlobalsBannerRotator::$table_settings,$arrUpdate,array("id"=>$id));
			}
		}		
		
		//Get field from db
		public function getFieldFromDB($name) {			
			$arr = $this->db->fetch(GlobalsBannerRotator::$table_settings);
						
			if(empty($arr))
				return("");				
			
			$arr = $arr[0];
			
			if(array_key_exists($name, $arr) == false)
				UniteFunctionsBanner::throwError("The settings db should cotnain field: $name");
			
			$value = $arr[$name];
			return($value);
		}		
		
	}
?>