<div class="wrap settings_wrap">
	<div class="clear_both"></div> 

	<div class="title_line">
		<div class="view_title">
			<i class="icon-list-add"></i>
			<?php _e("New Slider",BANNERROTATOR_TEXTDOMAIN)?>
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
						<a class="button-primary btn-green" id="button_save_slider" href="javascript:void(0)"><i class="icon-cog"></i><?php _e("Create Slider",BANNERROTATOR_TEXTDOMAIN)?></a>
						<a class="button-primary btn-red" id="button_cancel_save_slider" href="<?php echo self::getViewUrl("sliders") ?>"><i class="icon-cancel"></i><?php _e("Close",BANNERROTATOR_TEXTDOMAIN)?></a>
					</div>
					
					<div class="clear"></div>
				</div>
			</div>
			 
		</div>
		
		<div class="settings_panel_right">
			<?php $settingsSliderParams->draw("form_slider_params",true); ?>
		</div>
		
		<div class="clear"></div>
		
	</div>

</div>

<script type="text/javascript">
	jQuery(document).ready(function() {		
		BannerRotatorAdmin.initAddSliderView();
	});
</script>
	
