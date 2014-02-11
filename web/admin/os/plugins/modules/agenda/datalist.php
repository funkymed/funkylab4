<?php
	require_once("../../../php/const.php");
	
	$where = getFilter('agenda');
	if(isset($_SESSION['order']['agenda']))
		unset($_SESSION['order']['agenda']);
		
	if(isset($_POST['sort']) && isset($_POST['dir']))
		$_SESSION['order']['agenda']="ORDER BY ".$_POST['sort']." ".$_POST['dir'];
	
		
 	$countsqlquery="SELECT count(*) FROM agenda ".$where;

	$countsqlres=mysql_query($countsqlquery);
	print mysql_error();
	$countrow = mysql_fetch_array($countsqlres);
	
	$arrayObj=array(
		'meta'=>array(
			"code"=>1,
			"exception"=>array(),
			"success"=>true,
			"message"=>null
		),
		"data"=>array(
			"total"=>$countrow['count(*)'],
			"results"=>array()
		)
	);
	
	$contentItem = "agenda.*,cms_users.nom as adm_nom,cms_users.prenom as adm_prenom";
	
	$contentItem .= ",DATE_FORMAT(edit_creation,'%d/%m/%Y %H:%i:%s') as edit_creation";
	$contentItem .= ",DATE_FORMAT(edit_date,'%d/%m/%Y %H:%i:%s') as edit_date";
	$contentItem .= ",DATE_FORMAT(agenda_debut,'%d/%m/%Y %H:%i') as agenda_debut";
	$contentItem .= ",DATE_FORMAT(agenda_fin,'%d/%m/%Y %H:%i') as agenda_fin";
	
	
	$sqlquery="SELECT ".$contentItem." FROM agenda ";
	$sqlquery.="LEFT JOIN cms_users ON (cms_users.id = agenda.edit_user_fk) ";
	$sqlquery.=$where." ORDER BY ".$_POST['sort']." ".$_POST['dir']." LIMIT ".$_POST['start'].",".($_POST['limit']);
	
	$sqlres=mysql_query($sqlquery);
	print mysql_error();
	while($row = mysql_fetch_object($sqlres)){
		$row->useredit = $row->adm_nom." ".$row->adm_prenom;
		$arrayObj['data']['results'][]=$row;
	}
	
	
	print json_encode($arrayObj);

?>

