<?php
	
	ini_set("display_errors",1);
	error_reporting(E_ALL);
	$offset="../../../";
	
	require_once($offset."includes/php/bddconf.php");
	require_once($offset."includes/php/json.php");
	
	session_start();
	if(!isset($_SESSION[sessionName]))
		exit();
	
	$path=isset($_GET['path']) ? "/".$_GET['path'] : '';
	$path = $offset."data".$path;
	
	$paths = array();
	$d = dir($path);
	$countfile=0;
	while($f = $d->read()){
		if($f == '.' || $f == '..' || substr($f, 0, 1) == '.')continue;
		if(is_dir($path.'/'.$f)){
			$paths[] = array(
				'text'=>utf8_encode($f),
				'disabled'=>false,
				'leaf'=>true,
				'fileurl'=>utf8_encode('images/'.$f)
			);
			$countfile++;
		}
	}
	
	$d->close();
	$json = new Services_JSON();
	
	if(isset($_GET['callback']))
		print $_GET['callback']."({totalCount:".$countfile.",topics:".$json->encode($paths)."});";

?>
