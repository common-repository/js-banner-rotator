<?php
	global $bannerRotatorVersion;
	
	$wrapperClass = "";
	if(GlobalsBannerRotator::$isNewVersion == false)
		 $wrapperClass = " oldwp";
		 
	$nonce = wp_create_nonce("bannerrotator_actions");
?>
<script type="text/javascript">
	var g_bannerNonce = "<?php echo $nonce?>";
	var g_uniteDirPlagin = "<?php echo self::$dir_plugin?>";
	var g_urlContent = "<?php echo UniteFunctionsWPBanner::getUrlContent()?>";
	var g_urlAjaxShowImage = "<?php echo UniteBaseClassBanner::$url_ajax_showimage?>";
	var g_urlAjaxActions = "<?php echo UniteBaseClassBanner::$url_ajax_actions?>";
	var g_settingsObj = {};
	
</script>

<div id="div_debug"></div>

<div class='unite_error_message' id="error_message" style="display:none;"></div>

<div class='unite_success_message' id="success_message" style="display:none;"></div>

<div id="viewWrapper" class="view_wrapper<?php echo $wrapperClass?>">
	<?php self::requireView($view); ?>
</div>

<div id="divColorPicker" style="display:none;"></div>

<?php self::requireView("system/video_dialog")?>
<?php self::requireView("system/update_dialog")?>
<?php self::requireView("system/general_settings_dialog")?>

<div class="fb-plugin-version">&copy; All rights reserved, <a href="http://www.flashbluedesign.com/" target="_blank">flashblue</a>  ver. <?php echo $bannerRotatorVersion?>
	<a id="button_upload_plugin" class="button-primary btn-purple update_plugin" href="javascript:void(0)"><i class="icon-up-big"></i><?php _e("Update Plugin",BANNERROTATOR_TEXTDOMAIN)?></a>
</div>

<?php if(GlobalsBannerRotator::SHOW_DEBUG == true): ?>
	Debug Functions (for developer use only): 
	<br><br>
	
	<a id="button_update_text" class="button-primary" href="javascript:void(0)">Update Text</a>	
<?php endif?>

