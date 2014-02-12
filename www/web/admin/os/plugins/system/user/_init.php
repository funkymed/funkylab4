<?php
	require_once("../../../php/bddconf.php");

	$strPath = '../../../../htpassword';
	$strFile = $strPath.'/htpassword';
	if(is_dir($strPath))
	{
		$q=sprintf("SELECT * FROM %s;",'cms_users');
		$r = mysql_query($q);
		$aFile =  array();
		while($ro = mysql_fetch_object($r))
		{
			$pass = ENCODAGE_PASSWORD=="basic" ? crypt($ro->pass) : $ro->pass;
			$aFile[] = $ro->login.':'.$pass;
		}
		if(is_file($strFile))unlink($strFile);
		file_put_contents($strFile, join("\n",$aFile));
	}
		
?>