<?php
	$exampleID = '"slider1"';
	if(!empty($arrSliders))
		$exampleID = '"'.$arrSliders[0]->getAlias().'"';
?>
	<div class='wrap'>
		<div class="clear_both"></div> 

		<div class="title_line">
			<div class="view_title">
				<?php _e("Banner Rotators",BANNERROTATOR_TEXTDOMAIN)?>
			</div>
			<span class="float_right mtop_10">				
				<a id="button_general_settings" class="button-primary btn-darkblue"><i class="icon-edit"></i><?php _e("Global Settings",BANNERROTATOR_TEXTDOMAIN)?></a>			
			</span>
		</div>

		<?php if(empty($arrSliders)): ?>
			<?php _e("No Sliders Found",BANNERROTATOR_TEXTDOMAIN)?>
			<br>
		<?php else:
			 require self::getPathTemplate("sliders_list");	 		
		endif?>
			
		<p>			
			<a class='button-primary btn-blue' href='<?php echo $addNewLink?>'><i class="icon-list-add"></i><?php _e("Create New Slider",BANNERROTATOR_TEXTDOMAIN)?> </a>			
			<a id="button_import_slider" class='button-primary btn-turquoise float_right' href='javascript:void(0)'><i class="icon-login"></i><?php _e("Import Slider",BANNERROTATOR_TEXTDOMAIN)?> </a>		
		</p>
		 
		<br>
		 
		<div>		
			<h3> <?php _e("How To Use",BANNERROTATOR_TEXTDOMAIN)?>:</h3>
			
			<ul>
				<li><?php _e("* From the",BANNERROTATOR_TEXTDOMAIN)?> <b><?php _e("page and/or post editor",BANNERROTATOR_TEXTDOMAIN)?></b> <?php _e("insert the shortcode from the sliders table",BANNERROTATOR_TEXTDOMAIN)?></li>
				<li>
					<?php _e("* From the")?> <b><?php _e("theme html",BANNERROTATOR_TEXTDOMAIN)?></b> <?php _e("use",BANNERROTATOR_TEXTDOMAIN)?>: <code>&lt?php putBannerRotator( "alias" ) ?&gt</code> <?php _e("example",BANNERROTATOR_TEXTDOMAIN)?>: <code>&lt?php putBannerRotator(<?echo $exampleID?>) ?&gt</code>
					<br>
					&nbsp;&nbsp; <?php _e("For show only on homepage use",BANNERROTATOR_TEXTDOMAIN)?>: <code>&lt?php putBannerRotator(<?echo $exampleID?>,"homepage") ?&gt</code>
					<br>&nbsp;&nbsp; <?php _e("For show on certain pages use")?>: <code>&lt?php putBannerRotator(<?echo $exampleID?>,"2,10") ?&gt</code> 
				</li>
				<li><?php _e("* From the",BANNERROTATOR_TEXTDOMAIN)?> <b><?php _e("widgets panel",BANNERROTATOR_TEXTDOMAIN)?></b> <?php _e("drag the \"Banner Rotator\" widget to the desired sidebar",BANNERROTATOR_TEXTDOMAIN)?></li>
			</ul>		
		</div>
		
		<p></p>
		
	</div>
		
	<!-- Import slider dialog -->
	<div id="dialog_import_slider" title="<?php _e("Import Slider",BANNERROTATOR_TEXTDOMAIN)?>" class="dialog_import_slider" style="display:none">
		<br><br><br>
		<form action="<?php echo UniteBaseClassBanner::$url_ajax?>" enctype="multipart/form-data" method="post">			
			<input type="hidden" name="action" value="bannerrotator_ajax_action">
			<input type="hidden" name="client_action" value="import_slider_slidersview">
			<input type="hidden" name="nonce" value="<?php echo wp_create_nonce("bannerrotator_actions"); ?>">			
			<?php _e("Choose the import file",BANNERROTATOR_TEXTDOMAIN)?>:   
			<br>
			<input type="file" size="60" name="import_file" class="input_import_slider">
			<br><br>
			<input type="submit" class='button-primary btn-blue' value="<?php _e("Import Slider",BANNERROTATOR_TEXTDOMAIN)?>">
		</form>	
	</div>
	
	<script type="text/javascript">
		jQuery(document).ready(function() {
			BannerRotatorAdmin.initSlidersListView();
		});
	</script>
	