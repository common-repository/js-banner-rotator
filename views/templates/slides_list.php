<div class="postbox box-slideslist">
	<h3>
		<span class='slideslist-title'><?php _e("Slides List",BANNERROTATOR_TEXTDOMAIN)?></span>
		<span id="saving_indicator" class='slideslist-loading'><?php _e("Saving Order")?>...</span>
	</h3>
	<div class="inside">
		<?php if(empty($arrSlides)):?>
		<?php _e("No Slides Found",BANNERROTATOR_TEXTDOMAIN)?>
		<?php endif?>		
		
		<ul id="list_slides" class="list_slides ui-sortable">		
			<?php foreach($arrSlides as $slide):				
				$bgType = $slide->getParam("background_type","image");
				
				$order = $slide->getOrder();
				
				//Set language flag url
				$isWpmlExists = UniteWpmlBanner::isWpmlExists();
				$useWpml = $slider->getParam("useWpml","false");
				$showLangs = false;
				$addItemStyle = "";
				if($isWpmlExists && $useWpml == "true"){
					$showLangs = true;
					$arrChildLangs = $slide->getArrChildrenLangs();
					$arrSlideLangCodes = $slide->getArrChildLangCodes();					
					
					if(UniteWpmlBanner::isAllLangsInArray($arrSlideLangCodes))
						$addItemStyle = "style='display:none'";
				}
				
				$imageFilepath = $slide->getImageFilepath();									
				$urlImageForView = $slide->getThumbUrl();
				
				$slideTitle = $slide->getParam("title","Slide");
				$title = $slideTitle;
				$filename = $slide->getImageFilename();
				
				$imageAlt = stripslashes($slideTitle);
				if(empty($imageAlt))
					$imageAlt = "slide";
				
				if($bgType == "image")
					$title .= " ({$filename})";
				
				$slideid = $slide->getID();
				
				$urlEditSlide = self::getViewUrl(BannerRotatorAdmin::VIEW_SLIDE,"id=$slideid");
				$linkEdit = UniteFunctionsBanner::getHtmlLink($urlEditSlide, $title);
				
				$state = $slide->getParam("state","published");
				
			?>
				<li id="slidelist_item_<?php echo $slideid?>" class="ui-state-default">
				
					<span class="slide-col col-order">
						<span class="order-text"><?php echo $order?></span>
						<div class="state_loader" style="display:none;"></div>
						<?php if($state == "published"):?>
						<div class="icon_state state_published" data-slideid="<?php echo $slideid?>" title="<?php _e("Unpublish Slide",BANNERROTATOR_TEXTDOMAIN)?>"></div>
						<?php else:?>
						<div class="icon_state state_unpublished" data-slideid="<?php echo $slideid?>" title="<?php _e("Publish Slide",BANNERROTATOR_TEXTDOMAIN)?>"></div>
						<?php endif?>
						
						<div class="icon_slide_preview" title="Preview Slide" data-slideid="<?php echo $slideid?>"></div>
						
					</span>
					
					<span class="slide-col col-name">
						<div class="slide-title-in-list"><?php echo $linkEdit?></div>
						<a class='button-primary btn-green' href='<?php echo $urlEditSlide?>'><i class="icon-pencil"></i><?php _e("Edit Slide",BANNERROTATOR_TEXTDOMAIN)?></a>
					</span>
					
					<span class="slide-col col-image">
						<?php switch($bgType):
								default:
								case "image":
									?>
									<div id="slide_image_<?php echo $slideid?>" style="background-image:url('<?php echo $urlImageForView?>')" class="slide_image" title="Slide Image - Click to change"></div>
									<?php 
								break;
								case "solid":
									$bgColor = $slide->getParam("slide_bg_color","#d0d0d0");
									?>
									<div class="slide_color_preview" style="background-color:<?php echo $bgColor?>"></div>
									<?php 
								break;
								case "trans":
									?>
									<div class="slide_color_preview_trans"></div>
									<?php 
								break;
								endswitch;  ?>
					</span>
					
					<span class="slide-col col-operations">
						<a class='button-primary btn-red button_delete_slide' id="button_delete_slide_<?php echo $slideid?>" href='javascript:void(0)'><i class="icon-trash"></i><?php _e("Delete",BANNERROTATOR_TEXTDOMAIN)?></a>
						<span class="loader_round loader_delete" style="display:none;"><?php _e("Deleting Slide...")?></span>
						<a class='button-primary btn-yellow button_duplicate_slide' id="button_duplicate_slide_<?php echo $slideid?>" href='javascript:void(0)'><i class="icon-picture"></i><?php _e("Duplicate",BANNERROTATOR_TEXTDOMAIN)?></a>
						<?php
							$copyButtonClass = "button-primary btn-blue button_copy_slide";
							$copyButtonTitle = __("Open copy / move dialog",BANNERROTATOR_TEXTDOMAIN);
							
							 if($numSliders == 0) {
								$copyButtonClass .= " button-disabled";
								$copyButtonTitle = "Copy / move disabled, no more sliders found";
							}
						?>
						<a class='<?php echo $copyButtonClass?>' id="button_copy_slide_<?php echo $slideid?>" title="<?php echo $copyButtonTitle?>" href='javascript:void(0)'><i class="icon-docs"></i><?php _e("Copy / Move",BANNERROTATOR_TEXTDOMAIN)?></a>							
						<span class="loader_round loader_copy mtop_10 mleft_20 display_block" style="display:none;"><?php _e("Working...")?></span>
					</span>
					
					<span class="slide-col col-handle">
						<div class="col-handle-inside">
							<span class="ui-icon ui-icon-arrowthick-2-n-s"></span>
						</div>
					</span>	
					<div class="clear"></div>
					
					<?php if($showLangs == true):?>
						<ul class="list_slide_icons">
							<?php foreach($arrChildLangs as $arrLang):
									$isParent = UniteFunctionsBanner::boolToStr($arrLang["isparent"]);
									$childSlideID = $arrLang["slideid"];
									$lang = $arrLang["lang"];
									$urlFlag = UniteWpmlBanner::getFlagUrl($lang);
									$langTitle = UniteWpmlBanner::getLangTitle($lang);	
							?>
							<li>
								<img id="icon_lang_<?php echo $childSlideID?>" class="icon_slide_lang" src="<?php echo $urlFlag?>" title="<?php echo $langTitle?>" data-slideid="<?php echo $childSlideID?>" data-lang="<?php echo $lang?>" data-isparent="<?php echo $isParent?>">
								<div class="icon_lang_loader loader_round" style="display:none"></div>								
							</li>
							<?php endforeach?>
							<li>
								<div id="icon_add_lang_<?php echo $slideid?>" class="icon_slide_lang_add" data-operation="add" data-slideid="<?php echo $slideid?>" <?php echo $addItemStyle?>></div>
								<div class="icon_lang_loader loader_round" style="display:none"></div>
							</li>
						</ul>						
					<?php endif?>
				</li>
			<?php endforeach;?>
		</ul>
		
	</div>
</div>