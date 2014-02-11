<?php
	
	$strServeur = strtolower($_SERVER['HTTP_HOST']);
	
	switch($strServeur){
		case "localhost":
			ini_set("display_errors",1);
			error_reporting(E_ALL);
// 			$server 	= "localhost";
// 			$login 		= "root";
// 			$mdp 		= "";
// 			$ddb 		= "cotestade";
// 			$siteName	= "";
// 			$uploadDir	= "data/";
// 			$siteDir	= $_SERVER['DOCUMENT_ROOT'].$siteName;
// 			$siteUrl	= 'http://'.$_SERVER['SERVER_NAME'].'/'.$siteName;
// 			$encodage 	= "none";
// 			$sessionpath= 'C:\wamp\www\www.ville-saint-denis.eu\private\application\tmp';
			break;
		default:
			ini_set("display_errors",0);
			$server 	= "localhost:/tmp/mysql5.sock";
			$login 		= "dbo315666270";
			$mdp 		= "r121w4g54t";
			$ddb 		= "db315666270";
			$siteName	= "";
			$uploadDir	= "data/";
			$siteDir	= $_SERVER['DOCUMENT_ROOT'].$siteName;
			$siteUrl	= 'http://'.$_SERVER['SERVER_NAME'].'/'.$siteName;
			$encodage 	= "basic";
			$sessionpath= '/homepages/15/d301349868/htdocs/f4.funkylab.net/private/application/tmp';
			
			break;
	}
	
	define ("SESSION_PATH",$sessionpath);
	define ("ENCODAGE_PASSWORD",$encodage);
	define ("SITEURL",$siteUrl);
	define ("DB_SERVER",$server);
	define ("DB_LOGIN", $login);
	define ("DB_PASSWORD",$mdp);
	define ("DATABASE",$ddb);
	
	define ("ext_site","/");
	define ("sessionName","cotestade");
	
	mysql_connect(DB_SERVER,DB_LOGIN,DB_PASSWORD);
	mysql_select_db(DATABASE);
	
	function Mod_addslashes ( $string ){
		if (get_magic_quotes_gpc()==1){
			return ( $string );
		}else{
			return ( addslashes ( $string ) );
		}
	}
	ini_set('session.save_path',SESSION_PATH); 
	
	if(!isset($_COOKIE[sessionName]))
		header("location:../");
		
	session_id($_COOKIE[sessionName]);
?>