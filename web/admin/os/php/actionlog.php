<?php
	
	require_once("bddconf.php");
	session_start();
	
	$query = sprintf("SELECT * FROM cms_users WHERE login='%s' AND  pass='%s'",
		mysql_real_escape_string($_POST['login']),
		mysql_real_escape_string($_POST['mdp'])
	);	
	
	$sqlres=mysql_query($query);
	$row = mysql_fetch_array($sqlres);
	
	if($row){
		$_SESSION[sessionName]['user'] = $row;
		
		$resLangue = mysql_query("SELECT pays_class,pays_langue,pays_libelle FROM cms_pays WHERE pays_libelle='".$_SESSION[sessionName]['user']['langue']."'");
		$rowLangue = mysql_fetch_array($resLangue);
		$_SESSION[sessionName]['user']['classlangue']=$rowLangue['pays_class'];
		$_SESSION[sessionName]['user']['languecms']=$rowLangue['pays_libelle'];
		mysql_query("UPDATE cms_users SET dateconnexion=now() WHERE id=".$_SESSION[sessionName]['user']['id']);
		print 1;exit();
	}else{
		print 0;exit();
	}
	
?>