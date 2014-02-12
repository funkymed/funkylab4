<?php
	
	ini_set("display_errors",1);
	error_reporting(E_ALL);
	$offset="../../../";
	require_once($offset."includes/php/bddconf.php");
	require_once($offset."includes/php/json.php");
	
	session_start();
	if(!isset($_SESSION[sessionName]))
		exit();
		
		
		
	$allTypeFile = array(
		"flv"=>array("flv"),
		"image"=>array("jpg","png","gif"),
		"swf"=>array("swf"),
		"csv"=>array("csv"),
		"pdf"=>array("pdf")
	);
	
	
	function formatBytes($val, $digits = 3, $mode = "SI", $bB = "B"){
	   $si = array("", "K", "M", "G", "T", "P", "E", "Z", "Y");
	   $iec = array("", "Ki", "Mi", "Gi", "Ti", "Pi", "Ei", "Zi", "Yi");
	   switch(strtoupper($mode)) {
	       case "SI" : $factor = 1000; $symbols = $si; break;
	       case "IEC" : $factor = 1024; $symbols = $iec; break;
	       default : $factor = 1000; $symbols = $si; break;
	   }
	   switch($bB) {
	       case "b" : $val *= 8; break;
	       default : $bB = "B"; break;
	   }
	   for($i=0;$i<count($symbols)-1 && $val>=$factor;$i++)
	       $val /= $factor;
	   $p = strpos($val, ".");
	   if($p !== false && $p > $digits) $val = round($val);
	   elseif($p !== false) $val = round($val, $digits-$p);
	   return round($val, $digits) . " " . $symbols[$i] . $bB;
	}
	
	$path=isset($_GET['path']) ? "/".$_GET['path'] : '';
	$path = $offset."data".$path;
	
	$paths = array();
	$d = dir($path);
	
	
	
	$countfile=0;
	while($f = $d->read()){
		if($f == '.' || $f == '..' || substr($f, 0, 1) == '.')continue;
		if(!is_dir($path.'/'.$f)){
			$size = formatBytes(filesize($path.'/'.$f), 2);
			
			$infofile=pathinfo($path.'/'.$f);
			$ext = $infofile['extension'];
			
			if(!isset($_GET['type']) || (isset($_GET['type']) && in_array($ext,$allTypeFile[$_GET['type']]))){
		
				$paths[] = array(
 					'text'=>utf8_encode($f), 
					'iconCls'=>'file-'.$ext,
					'disabled'=>false,
					'leaf'=>true,
					'qtip'=>$size,
					'fileurl'=>utf8_encode($f)
				);
				$countfile++;
			}
		}
	}
	
	$d->close();
	$json = new Services_JSON();
	
	if(isset($_GET['callback']))
		print $_GET['callback']."({totalCount:".$countfile.",topics:".$json->encode($paths)."});";

?>
