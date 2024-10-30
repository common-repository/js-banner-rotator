<?php 
	class BannerRotator_Widget extends WP_Widget {
		
		public function __construct() {			
			//Widget actual processes
			$widget_ops = array('classname' => 'widget_bannerrotator', 'description' => __('Displays an Banner Rotator on the page') );
			parent::__construct('banner-rotator-widget', __('Banner Rotator'), $widget_ops);
		}
	 
		//The form
		public function form($instance) {
		
			$slider = new BannerRotator();
			$arrSliders = $slider->getArrSlidersShort();
					
			if(empty($arrSliders))
				echo __("No sliders found, Please create a slider");
			else{
				
				$field = "banner_rotator";
				$fieldPages = "banner_rotator_pages";
				$fieldCheck = "banner_rotator_homepage";
				
				$sliderID = UniteFunctionsBanner::getVal($instance, $field);
				$homepage = UniteFunctionsBanner::getVal($instance, $fieldCheck);
				$pagesValue = UniteFunctionsBanner::getVal($instance, $fieldPages);
				
				$fieldID = $this->get_field_id( $field );
				$fieldName = $this->get_field_name( $field );
				
				$select = UniteFunctionsBanner::getHTMLSelect($arrSliders,$sliderID,'name="'.$fieldName.'" id="'.$fieldID.'"',true);
				
				$fieldID_check = $this->get_field_id( $fieldCheck );
				$fieldName_check = $this->get_field_name( $fieldCheck );
				$checked = "";
				if($homepage == "on")
					$checked = "checked='checked'";
	
				$fieldPages_ID = $this->get_field_id( $fieldPages );
				$fieldPages_Name = $this->get_field_name( $fieldPages );
				
			?>
				Choose Slider: <?php echo $select?>
				<div style="padding-top:10px;"></div>
				
				<label for="<?php echo $fieldID_check?>">Home Page Only:</label>
				<input type="checkbox" name="<?php echo $fieldName_check?>" id="<?php echo $fieldID_check?>" <?php echo $checked?> >
				<br><br>
				<label for="<?php echo $fieldPages_ID?>">Pages: (example: 2,10) </label>
				<input type="text" name="<?php echo $fieldPages_Name?>" id="<?php echo $fieldPages_ID?>" value="<?php echo $pagesValue?>">
				
				<div style="padding-top:10px;"></div>
			<?php
			}
			 
		}
	 
		//Update
		public function update($new_instance, $old_instance) {
			
			return($new_instance);
		}	
		
		//Widget output
		public function widget($args, $instance) {			
			$sliderID = UniteFunctionsBanner::getVal($instance, "banner_rotator");
					
			$homepageCheck = UniteFunctionsBanner::getVal($instance, "banner_rotator_homepage");
			$homepage = "";
			if($homepageCheck == "on")
				$homepage = "homepage";
			
			$pages = UniteFunctionsBanner::getVal($instance, "banner_rotator_pages");
			if(!empty($pages)) {
				if(!empty($homepage))
					$homepage .= ",";
				$homepage .= $pages;
			}
					
			if(empty($sliderID))
				return(false);
							
			BannerRotatorOutput::putSlider($sliderID,$homepage);
		}
	 
	}
?>