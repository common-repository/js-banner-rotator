<!-- Youtube dialog -->
<div id="dialog_video" class="dialog-video" title="<?php _e("Add Youtube Layout",BANNERROTATOR_TEXTDOMAIN)?>" style="display:none">
	
	<!-- Type chooser -->		
	<div id="video_type_chooser" class="video-type-chooser">
		<div class="choose-video-type float_left">
			<?php _e("Choose video type",BANNERROTATOR_TEXTDOMAIN)?>
		</div>
		
		<div class="video_type_radio_list float_left">
			<label for="video_radio_youtube"><?php _e("Youtube",BANNERROTATOR_TEXTDOMAIN)?></label>
			<input type="radio" checked id="video_radio_youtube" name="video_select">
			
			<label for="video_radio_vimeo"><?php _e("Vimeo",BANNERROTATOR_TEXTDOMAIN)?></label>
			<input type="radio" id="video_radio_vimeo" name="video_select">
			
			<label for="video_radio_html5"><?php _e("HTML5",BANNERROTATOR_TEXTDOMAIN)?></label>
			<input type="radio" id="video_radio_html5" name="video_select">			
		</div>
		<div style="clear:both"></div>			
		
		<hr>
		<div style="width:100%;height:15px;"></div>	
	</div>
	
	<div class="video_left" id="video-dialog-wrap">
		
		<!-- Vimeo block -->		
		<div id="video_block_vimeo" class="video-select-block" style="display:none;" >
			<div class="video-title" >
				<?php _e("Enter Vimeo ID or URL",BANNERROTATOR_TEXTDOMAIN)?>
			</div>
			
			<input type="text" id="vimeo_id" class="regular-text" value=""></input>
			&nbsp;
			<input type="button" id="button_vimeo_search" class="button-primary btn-blue" value="search">
			
			<img id="vimeo_loader" src="<?php echo self::$url_plugin?>/images/loader.gif" style="display:none">
			
			<div class="video_example">
				<?php _e("example:  30300114",BANNERROTATOR_TEXTDOMAIN)?>
			</div>		
		</div>
		
		<!-- Youtube block -->		
		<div id="video_block_youtube" class="video-select-block">		
			<div class="video-title">
				<?php _e("Enter Youtube ID or URL",BANNERROTATOR_TEXTDOMAIN)?>:
			</div>
			
			<input type="text" id="youtube_id" class="regular-text" value=""></input>
			&nbsp;
			<input type="button" id="button_youtube_search" class="button-primary btn-blue" value="search">
			
			<img id="youtube_loader" src="<?php echo self::$url_plugin?>/images/loader.gif" style="display:none">
			
			<div class="video_example">
				<?php _e("example",BANNERROTATOR_TEXTDOMAIN)?>:  <?php echo GlobalsBannerRotator::YOUTUBE_EXAMPLE_ID?>
			</div>			
		</div>
		
		<!-- Html5 block -->		
		<div id="video_block_html5" class="video-select-block" style="display:none;">			
			<ul>
				<li>
					<div class="video_title2">
					<?php _e("Poster Image Url")?>:
					</div>
					<input type="text" id="html5_url_poster" class="regular-text" value=""></input>
					<span class="video_example"><?php _e("Example",BANNERROTATOR_TEXTDOMAIN)?>: http://video-js.zencoder.com/oceans-clip.png</span>
				</li>
				<li>
					<div class="video_title2">				
					<?php _e("Video MP4 Url")?>:
					</div>
					<input type="text" id="html5_url_mp4" class="regular-text" value=""></input>
					<span class="video_example"><?php _e("Example",BANNERROTATOR_TEXTDOMAIN)?>: http://video-js.zencoder.com/oceans-clip.mp4</span>
				</li>
				<li>
					<div class="video_title2">								
					<?php _e("Video WEBM Url")?>:
					</div>
					<input type="text" id="html5_url_webm" class="regular-text" value=""></input>
					<span class="video_example"><?php _e("Example",BANNERROTATOR_TEXTDOMAIN)?>: http://video-js.zencoder.com/oceans-clip.webm</span>					
				</li>
				<li>
					<div class="video_title2">
					<?php _e("Video OGV Url")?>:
					</div>			
					<input type="text" id="html5_url_ogv" class="regular-text" value=""></input>
					<span class="video_example"><?php _e("Example",BANNERROTATOR_TEXTDOMAIN)?>: http://video-js.zencoder.com/oceans-clip.ogv</span>	
				</li>				
			</ul>			
		</div>		
		
		<!-- Video controls -->		
		<div id="video_hidden_controls" style="display:none;">		
			<div id="video_size_wrapper" class="youtube-inputs-wrapper">
				<?php _e("Width",BANNERROTATOR_TEXTDOMAIN)?>:
				<input type="text" id="input_video_width" class="small-text" value="320">
				&nbsp;&nbsp;&nbsp;
				<?php _e("Height",BANNERROTATOR_TEXTDOMAIN)?>:
				<input type="text" id="input_video_height" class="small-text" value="240">				
			</div>
			
			<div class="mtop_20">
				<label for="input_video_fullwidth" class="video-title float_left">
					<?php _e("Full Width:",BANNERROTATOR_TEXTDOMAIN)?>
				</label>
				
				<input type="checkbox" class="checkbox_video_dialog float_left" id="input_video_fullwidth" ></input>			
			</div>
			
			<div class="clear"></div>
			
			<div class="video-title mtop_20">
				<?php _e("Arguments:",BANNERROTATOR_TEXTDOMAIN)?>
			</div>
					
			<input type="text" id="input_video_arguments" class="regular-text" value="" data-youtube="<?php echo GlobalsBannerRotator::DEFAULT_YOUTUBE_ARGUMENTS?>" data-vimeo="<?php echo GlobalsBannerRotator::DEFAULT_VIMEO_ARGUMENTS?>" ></input>
			
			<div class="mtop_20">
				<label for="input_video_autoplay" class="video-title float_left">
					<?php _e("Autoplay:",BANNERROTATOR_TEXTDOMAIN)?>
				</label>
				
				<input type="checkbox" class="checkbox_video_dialog float_left" id="input_video_autoplay" ></input>
				
				<label for="input_video_nextslide" class="video-title float_left mleft_20">
					<?php _e("Next Slide On End:",BANNERROTATOR_TEXTDOMAIN)?>
				</label>
				
				<input type="checkbox" class="checkbox_video_dialog float_left" id="input_video_nextslide" ></input>				
			</div>
			
			<div class="clear"></div>	
							
			<div class="add-button-wrapper">
				<a href="javascript:void(0)" class="button-primary btn-blue" id="button-video-add" data-textadd="<?php _e("Add This Video",BANNERROTATOR_TEXTDOMAIN)?>" data-textupdate="<?php _e("Update Video",BANNERROTATOR_TEXTDOMAIN)?>" ><?php _e("Add This Video",BANNERROTATOR_TEXTDOMAIN)?></a>
			</div>
		</div>		
	</div>
	
	<div id="video_content" class="video_right"></div>		
	
</div>
