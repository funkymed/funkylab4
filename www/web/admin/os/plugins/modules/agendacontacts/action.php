<?php
	require_once("../../../php/const.php");
	
	$items=array(
		'query'=>array(
			'contact_titre','contact_nom','contact_prenom',
			'contact_adresse','contact_cp','contact_ville',
			'contact_tel','contact_email','contact_url',
			'contact_lat','contact_long',
			'contact_fax','contact_infopratique','contact_quartier'
		),
		'typeSearch'=>array(
			'str','str','str',
			'str','str','str',
			'str','str','str',
			'str','str',
			'str','str','str'
		)
	);
	
	
	print_r($_REQUEST);
	$count=0;
	$success="0";
	
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
						}
					}
				}
				
				
				$allQuery[]="edit_date=now()";
				$allQuery[]="edit_user_fk=".$_SESSION[sessionName]['user']['id'];
				
				print  $query=("UPDATE contacts SET ".implode(", ",$allQuery)." WHERE id_contact=".$_POST['id_contact']);		
				if(mysql_query($query)){
					$success="1";
				}else{
					echo mysql_error();
					$success="0";
				}
				break;
				
			// add
			case "add":
				$allField=array();
				$allQuery=array();
				for($xx=0;$xx<count($items['query']);$xx++){
					if(isset($_POST[$items['query'][$xx]])){
						if($items['typeSearch'][$xx]=='bool'){
							$queryValue=$_POST[$items['query'][$xx]]=='on' ? '1' : '0';
						}else{
							$queryValue="'".addSlashesCheckMagic($_POST[$items['query'][$xx]])."'";
						}
						$allField[]=$items['query'][$xx];
						$allQuery[]=$queryValue;
					}
				}
				$allField[]="edit_date";
				$allQuery[]="now()";
				$allField[]="edit_creation";
				$allQuery[]="now()";
				$allField[]="edit_user_fk";
				$allQuery[]=$_SESSION[sessionName]['user']['id'];
				
				print $query=("INSERT INTO contacts (".implode(", ",$allField).") VALUES (".implode(", ",$allQuery).")");	
				if(mysql_query($query)){
					$success="1";
				}else{
					echo mysql_error();
					$success="0";
				}
				break;
			
			case "remove":
				$ids=explode(",",$_POST['id']);
				foreach($ids as $id){
					$query="DELETE FROM contacts WHERE id_contact=".$id;	
					if(mysql_query($query)){
						$success="1";
					}else{
						echo mysql_error();
						$success="0";
					}
				}
				
				break;
		}
	}  

	print "[{success:".$success.",count:".$count."}]";

?>