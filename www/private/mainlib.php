<?php

	switch ( $_SERVER["SERVER_NAME"] ) {
		
		case "localhost":
		case "cotestade.local":
	
			$paramsBDD = array(
			    'host'     => 'localhost',
			    'username' => 'root',
			    'password' => '',
			    'dbname'   => 'cote_stade',
			    'profiler' => false 
			);
			
			break;
			
		default:
		
			$paramsBDD = array(
			    'host'     => 'localhost',
			    'unix_socket'=>'/tmp/mysql5.sock',
			    'username' => 'dbo315666270',
			    'password' => 'r121w4g54t',
			    'dbname'   => 'db315666270',
			    'profiler' => false
			);
			
			break;
			
		}
		

?>