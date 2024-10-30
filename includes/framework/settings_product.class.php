<?php
	class UniteSettingsBannerProductBanner extends UniteSettingsOutputBanner {		
		
		//Draw text as input
		protected function drawTextInput($setting) {
			$disabled = "";
			$style="";
			$readonly = "";
			
			if(isset($setting["style"])) 
				$style = "style='".$setting["style"]."'";
			if(isset($setting["disabled"])) 
				$disabled = 'disabled="disabled"';
				
			if(isset($setting["readonly"])) {
				$readonly = "readonly='readonly'";
			}
			
			$class = "regular-text";
						
			if(isset($setting["class"]) && !empty($setting["class"])) {
				$class = $setting["class"];
				
				//Convert short classes:
				switch($class) {
					case "small":
						$class = "small-text";
					break;
					case "code":
						$class = "regular-text code";
					break;
				}
			}
				
			if(!empty($class))
				$class = "class='$class'";
			
			?>
				<input type="text" <?php echo $class?> <?php echo $style?> <?php echo $disabled?><?php echo $readonly?> id="<?php echo $setting["id"]?>" name="<?php echo $setting["name"]?>" value="<?php echo $setting["value"]?>" />
			<?php
		}		
		
		//Draw image input
		protected function drawImageInput($setting) {			
			$class = UniteFunctionsBanner::getVal($setting, "class");
			
			if(!empty($class))
				$class = "class='$class'";
			
			$settingsID = $setting["id"];
			
			$buttonID = $settingsID."_button";
			
			$spanPreviewID = $buttonID."_preview";
			
			$img = "";
			$value = UniteFunctionsBanner::getVal($setting, "value");
			
			if(!empty($value)) {
				$urlImage = $value;
				$imagePath = UniteFunctionsWPBanner::getImageRealPathFromUrl($urlImage);
				if(file_exists($realPath)) {
					$filepath = UniteFunctionsWPBanner::getImagePathFromURL($urlImage);
					$urlImage = UniteBaseClassBanner::getImageUrl($filepath,100,70,true);
				}
				
				$img = "<img width='100' height='70' src='$urlImage'></img>";
			}			
			?>
				<span id='<?php echo $spanPreviewID?>' class='setting-image-preview'><?php echo $img?></span>
				
				<input type="hidden" id="<?php echo $setting["id"]?>" name="<?php echo $setting["name"]?>" value="<?php echo $setting["value"]?>" />
				
				<input type="button" id="<?php echo $buttonID?>" class='button-image-select button-primary btn-blue <?php echo $class?>' value="Choose Image"></input>
			<?php
		}		
		
		//Draw a color picker
		protected function drawColorPickerInput($setting) {			
			$bgcolor = $setting["value"];
			$bgcolor = str_replace("0x","#",$bgcolor);			
			
			//Set the forent color (by black and white value)
			$rgb = UniteFunctionsBanner::html2rgb($bgcolor);
			$bw = UniteFunctionsBanner::yiq($rgb[0],$rgb[1],$rgb[2]);
			$color = "#000000";
			if($bw<128) $color = "#ffffff";
			
			
			$disabled = "";
			if(isset($setting["disabled"])) {
				$color = "";
				$disabled = 'disabled="disabled"';
			}
			
			$style="style='background-color:$bgcolor;color:$color'";			
			?>
				<input type="text" class="inputColorPicker" id="<?php echo $setting["id"]?>" <?php echo $style?> name="<?php echo $setting["name"]?>" value="<?php echo $bgcolor?>" <?php echo $disabled?>></input>
			<?php
		}
		
		//Draw a date picker
		protected function drawDatePickerInput($setting){			
			$date = $setting["value"];
			?>
				<input type="text" class="inputDatePicker medium-text" id="<?php echo $setting["id"]?>" name="<?php echo $setting["name"]?>" value="<?php echo $date?>"></input>
			<?php
		}
		
		//Draw setting input by type
		protected function drawInputs($setting) {
			switch($setting["type"]) {
				case UniteSettingsBanner::TYPE_TEXT:
					$this->drawTextInput($setting);
				break;
				case UniteSettingsBanner::TYPE_COLOR:
					$this->drawColorPickerInput($setting);
				break;
				case UniteSettingsBanner::TYPE_DATE:
					$this->drawDatePickerInput($setting);
				break;
				case UniteSettingsBanner::TYPE_SELECT:
					$this->drawSelectInput($setting);
				break;
				case UniteSettingsBanner::TYPE_CHECKLIST:
					$this->drawChecklistInput($setting);
				break;
				case UniteSettingsBanner::TYPE_CHECKBOX:
					$this->drawCheckboxInput($setting);
				break;
				case UniteSettingsBanner::TYPE_RADIO:
					$this->drawRadioInput($setting);
				break;
				case UniteSettingsBanner::TYPE_TEXTAREA:
					$this->drawTextAreaInput($setting);
				break;
				case UniteSettingsBanner::TYPE_IMAGE:
					$this->drawImageInput($setting);
				break;
				case UniteSettingsBanner::TYPE_CUSTOM:
					if(method_exists($this,"drawCustomInputs") == false) {
						UniteFunctionsBanner::throwError("Method don't exists: drawCustomInputs, please override the class");
					}
					$this->drawCustomInputs($setting);
				break;
				default:
					throw new Exception("wrong setting type - ".$setting["type"]);
				break;
			}			
		}	
		
		//Draw text area input		
		protected function drawTextAreaInput($setting) {
			
			$disabled = "";
			if (isset($setting["disabled"])) $disabled = 'disabled="disabled"';
			
			$style = "";
			if(isset($setting["style"]))
				$style = "style='".$setting["style"]."'";

			$rows = UniteFunctionsBanner::getVal($setting, "rows");
			if(!empty($rows))
				$rows = "rows='$rows'";
				
			$cols = UniteFunctionsBanner::getVal($setting, "cols");
			if(!empty($cols))
				$cols = "cols='$cols'";
			
			?>
				<textarea id="<?php echo $setting["id"]?>" name="<?php echo $setting["name"]?>" <?php echo $style?> <?php echo $disabled?> <?php echo $rows?> <?php echo $cols?>  ><?php echo $setting["value"]?></textarea>
			<?php
			if(!empty($cols))
				echo "<br>";	//Break line on big textareas
		}	
		
		//Draw radio input
		protected function drawRadioInput($setting) {
			$items = $setting["items"];
			$settingID = UniteFunctionsBanner::getVal($setting, "id");
			$wrapperID = $settingID."_wrapper";
			
			$addParams = UniteFunctionsBanner::getVal($setting, "addparams");
			
			$counter = 0;
			?>
			<span id="<?php echo $wrapperID?>" class="radio_settings_wrapper" <?php echo $addParams?>>
			<?php 
			foreach($items as $value=>$text):
				$counter++;
				$radioID = $setting["id"]."_".$counter;
				$checked = "";
				if($value == $setting["value"]) $checked = " checked"; 
				?>
					<input type="radio" id="<?php echo $radioID?>" value="<?php echo $value?>" name="<?php echo $setting["name"]?>" <?php echo $checked?>/>
					<label for="<?php echo $radioID?>" style="cursor:pointer;"><?php echo $text?></label>
					&nbsp; &nbsp;
				<?php				
			endforeach;
			?>
			</span>
			<?php 
		}
		
		//Draw checkbox
		protected function drawCheckboxInput($setting) {
			$checked = "";
			if($setting["value"] == "true") $checked = 'checked="checked"';
			?>
				<input type="checkbox" id="<?php echo $setting["id"]?>" class="iphone_checkboxes" name="<?php echo $setting["name"]?>" <?php echo $checked?>/>
			<?php
		}		
		
		//Draw select input
		protected function drawSelectInput($setting) {			
			$className = "";
			if(isset($this->arrControls[$setting["name"]])) $className = "control";
			$class = "";
			if($className != "") $class = "class='".$className."'";
			
			$disabled = "";
			if(isset($setting["disabled"])) $disabled = 'disabled="disabled"';
			
			?>
			<select id="<?php echo $setting["id"]?>" name="<?php echo $setting["name"]?>" <?php echo $disabled?> <?php echo $class?>>
			<?php			
			foreach($setting["items"] as $value=>$text):
				$text = __($text,BANNERROTATOR_TEXTDOMAIN);
				$selected = "";
				if($value == $setting["value"]) $selected = 'selected="selected"';
				?>
					<option value="<?php echo $value?>" <?php echo $selected?>><?php echo $text?></option>
				<?php
			endforeach
			?>
			</select>
			<?php
		}

		
		//Draw checklist input
		//@param unknown_type $setting
		protected function drawChecklistInput($setting) {
			
			$className = "input_checklist";
			if(isset($this->arrControls[$setting["name"]])) 
				$className .= " control";
							
			$class = "";
			if($className != "") $class = "class='".$className."'";
			
			$disabled = "";
			if(isset($setting["disabled"])) $disabled = 'disabled="disabled"';
			
			$args = UniteFunctionsBanner::getVal($setting, "args");
			
			$settingValue = $setting["value"];
			
			if(strpos($settingValue,",") !== false)
				$settingValue = explode(",", $settingValue);
			
			$style = "z-index:1000;";
			$minWidth = UniteFunctionsBanner::getVal($setting, "minwidth");
			
			if(!empty($minWidth)) {
				$style .= "min-width:{$minWidth}px;";
				$args .= " data-minwidth='{$minWidth}'";
			}			
			?>
			<select id="<?php echo $setting["id"]?>" name="<?php echo $setting["name"]?>" <?php echo $disabled?> multiple <?php echo $class?> <?php echo $args?> size="1" style="<?php echo $style?>">
			<?php
			foreach($setting["items"] as $value=>$text):
				//set selected
				$selected = "";
				$addition = "";
				if(strpos($value,"option_disabled") === 0) {
					$addition = "disabled";					
				}else{
					if(is_array($settingValue)) {
						if(array_search($value, $settingValue) !== false) 
							$selected = 'selected="selected"';
					}else{
						if($value == $settingValue) 
							$selected = 'selected="selected"';
					}
				}
									
				
				?>
					<option <?php echo $addition?> value="<?php echo $value?>" <?php echo $selected?>><?php echo $text?></option>
				<?php
			endforeach
			?>
			</select>
			<?php
		}		
		
		//Draw hr row
		protected function drawTextRow($setting) {			
			//Set cell style
			$cellStyle = "";
			if(isset($setting["padding"])) 
				$cellStyle .= "padding-left:".$setting["padding"].";";
				
			if(!empty($cellStyle))
				$cellStyle="style='$cellStyle'";
				
			//Set style
			$rowStyle = "";					
			if(isset($setting["hidden"])) 
				$rowStyle .= "display:none;";
				
			if(!empty($rowStyle))
				$rowStyle = "style='$rowStyle'";
			
			?>
				<tr id="<?php echo $setting["id_row"]?>" <?php echo $rowStyle ?> valign="top">
					<td colspan="4" align="right" <?php echo $cellStyle?>>
						<span class="spanSettingsStaticText"><?php echo $setting["text"]?></span>
					</td>
				</tr>
			<?php 
		}
		
		//Draw hr row
		protected function drawHrRow($setting) {
			//Set hidden
			$rowStyle = "";
			if(isset($setting["hidden"])) $rowStyle = "style='display:none;'";
			
			$class = UniteFunctionsBanner::getVal($setting, "class");
			if(!empty($class))
				$class = "class='$class'";
			
			?>
			<tr id="<?php echo $setting["id_row"]?>" <?php echo $rowStyle ?>>
				<td colspan="4" align="left" style="text-align:left;">
					 <hr <?php echo $class; ?> /> 
				</td>
			</tr>
			<?php 
		}
		
		//Draw settings row
		protected function drawSettingRow($setting) {		
			//Set cellstyle
			$cellStyle = "";
			if(isset($setting[UniteSettingsBanner::PARAM_CELLSTYLE])) {
				$cellStyle .= $setting[UniteSettingsBanner::PARAM_CELLSTYLE];
			}
			
			//Set text style
			$textStyle = $cellStyle;
			if(isset($setting[UniteSettingsBanner::PARAM_TEXTSTYLE])) {
				$textStyle .= $setting[UniteSettingsBanner::PARAM_TEXTSTYLE];
			}
			
			if($textStyle != "") $textStyle = "style='".$textStyle."'";
			if($cellStyle != "") $cellStyle = "style='".$cellStyle."'";
			
			//Set hidden
			$rowStyle = "";
			if(isset($setting["hidden"])) $rowStyle = "display:none;";
			if(!empty($rowStyle)) $rowStyle = "style='$rowStyle'";
			
			//Set text class
			$class = "";
			if(isset($setting["disabled"])) $class = "class='disabled'";
			
			//Modify text
			$text = UniteFunctionsBanner::getVal($setting,"text","");				
			
			//Prevent line break (convert spaces to nbsp)
			$text = str_replace(" ","&nbsp;",$text);
			switch($setting["type"]) {					
				case UniteSettingsBanner::TYPE_CHECKBOX:
					$text = "<label for='".$setting["id"]."' style='cursor:pointer;'>$text</label>";
				break;
			}			
			
			//Set settings text width
			$textWidth = "";
			if(isset($setting["textWidth"])) $textWidth = 'width="'.$setting["textWidth"].'"';
			
			$description = UniteFunctionsBanner::getVal($setting, "description");
			$required = UniteFunctionsBanner::getVal($setting, "required");			
			?>
				<tr id="<?php echo $setting["id_row"]?>" <?php echo $rowStyle ?> <?php echo $class?> valign="top">
					<th <?php echo $textStyle?> scope="row" <?php echo $textWidth ?>>
						<?php echo $text?>:
					</th>
					<td <?php echo $cellStyle?>>
						<?php 
							$this->drawInputs($setting);
						?>
						<?php if(!empty($required)):?>
							<span class='setting_required'>*</span>
						<?php endif?>	
						<div class="description_container">
							<?php if(!empty($description)):?>
								<span class="description"><?php echo $description?></span>
							<?php endif?>	
						</div>										
					</td>
					<td class="description_container_in_td">
						<?php if(!empty($description)):?>
							<span class="description"><?php echo $description?></span>
						<?php endif?>	
					</td>
				</tr>								
			<?php 
		}
		
		//Draw all settings
		public function drawSettings() {
			$this->drawHeaderIncludes();
			$this->prepareToDraw();
			
			//Draw main div
			$lastSectionKey = -1;
			$visibleSectionKey = 0;
			$lastSapKey = -1;
			
			$arrSections = $this->settings->getArrSections();
			$arrSettings = $this->settings->getArrSettings();
			
			//Draw settings - simple
			if(empty($arrSections)):
					?><table class='form-table'><?php
					foreach($arrSettings as $key=>$setting) {
						switch($setting["type"]) {
							case UniteSettingsBanner::TYPE_HR:
								$this->drawHrRow($setting);
							break;
							case UniteSettingsBanner::TYPE_STATIC_TEXT:
								$this->drawTextRow($setting);
							break;
							default:
								$this->drawSettingRow($setting);
							break;
						}
					}
					?></table><?php					
			else:			
				//Draw settings - advanced - with sections
				foreach($arrSettings as $key=>$setting):
								
					//Operate sections
					if(!empty($arrSections) && isset($setting["section"])) {										
						$sectionKey = $setting["section"];
												
						if($sectionKey != $lastSectionKey):	//new section					
							$arrSaps = $arrSections[$sectionKey]["arrSaps"];
							
							if(!empty($arrSaps)) {
								//Close sap
								if($lastSapKey != -1):
								?>
									</table>
									</div>
								<?php						
								endif;							
								$lastSapKey = -1;
							}
							
					 		$style = ($visibleSectionKey == $sectionKey)?"":"style='display:none'";
					 		
					 		//Close section
					 		if($sectionKey != 0):
					 			if(empty($arrSaps))
					 				echo "</table>";
					 			echo "</div>\n";	 
					 		endif;					 		
					 		
							//If no saps - add table
							if(empty($arrSaps)):
							?><table class="form-table"><?php
							endif;								
						endif;
						$lastSectionKey = $sectionKey;
					}
					
					//Operate saps
					if(!empty($arrSaps) && isset($setting["sap"])) {				
						$sapKey = $setting["sap"];
						if($sapKey != $lastSapKey) {
							$sap = $this->settings->getSap($sapKey,$sectionKey);
							
							//Draw sap end					
							if($sapKey != 0): ?>
							</table>
							<?php endif;
							
							//Set opened/closed states
							$style = "";
							
							$class = "divSapControl";
							
							if($sapKey == 0 || isset($sap["opened"]) && $sap["opened"] == true) {
								$style = "";
								$class = "divSapControl opened";						
							}
							
							?>
								<div id="divSapControl_<?php echo $sectionKey."_".$sapKey?>" class="<?php echo $class?>">
									
									<h3><?php echo $sap["text"]?></h3>
								</div>
								<div id="divSap_<?php echo $sectionKey."_".$sapKey?>" class="divSap" <?php echo $style ?>>				
								<table class="form-table">
							<?php 
							$lastSapKey = $sapKey;
						}
					}
					
					//Draw row
					switch($setting["type"]) {
						case UniteSettingsBanner::TYPE_HR:
							$this->drawHrRow($setting);
						break;
						case UniteSettingsBanner::TYPE_STATIC_TEXT:
							$this->drawTextRow($setting);
						break;
						default:
							$this->drawSettingRow($setting);
						break;
					}					
				endforeach;
			endif;	
			 ?>
			</table>
			
			<?php
			if(!empty($arrSections)):
				if(empty($arrSaps))	 //Close table settings if no saps 
					echo "</table>";
				echo "</div>\n";	 //Close last section div
			endif;			
		}		
		
		//Draw sections menu
		public function drawSections($activeSection=0) {
			if(!empty($this->arrSections)):
				echo "<ul class='listSections' >";
				for($i=0;$i<count($this->arrSections);$i++):
					$class = "";
					if($activeSection == $i) $class="class='selected'";
					$text = $this->arrSections[$i]["text"];
					echo '<li '.$class.'><a onfocus="this.blur()" href="#'.($i+1).'"><div>'.$text.'</div></a></li>';
				endfor;
				echo "</ul>";
			endif;
				
			//Call custom draw function:
			if($this->customFunction_afterSections) call_user_func($this->customFunction_afterSections);
		}
		
		//Draw settings function
		//@param $drawForm draw the form yes / no
		public function draw($formID=null,$drawForm = false) {
			if(empty($formID))
				UniteFunctionsBanner::throwError("The form ID can't be empty. you must provide it");
				
				$this->formID = $formID;
				
			?>
				<div class="settings_wrapper unite_settings_wide">
			<?php
			
			if($drawForm == true) {
				?>
				<form name="<?php echo $formID?>" id="<?php echo $formID?>">
					<?php $this->drawSettings() ?>
				</form>
				<?php 				
			}else
				$this->drawSettings();
			
			?>
			</div>
			<?php 
		}
		
	}
?>