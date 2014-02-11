<?php
	require_once("../../../php/const.php");
	
	$strWhere="";
	if($_SESSION[sessionName]['user']['admin']!='sadmin')
	{
		$strWhere=" WHERE( admin!='sadmin') ";
	}
	
 	$countsqlquery="SELECT count(id) FROM cms_users ".$strWhere." ORDER BY ".$_POST['sort']." ".$_POST['dir'];

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
			"total"=>$countrow['count(id)'],
			"results"=>array()
		)
	);
	
	$contentItem = "id,login,nom,prenom,langue,admin";
	$contentItem .= ",DATE_FORMAT(datecreation,'%d/%m/%Y %h:%i:%s') as datecreation";
	$contentItem .= ",DATE_FORMAT(datemodif,'%d/%m/%Y %h:%i:%s') as datemodif";
	$contentItem .= ",DATE_FORMAT(dateconnexion,'%d/%m/%Y %h:%i:%s') as dateconnexion";	
	
	$sqlquery="SELECT ".$contentItem." FROM cms_users ".$strWhere." ORDER BY ".$_POST['sort']." ".$_POST['dir']." LIMIT ".($_POST['start']).",".($_POST['limit']);
	
	$sqlres=mysql_query($sqlquery);
	while($row = mysql_fetch_object($sqlres)){
		
		$resLangue = mysql_query("SELECT pays_class,pays_name FROM cms_pays WHERE pays_libelle='".$row->langue."'");
		$rowLangue = mysql_fetch_object($resLangue);
		$row->langue = isset($rowLangue->pays_class) ? $rowLangue->pays_class : '';
		$row->pays_name = isset($rowLangue->pays_name) ? $rowLangue->pays_name : '';
		$arrayObj['data']['results'][]=$row;
	}
	
	
	print json_encode($arrayObj);

?>
