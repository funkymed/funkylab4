<?php
	require_once("../../../php/const.php");
	
	$field = $_REQUEST['field'];
	$fieldArray = array();
	if(trim($_REQUEST['query'])==""){
		$sqlquery="SELECT ".$field." as field FROM contacts GROUP BY ".$field." ORDER BY ".$field;	
	}else{
		$sqlquery="SELECT ".$field." as field FROM contacts WHERE ".$field." like '".$query."%' GROUP BY ".$field." ORDER BY ".$field;
	}
	
	
	$sqlres=mysql_query($sqlquery);
	
	while($row = mysql_fetch_object($sqlres)){
		$fieldArray[]=array("value"=>$row->field,"text"=>$row->field);
	}
	
	print $_GET['callback']."({totalCount:".count($fieldArray).",topics:".json_encode($fieldArray)."});";

?>

