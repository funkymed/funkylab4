<?php
	require_once("bddconf.php");
	session_start();
	unset($_SESSION[sessionName]['user']);
	header("location:".SITEURL."index.html");
?>