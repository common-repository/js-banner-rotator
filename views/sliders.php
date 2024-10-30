<?php
	$slider = new BannerRotator();
	$arrSliders = $slider->getArrSliders();
	
	$addNewLink = self::getViewUrl(BannerRotatorAdmin::VIEW_SLIDER);
	
	require self::getPathTemplate("sliders");
?>


	