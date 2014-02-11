<?php
    
	class cropimage{
		
		var $info;
		var $sourceimage;
		var $endimage;
		var $file_name;
		
		function __construct() {
			$this->offset = "";
		}
		
		function __destruct() {
		}
		
		function setInfo($info){
			$info['filesize'] = getimagesize($info['image']);
			$this->info = $info;
		}
		
		function createthumb(){
			//$this->file_name = substr(md5(uniqid(rand(), true)), 0, 8).".jpg";
			switch($this->info['filesize'][2]){
				case 1: $this->sourceimage = imagecreatefromgif($this->info['image']); 		break;
				case 2: $this->sourceimage = imagecreatefromjpeg($this->info['image']); 	break;
				case 3: $this->sourceimage = imagecreatefrompng($this->info['image']); 		break;
			}
			$this->endimage = imagecreatetruecolor($this->info['end_w'], $this->info['end_h']);
			imagecopyresampled(
				$this->endimage, 
				$this->sourceimage, 
				0, 0,											// dst_x  dst_y
				$this->info['x1'], $this->info['y1'], 			// src_x src_y
				$this->info['end_w'], $this->info['end_h'],  	// dst_w dst_h
				$this->info['width'], $this->info['height'] 	// src_w src_h
			);
		}	
		
		function save(/*$string*/ $file=false){
			$file_name = ($file!=false) ? $file : $this->info['image'];
			imagejpeg($this->endimage,$file_name,90);
			chmod($file_name,0644);
			return $file_name;
		}
		
		function display(){
			imagejpeg($this->endimage,90);
		}
		
	}

?>