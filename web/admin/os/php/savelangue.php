<?php
	session_start();
	require_once("../../../includes/php/bddconf.php");	
	$_SESSION[sessionName]['user'][$_POST['module']]['langue']=$_POST['langue'];
	print "1";
?>
