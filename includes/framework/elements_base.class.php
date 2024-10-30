<?php
	class UniteElementsBaseBanner {
		
		protected $db;
		
		public function __construct() {			
			$this->db = new UniteDBBanner();
		}
		
	}
?>