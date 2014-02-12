<?php
	require_once("../../../php/const.php");
	$items=array(
		'query'=>array(
			'pays_libelle','pays_langue','pays_name',
			'file_video1','file_video2',
			'modele1_online','modele2_online',
			'typo','colors','pays_domaine','pays_directory'
			
		),
		'typeSearch'=>array(
			'like','like','like',
			'like','like',
			'bool','bool',
			'like','like','like','like'
		)
	);
	$count=0;
	
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
				$allQuery[]="edit_date=now()";
				$allQuery[]="edit_user_fk=".$_SESSION[sessionName]['user']['id'];
				$class = explode("_",strtolower($_POST['pays_libelle']));
				$allQuery[]="pays_class='ux-flag-".$class[0]."'";
				print $query=("UPDATE cms_pays SET ".implode(", ",$allQuery)." WHERE pays_libelle='".$_POST['id']."'");		
				if(mysql_query($query)){
 					echo "ok";
				}else{
					echo mysql_error();
					echo "pas ok";
				}
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
				$allField[]="edit_user_fk";
				$allQuery[]=$_SESSION[sessionName]['user']['id'];
				$allField[]="edit_date";
				$allQuery[]="now()";
				$allField[]="edit_creation";
				$allQuery[]="now()";
				
				$allField[]="pays_class";
				$class = explode("_",strtolower($_POST['pays_libelle']));
				$allQuery[]="'ux-flag-".$class[0]."'";
				$query=("INSERT INTO cms_pays (".implode(", ",$allField).") VALUES (".implode(", ",$allQuery).")");	
				if(mysql_query($query)){
					echo "ok";
				}else{
					echo mysql_error();
					echo "pas ok";
				}
				break;
			case "remove":
				$query="DELETE FROM cms_pays WHERE pays_libelle='".$_POST['pays_libelle']."'";	
				if(mysql_query($query)){
// 					echo "ok";
				}else{
// 					echo mysql_error();
// 					echo "pas ok";
				}
				
				break;
		}
	}
	
	print "[{sucess:true,count:".$count."}]";

?>