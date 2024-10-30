<?php	
	define("BANNERROTATOR_TEXTDOMAIN","bannerrotator");
	
	class GlobalsBannerRotator {
		
		const SHOW_DEBUG = false;
		const TABLE_SLIDERS_NAME = "bannerrotator_sliders";
		const TABLE_SLIDES_NAME = "bannerrotator_slides";
		const TABLE_SETTINGS_NAME = "bannerrotator_settings";
		
		const FIELDS_SLIDE = "slider_id,slide_order,params,layers";
		const FIELDS_SLIDER = "title,alias,params";
		
		const YOUTUBE_EXAMPLE_ID = "cXwQjHRZieI";
		const DEFAULT_YOUTUBE_ARGUMENTS = "enablejsapi=1&amp;html5=1&amp;hd=1&amp;wmode=opaque&amp;controls=1&amp;autoplay=1&amp;showinfo=0;rel=0;";
		const DEFAULT_VIMEO_ARGUMENTS = "title=0&amp;byline=0&amp;portrait=0;api=1";
		
		public static $table_sliders;
		public static $table_slides;
		public static $table_settings;
		public static $filepath_captions;
		public static $filepath_captions_original;
		public static $urlCaptionsCSS;
		public static $isNewVersion;
	}
?>