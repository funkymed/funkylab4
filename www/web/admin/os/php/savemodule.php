<?php
	session_start();
	require_once("../../../includes/php/bddconf.php");	
	$_SESSION[sessionName]['user']['modele']=$_POST['modele'];
	print "1";
?>
