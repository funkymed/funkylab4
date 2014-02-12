<?php
	ini_set("display_errors",1);
	error_reporting(E_ALL);
	$offset="../../../";
	
	$pathFront =  isset($_GET['r'])  ? "admin/" : "";
	$pathOffset =  isset($_GET['r'])  ? "" : "../";
	
	if(strstr($_POST['file'],"directory/")){
		$_POST['file'] = isset($_POST['file']) ? $_REQUEST['file'] : false;
	}else{
		$_POST['file'] = isset($_POST['file']) ? "directory/".$_REQUEST['file'] : false;	
	}
	
	
	if(!isset($_POST['file']) || (trim($_POST['file'])=="") || !file_exists($offset.$_POST['file'])){
				
		echo "{success: 'false'}";
		
	}else{
		
		
		//$ Tester le fichier
		list($width, $height, $type, $attr) = getimagesize($offset.$_POST['file']);
		$size = filesize($offset.$_POST['file']);
		$aExt=split('\.',$_POST['file']);
		$strExt=strtolower($aExt[count($aExt)-1]);
		$bImage=false;
		if(($strExt=='gif')||($strExt=='jpg')||($strExt=='png'))$bImage=true;
		 
		 if($bImage==true)
		{
/*			$size = filesize($offset.$_POST['file']);
			$list[] = array(
				"name" => utf8_encode($_POST['file']), 							
				"width" => $width,
				"height" => $height, 
				"size" => $size, 
			);*/
			$strUrl=$pathOffset.$_POST['file'];
			$strLink=$pathOffset.$_POST['file'];
			$rh=$height;
			$rw=$width;
			$bPicture=1;
			
			if($rw > $rh)
			{
				if($rw < 80)
				{
					$w = $rw;
					$h = $rh;
				}
				else
				{
					$w = 60;
					$h = 60 / $rw * $rh;
				}
			}
			else
			{
				if($rh < 60) 
				{
					$w = $rw;
					$h = $rh;
				}
				else
				{
					$w = 60 / $rh * $rw;
					$h = 60;
				}
			}
		}
		else
		{
			$a=split('/',$_SERVER['SCRIPT_FILENAME']);
			array_pop($a);
			array_pop($a);
			$strExt=$_POST['file'];
			$aE=split('\.',$strExt);
			$strExt=$aE[count($aE)-1];
			$strPath=join('/',$a).'/htmleditorimage/extensions/';
			$file=$strPath.strtolower($strExt).'.gif';
			if(is_file($file))$strUrl=$pathFront.'os/htmleditorimage/extensions/'.strtolower($strExt).'.gif';				
			else $strUrl=$pathFront.'os/htmleditorimage/extensions/unknown.gif';
			$rw=0;
			$rh=0;
			$h=60;
			$w=60;
			$bPicture=0;
			$strLink=$strUrl;
			
			
		}
		
		echo "{success: 'true',url:'".$strUrl."',link:'".$strLink."', w: ".$w.", h: ".$h.",size:".$size.",rh:".$rh.",rw:".$rw.",picture:".$bPicture."}";
	}
    exit(0);
	
	
	
  // images directory
  //$images_dir = $offset."image/";
  $images_dir = isset($_POST['path']) ? $offset.$_POST['path'] : $offset."data/";
 
  // maximum file size (in bytes)
  // $max_size = 1073741824; // 100K
  $max_size = 6442450944; // 600K
  
  // valid extensions
  //$valid_exts = array(".gif", ".jpg", ".jpeg", ".png",".txt",".zip");
  $valid_exts=array();
if(!isset($_POST['extension']))$_POST['extension']='*';
if($_POST['extension']!="*")
{
	$aTmp=split('\|',$_POST['extension']);
	for($i=0;$i<count($aTmp);$i++)
	{
		array_push($valid_exts,'.'.$aTmp[$i]);
	}
}
	
  // return response
  function jsonResponse($success, $message)  {
    echo "{success: '$success', message: '$message'}";
    exit(0);
  }
  // verify images directory exists
  if (!is_dir("$images_dir")) {
 	jsonResponse("false", "DirNotExist###$images_dir");
	//jsonResponse("false", "$images_dir does not exist");
  }
  // verify images directory is writable
  if (!is_writeable("$images_dir")) {
  	jsonResponse("false", "DirWriteError###$images_dir");
	//jsonResponse("false", "Unable to write to $images_dir");
  }
  // verify file uploaded
  if (count($_FILES) == 0) {
    jsonResponse("false", "NoFileUpload");
	//jsonResponse("false", "No files were uploaded");
  }
  // get uploaded file record
  $file = $_FILES["image"];
  // get temporary filename used to store uploaded file
  $file_tmp = $file["tmp_name"];
  // verify valid upload file or result of hacking
  if (!is_uploaded_file($file_tmp)) {
    jsonResponse("false", "InvalidUploadFile");
	//jsonResponse("false", "Invalid upload file");
  }
  // get original filename
  $file_name = $file['name'];
  // verify file does not already exist
  if (file_exists($images_dir.$file_name)) {
    jsonResponse("false", "FileExist###$file_name");
//	jsonResponse("false", "$file_name already exists");
  }
  // verify file extension is valid
  $file_ext = strtolower(strrchr($file_name, "."));
  if (!in_array($file_ext, $valid_exts)&&($_POST['extension']!="*")) {
    jsonResponse("false", "InvalidFile###".join(',',$valid_exts));
	//jsonResponse("false", "Invalid file");
  }
  // get size of uploaded file
  $file_size = $file['size'];
  if ($file_size > $max_size) {
    jsonResponse("false", "ImageIsTooLarge");
	//jsonResponse("false", "Image is too large");
  }
  // move temporary upload file to images directory
  if (!move_uploaded_file($file_tmp, $images_dir.$file_name)) {
    // report reason why file did not move
    switch ($filearray["error"]) {
      case UPLOAD_ERR_INI_SIZE:
       // $error = "The uploaded file exceeded the upload_max_filesize directive (".ini_get("upload_max_filesize").") in php.ini.";
		$error='UPLOAD_ERR_INI_SIZE###'.ini_get("upload_max_filesize");
        break;
      case UPLOAD_ERR_FORM_SIZE:
       // $error = "The uploaded file exceeded the MAX_FILE_SIZE directive specified in the HTML form";
		$error='UPLOAD_ERR_FORM_SIZE';
        break;
      case UPLOAD_ERR_PARTIAL:
        //$error = "The uploaded file was only partially uploaded";
		$error = 'UPLOAD_ERR_PARTIAL';
        break;
      case UPLOAD_ERR_NO_FILE:
        //$error = "No file was uploaded";
		$error = 'UPLOAD_ERR_NO_FILE';
        break;
      case UPLOAD_ERR_NO_TMP_DIR:
        //$error = "Missing a temporary folder";
		$error = 'UPLOAD_ERR_NO_TMP_DIR';
        break;
      case UPLOAD_ERR_CANT_WRITE:
        //$error = "Failed to write file to disk";
		$error = 'UPLOAD_ERR_CANT_WRITE';
        break;
      default:
        //$error = $filearray["error"] - "Unknown File Error";
		$error = 'UPLOAD_OTHER_ERROR###'.$filearray["error"];
		break;
    }
    jsonResponse("false", $error);
  }
  // report success
  jsonResponse("true", "UPLOAD_SUCCESS###$file_name");
?>
