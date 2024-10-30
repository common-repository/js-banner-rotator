<?php
	class UniteZipBanner {
		
		private $zip;
		
		//Get true / false if the zip archive exists.
		public static function isZipExists() {
			$exists = class_exists("ZipArchive");
			return $exists;
		}		
		
	    //Add zip file
	    private function addItem($basePath,$path) {	    	
	    	$rel_path = str_replace($basePath."/", "", $path);
	    	
	    	if(is_dir($path)) {	    		
		    	//Add dir to zip
		    	if($basePath != $path)
		    		$this->zip->addEmptyDir($rel_path);
	    		
	    		$files = scandir($path);
	    		foreach($files as $file) {
	    			if($file == "." || $file == ".." || $file == ".svn")
	    				continue;
	    			$filepath = $path."/".$file;
	    			$this->addItem($basePath, $filepath);
	    		}
	    	} else {	
				//File
	    		if(!file_exists($path))
	    			throwError("filepath: '$path' don't exists, can't zip");
	    		
	    		$this->zip->addFile($path,$rel_path);
	    	}
	    }	    	    

	    //Make zip archive
	    //If exists additional paths, add additional items to the zip
	    public function makeZip($srcPath, $zipFilepath,$additionPaths = array()) {
	    	
	    	if(!is_dir($srcPath))
	    		throwError("The path: '$srcPath' don't exists, can't zip");
	    	
	        $this->zip = new ZipArchive;
	        $success = $this->zip->open($zipFilepath, ZipArchive::CREATE);
	        
	        if($success == false)
	        	throwError("Can't create zip file: $zipFilepath");
	        
	        $this->addItem($srcPath,$srcPath);
	       	
	        if(gettype($additionPaths) != "array")
	        	throwError("Wrong additional paths variable.");	       	
	        	
	        //Add additional paths
	        if(!empty($additionPaths))
	        	foreach($additionPaths as $path) {
	        		if(!is_dir($path))
	        			throwError("Path: $path not found, can't zip");
	        		$this->addItem($path, $path);
	        	}
	        
           	$this->zip->close();
	    }
	    
	    //Extract zip archive
	    public function extract($src, $dest) {
	        $zip = new ZipArchive;
	        if ($zip->open($src)===true) {
	            $zip->extractTo($dest);
	            $zip->close();
	            return true;
	        }
	        return false;
	    }	    
	    
	}
?>