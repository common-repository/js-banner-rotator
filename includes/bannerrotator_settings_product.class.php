<?php
	class BannerRotatorSettingsProduct extends UniteSettingsBannerProductBanner {
		
		
		//Set custom values to settings
		public static function setSettingsCustomValues(UniteSettingsBanner $settings,$arrValues) {
			$arrSettings = $settings->getArrSettings();
			
			foreach($arrSettings as $key=>$setting) {
				$type = UniteFunctionsBanner::getVal($setting, "type");
				if($type != UniteSettingsBanner::TYPE_CUSTOM)
					continue;
				$customType = UniteFunctionsBanner::getVal($setting, "custom_type");
				
				switch($customType) {
					case "sliderSize":
						$setting["width"] = UniteFunctionsBanner::getVal($arrValues, "width",UniteFunctionsBanner::getVal($setting,"width"));
						$setting["height"] = UniteFunctionsBanner::getVal($arrValues, "height",UniteFunctionsBanner::getVal($setting,"height"));
						$arrSettings[$key] = $setting;
					break;
					case "responsitiveSettings":						
						$id = $setting["id"];
						$setting["w1"] = UniteFunctionsBanner::getVal($arrValues, $id."_w1",UniteFunctionsBanner::getVal($setting,"w1"));
						$setting["w2"] = UniteFunctionsBanner::getVal($arrValues, $id."_w2",UniteFunctionsBanner::getVal($setting,"w2"));
						$setting["w3"] = UniteFunctionsBanner::getVal($arrValues, $id."_w3",UniteFunctionsBanner::getVal($setting,"w3"));
						$setting["w4"] = UniteFunctionsBanner::getVal($arrValues, $id."_w4",UniteFunctionsBanner::getVal($setting,"w4"));
						$setting["w5"] = UniteFunctionsBanner::getVal($arrValues, $id."_w5",UniteFunctionsBanner::getVal($setting,"w5"));
						$setting["w6"] = UniteFunctionsBanner::getVal($arrValues, $id."_w6",UniteFunctionsBanner::getVal($setting,"w6"));
						
						$setting["sw1"] = UniteFunctionsBanner::getVal($arrValues, $id."_sw1",UniteFunctionsBanner::getVal($setting,"sw1"));
						$setting["sw2"] = UniteFunctionsBanner::getVal($arrValues, $id."_sw2",UniteFunctionsBanner::getVal($setting,"sw2"));
						$setting["sw3"] = UniteFunctionsBanner::getVal($arrValues, $id."_sw3",UniteFunctionsBanner::getVal($setting,"sw3"));
						$setting["sw4"] = UniteFunctionsBanner::getVal($arrValues, $id."_sw4",UniteFunctionsBanner::getVal($setting,"sw4"));
						$setting["sw5"] = UniteFunctionsBanner::getVal($arrValues, $id."_sw5",UniteFunctionsBanner::getVal($setting,"sw5"));
						$setting["sw6"] = UniteFunctionsBanner::getVal($arrValues, $id."_sw6",UniteFunctionsBanner::getVal($setting,"sw6"));
						$arrSettings[$key] = $setting;				
					break;
				}
			}
			
			$settings->setArrSettings($arrSettings);
			
			//Disable settings by slider type:
			$sliderType = $settings->getSettingValue("sliderType");
			
			switch($sliderType) {
				case "fixed":
				case "fullwidth":
				case "fullscreen":
					//Hide responsive
					$settingRes = $settings->getSettingByName("responsitive");
					$settingRes["disabled"] = true;
					$settings->updateArrSettingByName("responsitive", $settingRes);
				break;
			}
			
			//Change height to max height
			$settingSize = $settings->getSettingByName("sliderSize");
			$settingSize["sliderType"] = $sliderType;
			$settings->updateArrSettingByName("sliderSize", $settingSize);
			
			return($settings);
		}	
		
		//Draw responsitive settings value
		protected function drawResponsitiveSettings($setting) {
			$id = $setting["id"];
			
			$w1 = UniteFunctionsBanner::getVal($setting, "w1");
			$w2 = UniteFunctionsBanner::getVal($setting, "w2");
			$w3 = UniteFunctionsBanner::getVal($setting, "w3");
			$w4 = UniteFunctionsBanner::getVal($setting, "w4");
			$w5 = UniteFunctionsBanner::getVal($setting, "w5");
			$w6 = UniteFunctionsBanner::getVal($setting, "w6");
			
			$sw1 = UniteFunctionsBanner::getVal($setting, "sw1");
			$sw2 = UniteFunctionsBanner::getVal($setting, "sw2");
			$sw3 = UniteFunctionsBanner::getVal($setting, "sw3");
			$sw4 = UniteFunctionsBanner::getVal($setting, "sw4");
			$sw5 = UniteFunctionsBanner::getVal($setting, "sw5");
			$sw6 = UniteFunctionsBanner::getVal($setting, "sw6");
			
			$disabled = (UniteFunctionsBanner::getVal($setting, "disabled") == true);
			
			$strDisabled = "";
			if($disabled == true)
				$strDisabled = "disabled='disabled'";
			
			?>
			<table>
				<tr>
					<td>
						<?php _e("Screen Width",BANNERROTATOR_TEXTDOMAIN)?>1:
					</td>
					<td>
						<input id="<?php echo $id?>_w1" name="<?php echo $id?>_w1" type="text" class="small-text" <?php echo $strDisabled?> value="<?php echo $w1?>">
					</td>
					<td>
						<?php _e("Slider Width",BANNERROTATOR_TEXTDOMAIN)?>1: 
					</td>
					<td>
						<input id="<?php echo $id?>_sw1" name="<?php echo $id?>_sw1" type="text" class="small-text" <?php echo $strDisabled?> value="<?php echo $sw1?>">
					</td>
				</tr>
				<tr>
					<td>
						<?php _e("Screen Width",BANNERROTATOR_TEXTDOMAIN)?>2: 
					</td>
					<td>
						<input id="<?php echo $id?>_w2" name="<?php echo $id?>_w2" type="text" class="small-text" <?php echo $strDisabled?> value="<?php echo $w2?>">
					</td>
					<td>
						<?php _e("Slider Width",BANNERROTATOR_TEXTDOMAIN)?>2: 
					</td>
					<td>
						<input id="<?php echo $id?>_sw2" name="<?php echo $id?>_sw2" type="text" class="small-text" <?php echo $strDisabled?> value="<?php echo $sw2?>">
					</td>
				</tr>
				<tr>
					<td>
						<?php _e("Screen Width",BANNERROTATOR_TEXTDOMAIN)?>3: 
					</td>
					<td>
						<input id="<?php echo $id?>_w3" name="<?php echo $id?>_w3" type="text" class="small-text" <?php echo $strDisabled?> value="<?php echo $w3?>">
					</td>
					<td>
						<?php _e("Slider Width",BANNERROTATOR_TEXTDOMAIN)?>3:
					</td>
					<td>
						<input id="<?php echo $id?>_sw3" name="<?php echo $id?>_sw3" type="text" class="small-text" <?php echo $strDisabled?> value="<?php echo $sw3?>">
					</td>
				</tr>
				<tr>
					<td>
						<?php _e("Screen Width",BANNERROTATOR_TEXTDOMAIN)?>4: 
					</td>
					<td>
						<input type="text" id="<?php echo $id?>_w4" name="<?php echo $id?>_w4" class="small-text" <?php echo $strDisabled?> value="<?php echo $w4?>">
					</td>
					<td>
						<?php _e("Slider Width",BANNERROTATOR_TEXTDOMAIN)?>4: 
					</td>
					<td>
						<input type="text" id="<?php echo $id?>_sw4" name="<?php echo $id?>_sw4" class="small-text" <?php echo $strDisabled?> value="<?php echo $sw4?>">
					</td>
				</tr>
				<tr>
					<td>
						<?php _e("Screen Width",BANNERROTATOR_TEXTDOMAIN)?>5:
					</td>
					<td>
						<input type="text" id="<?php echo $id?>_w5" name="<?php echo $id?>_w5" class="small-text" <?php echo $strDisabled?> value="<?php echo $w5?>">
					</td>
					<td>
						<?php _e("Slider Width",BANNERROTATOR_TEXTDOMAIN)?>5:
					</td>
					<td>
						<input type="text" id="<?php echo $id?>_sw5" name="<?php echo $id?>_sw5" class="small-text" <?php echo $strDisabled?> value="<?php echo $sw5?>">
					</td>
				</tr>
				<tr>
					<td>
						<?php _e("Screen Width",BANNERROTATOR_TEXTDOMAIN)?>6:
					</td>
					<td>
						<input type="text" id="<?php echo $id?>_w6" name="<?php echo $id?>_w6" class="small-text" <?php echo $strDisabled?> value="<?php echo $w6?>">
					</td>
					<td>
						<?php _e("Slider Width",BANNERROTATOR_TEXTDOMAIN)?>6:
					</td>
					<td>
						<input type="text" id="<?php echo $id?>_sw6" name="<?php echo $id?>_sw6" class="small-text" <?php echo $strDisabled?> value="<?php echo $sw6?>">
					</td>
				</tr>				
								
			</table>
			<?php
		}		
		
		//Draw slider size
		protected function drawSliderSize($setting) {
			
			$width = UniteFunctionsBanner::getVal($setting, "width");
			$height = UniteFunctionsBanner::getVal($setting, "height");
			
			$sliderType = UniteFunctionsBanner::getVal($setting, "sliderType");
			
			$textNormalW = __("Grid Width:",BANNERROTATOR_TEXTDOMAIN);
			$textNormalH = __("Grid Height:",BANNERROTATOR_TEXTDOMAIN);
			
			$textFullWidthW = __("Grid Width:",BANNERROTATOR_TEXTDOMAIN);
			$textFullWidthH = __("Grid Height:",BANNERROTATOR_TEXTDOMAIN);
			
			$textFullScreenW = __("Grid Width:",BANNERROTATOR_TEXTDOMAIN);
			$textFullScreenH = __("Grid Height:",BANNERROTATOR_TEXTDOMAIN);
			
			//Set default text (fixed, responsive) 
			switch($sliderType) {
				default:
					$textDefaultW = $textNormalW;
					$textDefaultH = $textNormalH;
				break;
				case "fullwidth":
					$textDefaultW = $textFullWidthW;
					$textDefaultH = $textFullWidthH;
				break;
				case "fullscreen":
					$textDefaultW = $textFullScreenW;
					$textDefaultH = $textFullScreenH;
				break;
			}			
			?>
			
			<table>
				<tr>
					<td id="cellWidth" data-textnormal="<?php echo $textNormalW?>" data-textfull="<?php echo $textFullWidthW?>" data-textscreen="<?php echo $textFullScreenW?>">
						<?php echo $textDefaultW ?>
					</td>
					<td id="cellWidthInput">
						<input id="width" name="width" type="text" class="small-text" value="<?php echo $width?>">
					</td>
					<td id="cellHeight" data-textnormal="<?php echo $textNormalH?>" data-textfull="<?php echo $textFullWidthH?>" data-textscreen="<?php echo $textFullScreenH?>">
						<?php echo $textDefaultH ?> 
					</td>
					<td>
						<input id="height" name="height" type="text" class="small-text" value="<?php echo $height?>">
					</td>
				</tr>
			</table>
			
			<?php 
		}		
		
		//Draw custom inputs for banner rotator
		protected function drawCustomInputs($setting) {			
			$customType = UniteFunctionsBanner::getVal($setting, "custom_type");
			switch($customType) {
				case "sliderSize":
					$this->drawSliderSize($setting);
				break;
				case "responsitiveSettings":
					$this->drawResponsitiveSettings($setting);
				break;
				default:
					UniteFunctionsBanner::throwError("No handler function for type: $customType");
				break;
			}			
		}
		
	}
?>