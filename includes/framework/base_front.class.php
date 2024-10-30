<?php
	class UniteBaseFrontClassBanner extends UniteBaseClassBanner {		
		
		const ACTION_ENQUEUE_SCRIPTS = "wp_enqueue_scripts";
		
		//Main constructor		 
		public function __construct($mainFile,$t) {			
			parent::__construct($mainFile,$t);
			
			self::addAction(self::ACTION_ENQUEUE_SCRIPTS, "onAddScripts");
		}	
		
	}
?>