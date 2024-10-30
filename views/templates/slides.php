<div class="wrap settings_wrap">
	<div class="clear_both"></div> 

	<div class="title_line">
		<div class="view_title">
			<i class="icon-pencil"></i>
			<?php _e("Edit Slides",BANNERROTATOR_TEXTDOMAIN)?>: <?php echo $slider->getTitle()?>
		</div>
	</div>
	
	<div class="vert_sap"></div>
	<?php if($numSlides >= 5):?>
		<a class='button-primary btn-blue' id="button_new_slide_top" href='javascript:void(0)'><i class="icon-list-add"></i><?php _e("New Slide",BANNERROTATOR_TEXTDOMAIN)?></a>
		<a class='button-primary btn-blue' id="button_new_slide_transparent_top" href='javascript:void(0)'><i class="icon-list-add"></i><?php _e("New Transparent Slide",BANNERROTATOR_TEXTDOMAIN)?></a>
		<span class="loader_round new_trans_slide_loader" style="display:none"><?php _e("Adding Slide...",BANNERROTATOR_TEXTDOMAIN)?></span>		
		<a class="button_close_slide button-primary btn-yellow" href='<?php echo self::getViewUrl(BannerRotatorAdmin::VIEW_SLIDERS);?>'><i class="icon-cancel"></i><?php _e("Close",BANNERROTATOR_TEXTDOMAIN)?></a>
	<?php endif?>
	
	<?php if($wpmlActive == true):?>
		<div id="langs_float_wrapper" class="langs_float_wrapper" style="display:none">
			<?php echo $langFloatMenu?>
		</div>
	<?php endif?>
	
	<div class="vert_sap"></div>
	<div class="sliders_list_container">
		<?php require self::getPathTemplate("slides_list");?>
	</div>
	
	<div class="vert_sap_medium"></div>
	
	<a class='button-primary btn-blue' id="button_new_slide" data-dialogtitle="<?php _e("Select image or multiple images to add slide or slides",BANNERROTATOR_TEXTDOMAIN)?>" href='javascript:void(0)'><i class="icon-list-add"></i><?php _e("New Slide",BANNERROTATOR_TEXTDOMAIN)?></a>
	<a class='button-primary btn-blue' id="button_new_slide_transparent" href='javascript:void(0)'><i class="icon-list-add"></i><?php _e("New Transparent Slide",BANNERROTATOR_TEXTDOMAIN)?></a>
	<span class="loader_round new_trans_slide_loader" style="display:none"><?php _e("Adding Slide...",BANNERROTATOR_TEXTDOMAIN)?></span>		
	<a class="button_close_slide button-primary btn-yellow" href='<?php echo self::getViewUrl(BannerRotatorAdmin::VIEW_SLIDERS);?>'><i class="icon-cancel"></i><?php _e("Close",BANNERROTATOR_TEXTDOMAIN)?></a>
	<a class='button-primary btn-green' href="<?php echo $linksSliderSettings?>"><i class="icon-cog"></i><?php _e("Slider Settings",BANNERROTATOR_TEXTDOMAIN)?></a>
	
</div>

<div id="dialog_copy_move" data-textclose="<?php _e("Close")?>" data-textupdate="<?php _e("Do It!")?>" title="<?php _e("Copy / move slide",BANNERROTATOR_TEXTDOMAIN)?>" style="display:none">
	<br>
	
	<?php _e("Choose Slider",BANNERROTATOR_TEXTDOMAIN)?> :
	<?php echo $selectSliders?>
	
	<br><br>
	
	<?php _e("Choose Operation")?> :
	 
	<input type="radio" id="radio_copy" value="copy" name="copy_move_operation" checked />
	<label for="radio_copy" style="cursor:pointer;"><?php _e("Copy",BANNERROTATOR_TEXTDOMAIN)?></label>
	&nbsp; &nbsp;
	<input type="radio" id="radio_move" value="move" name="copy_move_operation" />
	<label for="radio_move" style="cursor:pointer;"><?php _e("Move",BANNERROTATOR_TEXTDOMAIN)?></label>	
</div>

<?php require self::getPathTemplate("dialog_preview_slide");?>

<script type="text/javascript">
	jQuery(document).ready(function() {		
		BannerRotatorAdmin.initSlidesListView("<?php echo $sliderID?>");		
	});	
</script>
	