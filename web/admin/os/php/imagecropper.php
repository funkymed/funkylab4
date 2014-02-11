<?php



	function createthumb($file,$_maxW,$_maxH,$savename=false){
		if(is_file($file) && $size = getimagesize($file)){
			
			$width	= $size[0];
			$height	= $size[1]; 
			
			if(($width!=$_maxW || $height!=$_maxH) && ($width<=1800 && $height<=1800)){
				
				//-- DEBUT ThumbCropped
			
				if ($_maxW>$_maxH){
					$maxW = $_maxW;
					$maxH = $_maxW;
				}else{
					$maxW = $_maxH;
					$maxH = $_maxH;
				}
				
				$ratio	= ($width<$height ) ? $width/$maxW : $height/$maxH; // Paysage  ou portrait
				
				$thumbW	= floor($width/$ratio);
				$thumbH	= floor($height/$ratio);	
					

				//-- FIN ThumbCropped
			
				//Charge l'image
				switch($size[2]){
					case 1: $image_src = imagecreatefromgif($file); 	break;
					case 2: $image_src = imagecreatefromjpeg($file); 	break;
					case 3: $image_src = imagecreatefrompng($file); 	break;
				}
				//Creation conteneur vide
				$image_resized=imagecreatetruecolor($thumbW,$thumbH);
				//Copie resizé de l'image dans le conteneur
				imagecopyresampled($image_resized, $image_src, 0, 0, 0, 0, $thumbW, $thumbH, $width, $height);
				
				//Creation d'un autre conteneur vide
				$image_dest	= imagecreatetruecolor($_maxW,$_maxH);
				
				//Position de l'image pour la decoupe
				$posX	= floor($thumbW/2-$_maxW/2);
				$posY	= floor($thumbH/2-$_maxH/2);
				
				//Decoupe de l'image resizé pour caller a la taille final
				imagecopy($image_dest,$image_resized,0,0,$posX,$posY,$_maxW,$_maxH); 
				
				//Sauvegarde
				if($savename!=false){
					imagejpeg($savename,$file,75);
				}else{
					imagejpeg($image_dest,$file,75);
				}
				
				chmod($file,0644);
			
			}
		}
	}

	
		
?>