<?php
	require_once("cropimage.php");
	
	$_POST['image'] = "../../../".$_POST['image'];
	
	$cropimage = new cropimage();
	$cropimage->setInfo($_POST);
	$cropimage->createthumb();
	$file = $cropimage->save();
	
	$outputfile = str_replace("tmp_","",$file);
	rename($file,$outputfile);
	
	print str_replace("../","",$outputfile);

?>