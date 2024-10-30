<?php
	$api = "bannerapi".$sliderID;
?>	
<div id="api_wrapper" class="api_wrapper postbox unite-postbox ">
	<h3 class="box_closed fb-accordion fba-closed"><div class="postbox-arrow"></div><span><?php _e("API Functions",BANNERROTATOR_TEXTDOMAIN) ?></span></h3>
	<div class="toggled-content fb-closedatstart p20">
		<div class="api-caption"><?php _e("API Methods",BANNERROTATOR_TEXTDOMAIN)?>:</div>
		<div class="divide20"></div>
		<div class="api-desc"><?php _e("Please copy / paste those functions into your functions js file",BANNERROTATOR_TEXTDOMAIN)?>. </div>
		
		<table class="api-table">
			<tr>
				<td class="api-cell1"><?php _e("Pause Slider",BANNERROTATOR_TEXTDOMAIN)?>:</td>
				<td class="api-cell2"><input type="text" readonly="readonly" class="api-input" value="<?php echo $api?>.brPause();"></td>
			</tr>
			<tr>
				<td class="api-cell1"><?php _e("Resume Slider",BANNERROTATOR_TEXTDOMAIN)?>:</td>
				<td class="api-cell2"><input type="text" readonly="readonly" class="api-input" value="<?php echo $api?>.brResume();"></td>
			</tr>
			<tr>
				<td class="api-cell1"><?php _e("Previous Slide",BANNERROTATOR_TEXTDOMAIN)?>:</td>
				<td class="api-cell2"><input type="text" readonly="readonly" class="api-input" value="<?php echo $api?>.brPrev();"></td>
			</tr>
			<tr>
				<td class="api-cell1"><?php _e("Next Slide",BANNERROTATOR_TEXTDOMAIN)?>:</td>
				<td class="api-cell2"><input type="text" readonly="readonly" class="api-input" value="<?php echo $api?>.brNext();"></td>
			</tr>
			<tr>
				<td class="api-cell1"><?php _e("Get Total Slides",BANNERROTATOR_TEXTDOMAIN)?>:</td>
				<td class="api-cell2"><input type="text" readonly="readonly" class="api-input" value="<?php echo $api?>.brMaxSlide();"></td>
			</tr>
			<tr>
				<td class="api-cell1"><?php _e("Go To Slide",BANNERROTATOR_TEXTDOMAIN)?>:</td>
				<td class="api-cell2"><input type="text" readonly="readonly" class="api-input" value="<?php echo $api?>.brShowSlide(2);"></td>
			</tr>
			<tr>
				<td class="api-cell1"><?php _e("Get Current Slide Number",BANNERROTATOR_TEXTDOMAIN)?>:</td>
				<td class="api-cell2"><input type="text" readonly="readonly" class="api-input" value="<?php echo $api?>.brCurrentSlide();"></td>
			</tr>
			<tr>
				<td class="api-cell1"><?php _e("Get Last Playing Slide Number",BANNERROTATOR_TEXTDOMAIN)?>:</td>
				<td class="api-cell2"><input type="text" readonly="readonly" class="api-input" value="<?php echo $api?>.brLastSlide();"></td>
			</tr>
			<tr>
				<td class="api-cell1"><?php _e("External Scroll",BANNERROTATOR_TEXTDOMAIN)?>:</td>
				<td class="api-cell2"><input type="text" readonly="readonly" class="api-input" value="<?php echo $api?>.brScroll(offset);"></td>
			</tr>
			
		</table>
		<div class="divide20"></div>
		<hr>
		<div class="divide20"></div>					
		<div class="api-caption"><?php _e("API Events",BANNERROTATOR_TEXTDOMAIN)?>:</div>
		<div class="divide20"></div>
		<div class="api-desc"><?php _e("Copy and Paste the Below listed API Functions into your jQuery Functions for Banner Rotator Event Listening",BANNERROTATOR_TEXTDOMAIN)?>.</div>
		<textarea id="api_area" readonly>					
<?php echo $api?>.bind("banner_rotator.onloaded", function (e,data) {
	//alert("slider loaded");
});
	
<?php echo $api?>.bind("banner_rotator.onchange", function (e,data) {
	//alert("slide changed to: "+data.slideIndex);
});
	
<?php echo $api?>.bind("banner_rotator.onpause", function(e,data) {
	//alert("timer paused");
});
	
<?php echo $api?>.bind("banner_rotator.onresume", function(e,data) {
	//alert("timer resume");
});
	
<?php echo $api?>.bind("banner_rotator.onvideoplay", function(e,data) {
	//alert("video play");
});
	
<?php echo $api?>.bind("banner_rotator.onvideostop", function(e,data) {
	//alert("video stopped");
});
	
<?php echo $api?>.bind("banner_rotator.onstop", function(e,data) {
	//alert("slider stopped");
});
	
<?php echo $api?>.bind("banner_rotator.onbeforeswap", function(e,data) {
	//alert("swap slide started");
});
	
<?php echo $api?>.bind("banner_rotator.onafterswap", function(e,data) {
	//alert("swap slide complete");
});
		</textarea>
	</div>
</div>

<script type="text/javascript">
	jQuery(document).ready(function() {			
		BannerRotatorAdmin.initEditSlideView();
	});
</script>
