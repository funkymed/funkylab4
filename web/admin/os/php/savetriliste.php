<?php
	session_start();
	require_once("../../../includes/php/bddconf.php");
	if($_POST['liste']=="Aucune"){
		unset($_SESSION[sessionName]['emails']['liste']);
	}else{
		$_SESSION[sessionName]['emails']['liste']=$_POST['liste'];
	}
	
	print "1";
?>
