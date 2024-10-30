<input type="hidden" id="sliderid" value="<?php echo $sliderID?>"></input>

<div class="wrap settings_wrap">
	<div class="clear_both"></div> 
	
		<div class="title_line">
			<div class="view_title">
				<i class="icon-cog"></i>
				<?php _e("Edit Slider",BANNERROTATOR_TEXTDOMAIN)?>
			</div>
		</div>
	
		<div class="settings_panel">
		
			<div class="settings_panel_left">
				
				<div id="main_dlier_settings_wrapper" class="postbox unite-postbox ">
				  <h3 class="box-closed"><span><?php _e("Main Slider Settings",BANNERROTATOR_TEXTDOMAIN) ?></span></h3>
				  <div class="p10">
						<?php $settingsSliderMain->draw("form_slider_main",true)?>
						
						<div id="layout-preshow">
							<strong><?php _e("Layout Example",BANNERROTATOR_TEXTDOMAIN)?></strong> <?php _e("(Can be different based on Theme Style)",BANNERROTATOR_TEXTDOMAIN)?>
							<div class="divide20"></div>
							<div id="layout-preshow-page">
								<div class="layout-preshow-text"><?php _e("BROWSER",BANNERROTATOR_TEXTDOMAIN)?></div>
								<div id="layout-preshow-theme">
										<div class="layout-preshow-text"><?php _e("PAGE",BANNERROTATOR_TEXTDOMAIN)?></div>
								</div>
								<div id="layout-preshow-slider">
										<div class="layout-preshow-text"><?php _e("SLIDER",BANNERROTATOR_TEXTDOMAIN)?></div>
								</div>
								<div id="layout-preshow-grid">
										<div class="layout-preshow-text"><?php _e("CAPTIONS GRID",BANNERROTATOR_TEXTDOMAIN)?></div>										
								</div>
							</div>
						</div>
						
						<div class="divide20"></div>
						
						<div class="buttons-wrapper">
							<a class='button-primary btn-green' href='javascript:void(0)' id="button_save_slider" ><i class="icon-cog"></i><?php _e("Save Settings",BANNERROTATOR_TEXTDOMAIN)?></a>
							<span id="loader_update" class="loader_round" style="display:none;"><?php _e("updating...",BANNERROTATOR_TEXTDOMAIN)?> </span>
							<span id="update_slider_success" class="success_message"></span>
							<a class="button-primary btn-red" id="button_delete_slider" href="javascript:void(0)"><i class="icon-trash"></i><?php _e("Delete Slider",BANNERROTATOR_TEXTDOMAIN)?></a>
							<a class="button-primary btn-yellow" id="button_close_slider_edit" href="<?php echo self::getViewUrl("sliders") ?>"><i class="icon-cancel"></i><?php _e("Close",BANNERROTATOR_TEXTDOMAIN)?></a>
							<a class="button-primary btn-blue" id="link_edit_slides" href="<?php echo $linksEditSlides?>"><i class="icon-pencil"></i><?php _e("Edit Slides",BANNERROTATOR_TEXTDOMAIN)?> </a>
							<a class="button-primary btn-grey" id="button_preview_slider" href="javascript:void(0)"><i class="icon-search"></i><?php _e("Preview",BANNERROTATOR_TEXTDOMAIN)?></a>
						</div>
						
						<div class="clear"></div>
					</div>
				</div>
				 
				<?php require self::getPathTemplate("slider_toolbox"); ?>
				<?php require self::getPathTemplate("slider_api"); ?>
				
			</div>
			
			<div class="settings_panel_right">
				<?php $settingsSliderParams->draw("form_slider_params",true); ?>
			</div>
			
			<div class="clear"></div>
			
		</div>

</div>

<?php require self::getPathTemplate("dialog_preview_slider");?>

<script type="text/javascript">
	jQuery(document).ready(function() {		
		BannerRotatorAdmin.initEditSliderView();
	});
</script>
	
