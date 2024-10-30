<table class='wp-list-table widefat fixed unite_table_items'>
	<thead>
		<tr>
			<th width='20px'><?php _e("ID",BANNERROTATOR_TEXTDOMAIN)?></th>
			<th width='25%'><?php _e("Name",BANNERROTATOR_TEXTDOMAIN)?></th>
			<th width='15%'><?php _e("Shortcode",BANNERROTATOR_TEXTDOMAIN)?> </th>			
			<th width='60px'><?php _e("N. Slides",BANNERROTATOR_TEXTDOMAIN)?></th>						
			<th width='50%'><?php _e("Actions",BANNERROTATOR_TEXTDOMAIN)?> </th>			
		</tr>
	</thead>
	<tbody>
		<?php foreach($arrSliders as $slider):				
			$id = $slider->getID();
			$showTitle = $slider->getShowTitle();
			$title = $slider->getTitle();
			$alias = $slider->getAlias();
			$shortCode = $slider->getShortcode();
			$numSlides = $slider->getNumSlides();
			
			$editLink = self::getViewUrl(BannerRotatorAdmin::VIEW_SLIDER,"id=$id");
			$editSlidesLink = self::getViewUrl(BannerRotatorAdmin::VIEW_SLIDES,"id=$id");
			
			$showTitle = UniteFunctionsBanner::getHtmlLink($editLink, $showTitle);				
		?>
			<tr>
				<td><?php echo $id?><span id="slider_title_<?php echo $id?>" class="hidden"><?php echo $title?></span></td>								
				<td><?php echo $showTitle?></td>
				<td><?php echo $shortCode?></td>
				<td><?php echo $numSlides?></td>
				<td>
					<a class="button-primary btn-green button-edit-slider" href='<?php echo $editLink ?>'><i class="icon-cog"></i><?php _e("Settings",BANNERROTATOR_TEXTDOMAIN)?></a>
					<a class="button-primary btn-blue button-edit-slides" href='<?php echo $editSlidesLink ?>'><i class="icon-pencil"></i><?php _e("Edit Slides",BANNERROTATOR_TEXTDOMAIN)?></a>
					<a class="button-primary btn-orange export_slider_overview" id="export_slider_<?php echo $id?>" href="javascript:void(0);" title=""><i class="icon-export"></i><?php _e("Export Slider",BANNERROTATOR_TEXTDOMAIN)?></a>
					<a class="button-primary btn-red button_delete_slider" id="button_delete_<?php echo $id?>" href='javascript:void(0)' title='<?php _e("Delete",BANNERROTATOR_TEXTDOMAIN)?>'><i class="icon-trash"></i></a>
					<a class="button-primary btn-yellow button_duplicate_slider" id="button_duplicate_<?php echo $id?>" href='javascript:void(0)' title='<?php _e("Duplicate",BANNERROTATOR_TEXTDOMAIN)?>'><i class="icon-picture"></i></a>					
					<div class="button-primary btn-grey button_slider_preview" id="button_preview_<?php echo $id?>" title="<?php _e("Preview",BANNERROTATOR_TEXTDOMAIN)?>"><i class="icon-search"></i></div>
				</td>
			</tr>							
		<?php endforeach;?>		
	</tbody>		 
</table>

<?php require_once self::getPathTemplate("dialog_preview_slider");?>


	