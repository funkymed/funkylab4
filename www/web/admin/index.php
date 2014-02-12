<?php
	include "os/php/bddconf.php";
	
	
	session_start();
	
  	if(!isset($_SESSION[sessionName]['htpwd'])){
    	//if(isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])){
    	/*
    	 if(isset($_SERVER['HTTP_AUTHORIZATION']))
			{
			  list($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) = explode(':' , base64_decode(substr($_SERVER['HTTP_AUTHORIZATION'], 6)));
			}
    	*/
	    if(isset($_SERVER['REMOTE_USER'])){
	    	
      		$_SESSION[sessionName]['htpwd'] = 1;
			$query = sprintf("SELECT * FROM cms_users WHERE login='%s'",
				mysql_real_escape_string($_SERVER['REMOTE_USER'])
			);	
			
			$sqlres=mysql_query($query);
			
			if($row = mysql_fetch_array($sqlres)){
				$_SESSION[sessionName]['user'] = $row;
				$resLangue = mysql_query("SELECT pays_class,pays_langue,pays_libelle FROM cms_pays WHERE pays_libelle='".$_SESSION[sessionName]['user']['langue']."'");
				$rowLangue = mysql_fetch_array($resLangue);
				$_SESSION[sessionName]['user']['classlangue']=$rowLangue['pays_class'];
				$_SESSION[sessionName]['user']['languecms']=$rowLangue['pays_libelle'];
				mysql_query("UPDATE cms_users SET dateconnexion=now() WHERE id=".$_SESSION[sessionName]['user']['id']);
				//header("location:index.php");
	      	}
    	}
  	}	
	
	
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
	<head>
		<title>!!!</title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<link rel="icon" href="favicon.ico" type="image/x-icon" />

		<style type="text/css">
			html, body {
				background:#e9e9e9 url(os/resources/wallpapers/flower.png)  center center;
			    font: normal 12px tahoma, arial, verdana, sans-serif;
				margin: 0;
				padding: 0;
				border: 0 none;
				overflow: hidden;
				height: 100%;
			}
			#loading-mask{
		        position:absolute;
		        left:0;
		        top:0;
		        width:100%;
		        height:100%;
		        z-index:20000;
		        background:white;
		    }
		    #loading{
		        position:absolute;
		        left:47%;
		        top:45%;
		        padding:2px;
		        z-index:20001;
		        height:auto;
		    }
		    #loading img{
			    padding-left:10px;
		    }
		    #loading a {
		        color:#225588;
		    }
		    #loading .loading-indic{
		        color:white;
		        font:bold 13px tahoma,arial,helvetica;
		        margin:0;
		        height:auto;
		        text-align:center;
		    }
		    #loading-msg {
			    color:black;
			    clear:both;
		        font: normal 10px arial,tahoma,sans-serif;
		    }
		</style>
	</head>
	<body>
		
		<!-- LOADING -->
		<div id="loading-mask"></div>
		<div id="loading">
			<img src="os/resources/images/logo.png" alt="logo" />
		    <div class="loading-indic">
		    	<span id="loading-msg">Chargement...</span>
		    </div>
		</div>
	    <?php 
			if(isset($_SESSION[sessionName]['user'])){ 
		?>
		<script type="text/javascript" src="os/js/init.js"></script>
	    <script type="text/javascript" src="os/js/startos.php"></script>
	    <!-- HTML -->
		<div id="x-desktop">
			<img src="os/resources/images/logo.png" style="margin: 5px; float: right;"/>
			<dl id="x-shortcuts"></dl>
		</div>
		<div id="ux-taskbar">
			<div id="ux-taskbar-start"></div>
			<div id="ux-taskbuttons-panel"></div>
			<div class="x-clear"></div>
		</div>
		<?php  }else{ ?>
			<script type="text/javascript" src="os/js/init-log.js"></script>
			<script type="text/javascript" src="os/js/connexion.js"></script>
		<?php  } ?>
	</body>
</html>