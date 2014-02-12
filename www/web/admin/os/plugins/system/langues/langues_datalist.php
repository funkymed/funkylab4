<?php
	require_once("../../../php/const.php");
	
 	$countsqlquery="SELECT count(pays_libelle) FROM cms_pays ORDER BY ".$_POST['sort']." ".$_POST['dir'];
	$countsqlres=mysql_query($countsqlquery);
	$countrow = mysql_fetch_array($countsqlres);
	
	$arrayObj=array(
		'meta'=>array(
			"code"=>1,
			"exception"=>array(),
			"success"=>true,
			"message"=>null
		),
		"data"=>array(
			"total"=>$countrow['count(pays_libelle)'],
			"results"=>array()
		)
	);
	
	$contentItem = "typo,pays_libelle,pays_langue,pays_name,pays_class,cms_users.nom,cms_users.prenom";
	$contentItem .= ",DATE_FORMAT(edit_date,'%d/%m/%Y %h:%i:%s') as edit_date";
	$contentItem .= ",DATE_FORMAT(edit_creation,'%d/%m/%Y %h:%i:%s') as edit_creation";
	
	$sqlquery="SELECT ".$contentItem." FROM cms_pays LEFT JOIN cms_users ON (cms_pays.edit_user_fk=cms_users.id) ORDER BY ".$_POST['sort']." ".$_POST['dir']." LIMIT ".($_POST['start']).",".($_POST['limit']);

	$sqlres=mysql_query($sqlquery);
	print mysql_error();
	while($row = mysql_fetch_object($sqlres)){
		
		$row->edit_user_fk=(strtoupper($row->nom)." ".$row->prenom);
		$row->typo=UCFirst($row->typo);
		
		$arrayObj['data']['results'][]=$row;
	}
	
	
	print json_encode($arrayObj);

?>
