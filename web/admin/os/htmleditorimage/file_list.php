<?php
	session_start();
	
	
	$pathFront =   isset($_GET['r'])  ? "admin/" : "";
	$pathOffset =  isset($_GET['r'])  ? "" : "../";
	
	if (!function_exists('json_encode')){
		require_once("../../../includes/php/json.php");
		function json_encode($a=false){
			if($a!=false){
				$json = new Services_JSON();
				return $json->encode($a);
			}
		}
	}
	
	ini_set("display_errors",1);
	error_reporting(E_ALL);
	$offset="../../../";
	//require_once($offset."includes/php/json.php");
	$path=isset($_GET['path']) ? $_GET['path'] : "directory/";
	$images_dir=$offset.$path;
	
	//$valid_exts = array(".gif", ".jpg", ".jpeg", ".png",".txt",".zip");
	if($_REQUEST['extension']!='*')
	{
		$str='.'.str_replace(',',',.',$_REQUEST['extension']);
		$valid_exts=split(',',$str);	
	}
	
	
	$list = array();
	
	$dir = opendir($images_dir);
	while ($file_name = readdir($dir))
	{
		if (is_file($images_dir.$file_name))
		{
			if (($_REQUEST['extension']=='*') || (in_array(strtolower(strrchr($file_name, ".")), $valid_exts)) )
			{
				
				if(strstr($file_name,"thumb_")==""){
					//list($width, $height) = getimagesize($images_dir.$file_name);
					list($width, $height, $type, $attr) = getimagesize($images_dir.$file_name);
					
					$aExt=split('\.',$file_name);
					$strExt=strtolower($aExt[count($aExt)-1]);
					$bImage=false;
					if(($strExt=='gif')||($strExt=='jpg')||($strExt=='png'))$bImage=true;
					 
					 if($bImage==true)
					 {
						$size = filesize($images_dir.$file_name);
						$list[] = array(
							"name" => utf8_encode($file_name), 							
							"width" => $width,
							"height" => $height, 
							"size" => $size, 
							"type" => "file", 
							"picture" => true, 
							"url" => utf8_encode($pathOffset.$path.$file_name),
							"icon" => utf8_encode($pathOffset.$path.$file_name)
						);
					}
					else
					{
						$size = filesize($images_dir.$file_name);	
											
						$a=split('/',$_SERVER['SCRIPT_FILENAME']);
						array_pop($a);
						array_pop($a);
						$strExt=$file_name;
						$aE=split('\.',$strExt);
						$strExt=$aE[count($aE)-1];
						$strPath=join('/',$a).'/htmleditorimage/extensions/';
						$file=$strPath.strtolower($strExt).'.gif';
						if(is_file($file))$strUrl=$pathFront.'os/htmleditorimage/extensions/'.strtolower($strExt).'.gif';				
						else $strUrl=$pathFront.'os/htmleditorimage/extensions/unknown.gif';
						
						$filepath = utf8_encode($pathOffset.$path.$file_name);
						$filepath = str_replace("directory/","",$filepath);
						$list[] = array(
							"name" => utf8_encode($file_name), 							
							"width" => 60,
							"height" => 60, 
							"size" => $size, 
							"type" => "file", 
							"picture" => false, 
							"url" => $filepath,
							"icon" => $strUrl
						);
					}
				 }
			}
		}else if($file_name!="." && $file_name!=".."){
			$list[] = array(
				"name" => utf8_encode($file_name), 
				"width" => false,
				"height" => false, 
				"size" => false, 
				"type" => "directory", 
				"url" => $pathFront."os/resources/shared/dossier.png",
				"icon" => $pathFront."os/resources/shared/dossier.png"
			);
		}
	}
  closedir($dir);
  

  

  //$json = new Services_JSON();
  echo json_encode(array("images" => $list));
?>
