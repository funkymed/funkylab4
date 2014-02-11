<?php
	require_once("../../../php/const.php");
	
	$where = getFilter('contacts');
	if(isset($_SESSION['order']['contacts']))
		unset($_SESSION['order']['contacts']);
		
	if(isset($_POST['sort']) && isset($_POST['dir']))
		$_SESSION['order']['contacts']="ORDER BY ".$_POST['sort']." ".$_POST['dir'];
	
		
 	$countsqlquery="SELECT count(1) as nb FROM contacts ".$where;

	$countsqlres=mysql_query($countsqlquery);
	print mysql_error();
	$countrow = mysql_fetch_object($countsqlres);
	
	$arrayObj=array(
		'meta'=>array(
			"code"=>1,
			"exception"=>array(),
			"success"=>true,
			"message"=>null
		),
		"data"=>array(
			"total"=>$countrow->nb,
			"results"=>array()
		)
	);
	
	$contentItem = "contacts.*,cms_users.nom as adm_nom,cms_users.prenom as adm_prenom";
	
	$contentItem .= ",DATE_FORMAT(edit_creation,'%d/%m/%Y %H:%i:%s') as edit_creation";
	$contentItem .= ",DATE_FORMAT(edit_date,'%d/%m/%Y %H:%i:%s') as edit_date";
	
	$sqlquery="SELECT ".$contentItem." FROM contacts ";
	$sqlquery.="LEFT JOIN cms_users ON (cms_users.id = contacts.edit_user_fk) ";
	$sqlquery.=$where." ORDER BY ".$_POST['sort']." ".$_POST['dir']." LIMIT ".$_POST['start'].",".($_POST['limit']);
	
	$sqlres=mysql_query($sqlquery);
	print mysql_error();
	while($row = mysql_fetch_object($sqlres)){
		$row->useredit = $row->adm_nom." ".$row->adm_prenom;
		$arrayObj['data']['results'][]=$row;
	}
	
	
	print json_encode($arrayObj);

?>

