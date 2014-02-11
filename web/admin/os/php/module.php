<?php
	require_once("bddconf.php");
	session_start();
	
	function addSlashesCheckMagic($data){
		if (!get_magic_quotes_gpc()){
			$data = addslashes($data);
		}
		$data = str_replace("\n","",$data);
		$data = str_replace("\r","",$data);
		return $data;
	}
	$sql = "UPDATE ".$_POST['table']." SET ".$_POST['field']." ='".addSlashesCheckMagic($_POST['_new'])."' WHERE id_page=".$_POST['id'];
	
	$res = mysql_query($sql);
	print 1;
	

?>