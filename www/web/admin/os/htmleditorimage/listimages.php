<?php
	session_start();
	
	ini_set("display_errors",1);
	error_reporting(E_ALL);
	$offset="../../../";
	
	$path=isset($_GET['path']) ? $_GET['path'] : "directory/";
	$images_dir=$offset.$path;
	
	$valid_exts = array(".gif", ".jpg", ".jpeg", ".png");
	$list = array();
	
	$dir = opendir($images_dir);
	while ($file_name = readdir($dir)) {
		if (is_file($images_dir.$file_name)) {
			if (in_array(strtolower(strrchr($file_name, ".")), $valid_exts)) {
				
				if(strstr($file_name,"thumb_")==""){
					list($width, $height) = getimagesize($images_dir.$file_name);
					$size = filesize($images_dir.$file_name);
					
					$icon = (isset($_GET['r'])) ? $path.$file_name : "../".$path.$file_name;
					
					$list[] = array(
						"name" => utf8_encode($file_name), 
						"width" => $width,
						"height" => $height, 
						"size" => $size, 
						"type" => "file", 
						"url" => utf8_encode($icon)
					);
				 }
			}
		}else if($file_name!="." && $file_name!=".."){
			
			$icon = (isset($_GET['r'])) ? "admin/os/resources/shared/dossier.png" : "os/resources/shared/dossier.png";
			
			$list[] = array(
				"name" => utf8_encode($file_name), 
				"width" => false,
				"height" => false, 
				"size" => false, 
				"type" => "directory", 
				"url" => $icon
			);
		}
	}
  closedir($dir);
  

  
  echo json_encode(array("images" => $list));
?>
