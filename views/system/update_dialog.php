<div id="dialog_update_plugin" class="api_wrapper" title="<?php _e("Update Slider Plugin",BANNERROTATOR_TEXTDOMAIN)?>" style="display:none;">
	<div class="api-caption"><?php _e("Update Banner Rotator Plugin",BANNERROTATOR_TEXTDOMAIN)?>:</div>
	
	<div class="api-desc">
		<?php _e("To update the slider please show the slider install package. The files will be overwriten.",BANNERROTATOR_TEXTDOMAIN) ?>
		<br> <?php _e("File example: bannerrotator.zip")?>
	</div>
	
	<br>
	
	<form action="<?php echo UniteBaseClassBanner::$url_ajax?>" enctype="multipart/form-data" method="post">		    
		<input type="hidden" name="action" value="bannerrotator_ajax_action">
		<input type="hidden" name="client_action" value="update_plugin">
		<input type="hidden" name="nonce" value="<?php echo wp_create_nonce("bannerrotator_actions"); ?>">
		<?php _e("Choose the update file:",BANNERROTATOR_TEXTDOMAIN)?>   
		<br>
		<input type="file" name="update_file" class="input_update_slider">
		
		<input type="submit" class='button-secondary' value="<?php _e("Update Slider",BANNERROTATOR_TEXTDOMAIN)?>">
	</form>				
</div>

