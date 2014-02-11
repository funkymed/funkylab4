<?php
	require_once("../../../php/const.php");
	$items=array(
		'query'=>array(
			'nom','prenom','login','pass','langue','admin','id_arbo_fk'
		),
		'typeSearch'=>array(
			'like','like','like','like','like','like','like'
		)
	);
	$count=0;
	
	
	function RewriteHtPassword()
	{
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
	}	
	
	if(isset($_POST['action'])){
		switch($_POST['action']){
			// update
			case "update":
				$allQuery=array();
				for($xx=0;$xx<count($items['query']);$xx++){
					if(isset($_POST[$items['query'][$xx]])){
						if($items['typeSearch'][$xx]=='bool'){
							$queryValue=$_POST[$items['query'][$xx]]=='true' ? '1' : '0';
						}else{
							$queryValue="'".addSlashesCheckMagic($_POST[$items['query'][$xx]])."'";
						}
						$allQuery[]=($items['query'][$xx]."=".$queryValue);
					}else{
						if($items['typeSearch'][$xx]=='bool'){
							$allQuery[]=($items['query'][$xx]."=0");
						}else{
							$allQuery[]=($items['query'][$xx]."=''");
						}
						
					}
				}
				$allQuery[]="datemodif=now()";
				$allQuery[]="edit_user_fk=".$_SESSION[sessionName]['user']['id'];
				$query=("UPDATE cms_users SET ".implode(", ",$allQuery)." WHERE id=".$_POST['id']);	
				if(mysql_query($query)){
// 					echo "ok";
				}else{
// 					echo mysql_error();
// 					echo "pas ok";
				}
				RewriteHtPassword();
				break;
			// add
			case "add":
				$allField=array();
				$allQuery=array();
				for($xx=0;$xx<count($items['query']);$xx++){
					if(isset($_POST[$items['query'][$xx]]) && trim($_POST[$items['query'][$xx]])!=""){
						if($items['typeSearch'][$xx]=='bool'){
							$queryValue=$_POST[$items['query'][$xx]]=='true' ? 1 : 0;
						}else{
							$queryValue="'".$_POST[$items['query'][$xx]]."'";
						}
						$allField[]=$items['query'][$xx];
						$allQuery[]=$queryValue;
					}else{
						if($items['typeSearch'][$xx]=='bool'){
							$allField[]=$items['query'][$xx];
							$allQuery[]="0";
						}else{
							$allField[]=$items['query'][$xx];
							$allQuery[]="''";
						}
					}
				}
				$allField[]="dateconnexion";
				$allQuery[]="'0000-00-00 00:00:00'";
				$allField[]="datecreation";
				$allQuery[]="now()";
				$allField[]="datemodif";
				$allQuery[]="now()";
				$allField[]="edit_user_fk";
				$allQuery[]=$_SESSION[sessionName]['user']['id'];
				$query=("INSERT INTO cms_users (".implode(", ",$allField).") VALUES (".implode(", ",$allQuery).")");	
				if(mysql_query($query)){
// 					echo "ok";
				}else{
// 					echo mysql_error();
// 					echo "pas ok";
				}
				RewriteHtPassword();
				break;
			case "remove":
				$query="DELETE FROM cms_users WHERE id=".$_POST['id'];	
				if(mysql_query($query)){
// 					echo "ok";
				}else{
// 					echo mysql_error();
// 					echo "pas ok";
				}
				RewriteHtPassword();
				break;
		}
	}
	
	print "[{sucess:true,count:".$count."}]";

?>