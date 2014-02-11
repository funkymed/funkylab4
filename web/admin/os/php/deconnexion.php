<?php
	require_once("bddconf.php");	
	session_start();
	unset($_SESSION[sessionName]['user']);
	print 1;
?>