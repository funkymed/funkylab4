<?php
	require_once("../../../php/const.php");
	
	if(!isset($_REQUEST['query']) || !isset($_REQUEST['field']))
		exit();
	
	$field = $_REQUEST['field'];
	$fieldArray = array();
	
	$query = $_REQUEST['query'];
	
	if(trim($query)==""){
		$sqlquery="SELECT ".$field." as field FROM site_page GROUP BY ".$field." ORDER BY ".$field;	
	}else{
		$sqlquery="SELECT ".$field." as field FROM site_page WHERE ".$field." like '%".$query."%' GROUP BY ".$field." ORDER BY ".$field;
	}
	
	
	$sqlres=mysql_query($sqlquery);
	
	while($row = mysql_fetch_object($sqlres)){
		$fieldArray[]=array("value"=>$row->field,"text"=>$row->field);
	}
	
	print $_GET['callback']."({totalCount:".count($fieldArray).",topics:".json_encode($fieldArray)."});";

?>

