<?php	
	$sliderID = self::getGetVar("id");
	
	if(empty($sliderID))
		UniteFunctionsBanner::throwError("Slider ID not found"); 
	
	$slider = new BannerRotator();
	$slider->initByID($sliderID);
	$sliderParams = $slider->getParams();
	
	$arrSliders = $slider->getArrSlidersShort($sliderID);
	$selectSliders = UniteFunctionsBanner::getHTMLSelect($arrSliders,"","id='selectSliders'",true);
	
	$numSliders = count($arrSliders);
	
	//Set iframe parameters	
	$width = $sliderParams["width"];
	$height = $sliderParams["height"];
	
	$iframeWidth = $width+60;
	$iframeHeight = $height+50;
	
	$iframeStyle = "width:{$iframeWidth}px;height:{$iframeHeight}px;";
	
	//Handle wpml
	$isWpmlExists = UniteWpmlBanner::isWpmlExists();
	$useWpml = $slider->getParam("useWpml","false");
	
	$wpmlActive = false;
	if($isWpmlExists && $useWpml=="true") {
		$wpmlActive = true;
		
		//Get langs floating menu
		$urlIconDelete = self::$url_plugin."images/icon-trash.png";
		$urlIconEdit = self::$url_plugin."images/icon-edit.png";
		$urlIconPreview = self::$url_plugin."images/preview.png";
		
		$textDelete = __("Delete Slide",BANNERROTATOR_TEXTDOMAIN);
		$textEdit = __("Edit Slide",BANNERROTATOR_TEXTDOMAIN);
		$textPreview = __("Preview Slide",BANNERROTATOR_TEXTDOMAIN);
		
		$htmlBefore = "";
		$htmlBefore .= "<li class='item_operation operation_delete'><a data-operation='delete' href='javascript:void(0)'>"."\n";
		$htmlBefore .= "<img src='{$urlIconDelete}'/> {$textDelete}"."\n";				
		$htmlBefore .= "</a></li>"."\n";
		
		$htmlBefore .= "<li class='item_operation operation_edit'><a data-operation='edit' href='javascript:void(0)'>"."\n";
		$htmlBefore .= "<img src='{$urlIconEdit}'/> {$textEdit}"."\n";				
		$htmlBefore .= "</a></li>"."\n";
		
		$htmlBefore .= "<li class='item_operation operation_preview'><a data-operation='preview' href='javascript:void(0)'>"."\n";
		$htmlBefore .= "<img src='{$urlIconPreview}'/> {$textPreview}"."\n";				
		$htmlBefore .= "</a></li>"."\n";
		
		$htmlBefore .= "<li class='item_operation operation_sap'>"."\n";
		$htmlBefore .= "<div class='float_menu_sap'></div>"."\n";
		$htmlBefore .= "</a></li>"."\n";
		
		$langFloatMenu = UniteWpmlBanner::getLangsWithFlagsHtmlList("id='slides_langs_float' class='slides_langs_float'",$htmlBefore);
	}	
	
	$arrSlides = $slider->getSlides();
	$numSlides = count($arrSlides);
	
	$linksSliderSettings = self::getViewUrl(BannerRotatorAdmin::VIEW_SLIDER,"id=$sliderID");
	
	$patternViewSlide = self::getViewUrl("slide","id=[slideid]");
	
	require self::getPathTemplate("slides");	
?>
	