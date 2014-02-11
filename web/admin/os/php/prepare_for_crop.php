<?php
	function createthumbWithBorder($filename,$wi, $he,$outputfile){
			
		$size = getimagesize($filename);
		
		$width		= $size[0];
		$height		= $size[1]; 
		
		$ratio = ($width>$height) ? $width / $wi : $height / $he;
		
		$thumbW  	= $width/$ratio;
		$thumbH 	= $height/$ratio;
		
		$pos_x		= ($wi-$thumbW)/2;
		$pos_y		= ($he-$thumbH)/2;
			
		switch($size[2]){
			case 1: $image_src = imagecreatefromgif($filename); 	break;
			case 2: $image_src = imagecreatefromjpeg($filename); 	break;
			case 3: $image_src = imagecreatefrompng($filename); 	break;
		}
		
		$image_resized	= 	imagecreatetruecolor($wi,$he);
		
		imagefill($image_resized, 0, 0, imagecolorallocate($image_resized, 255, 255, 255));
		
		imagecopyresampled(
			$image_resized, $image_src, 
			$pos_x,$pos_y,					 	// Dest
			0, 0, 								// Source
			$thumbW, $thumbH, 					// Dest
			$width, $height 					// Source
		);
		
		imagejpeg($image_resized,$outputfile,90);
		chmod($outputfile,0644);
		return true;
		
		
	}	
	
	
	
	function generateKey($lenght){
		$string = "";
		$string_a = array("A","B","C","D","E","F","G","H","J","K","L","M","N","P","R","S","T","U","V","W","X","Y","Z","1","2","3","4","5","6","7","8","9");
	     for($xx=0;$xx<$lenght;$xx++){
		    $offset=rand(0,count($string_a)-1);
		    $string.=$string_a[$offset];
	     }
		return $string;
	}
	
	if(isset($_REQUEST['file'])){
		$file = "../../../".$_REQUEST['file'];
		$outputfile = dirname($file)."/tmp_".generateKey(8).".jpg";
		if(createthumbWithBorder($file,320, 240,$outputfile)){ // Thumbnail
			print str_replace("../","",$outputfile);
		}else{
			print "0";
		}
	}
 	
?>