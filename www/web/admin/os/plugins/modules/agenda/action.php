<?php
	require_once("../../../php/const.php");
	
	$items=array(
		'query'=>array(
			'agenda_titre','agenda_online',
			'agenda_type','agenda_thumb','agenda_chapeau','agenda_texte',
			'agenda_file','agenda_linkext','agenda_linkint',
			'id_contact_fk','agenda_tags'
		),
		'typeSearch'=>array(
			'str','bool',
			'str','str','str','str',
			'str','str','str',
			'str','str'
		)
	);
	
	function getDateFormarted($v){
		if(isset($_POST[$v])){
			$_date = $_POST[$v];
			if(isset($_POST[$v.'H']) && trim($_POST[$v.'H'])!="" && strlen($_POST[$v.'H'])==2){
				$_date.=" ".$_POST[$v.'H'];
			}else{
				$_date.=" 00";
			}
			
			if(isset($_POST[$v.'M']) && trim($_POST[$v.'M'])!="" && strlen($_POST[$v.'M'])==2){
				$_date.=":".$_POST[$v.'M'].":00";
			}else{
				$_date .=":00:00";
			}
			
			
		}else{
			$_date = '0000-00-00 00:00:00';
		}
		
		return $_date;
	}
	
	$userid = $_SESSION[sessionName]['user']['id'];
	
	
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
				
				$allQuery[]="agenda_debut='".getDateFormarted('agenda_debut')."'";
				$allQuery[]="agenda_fin='".getDateFormarted('agenda_fin')."'";
				
				$allQuery[]="edit_date=now()";
				$allQuery[]="edit_user_fk=".$_SESSION[sessionName]['user']['id'];
				
				$query=("UPDATE agenda SET ".implode(", ",$allQuery)." WHERE id_agenda=".$_POST['id_agenda']);		
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
				
				
				$allField[]="agenda_debut";
				$allQuery[]="'".getDateFormarted('agenda_debut')."'";
				
				$allField[]="agenda_fin";
				$allQuery[]="'".getDateFormarted('agenda_fin')."'";
				
				$allField[]="edit_user_fk";
				$allQuery[]=$_SESSION[sessionName]['user']['id'];
				
				$query=("INSERT INTO agenda (".implode(", ",$allField).") VALUES (".implode(", ",$allQuery).")");	
				if(mysql_query($query)){
					$success="1";
				}else{
					echo mysql_error();
					$success="0";
				}
				break;
			
			case "remove":
				mysql_query("DELETE FROM agenda WHERE id_agenda=".$_POST['id']);
				break;
				
			// STATE
			case "setpreview":	
				mysql_query("UPDATE agenda SET agenda_state='preview',edit_date=now(),edit_user_fk='".$userid."' WHERE id_agenda=".$_POST['id']);
				break;
			case "setpublished":	
				mysql_query("UPDATE agenda SET agenda_state='published',edit_date=now(),edit_user_fk='".$userid."' WHERE id_agenda=".$_POST['id']);
				break;
			case "setarchived":	
				mysql_query("UPDATE agenda SET agenda_state='archived',edit_date=now(),edit_user_fk='".$userid."' WHERE id_agenda=".$_POST['id']);
				break;
				
			// ONLINE
			case "setonline":	
				mysql_query("UPDATE agenda SET agenda_online='1',edit_date=now(),edit_user_fk='".$userid."' WHERE id_agenda=".$_POST['id']);
				break;		
			case "setoffline":	
				mysql_query("UPDATE agenda SET agenda_online='0',edit_date=now(),edit_user_fk='".$userid."' WHERE id_agenda=".$_POST['id']);
				break;	
				
			case "duplicate":
				$res = mysql_query('SELECT * FROM agenda WHERE id_agenda='.$_POST['id']);
				$row = mysql_fetch_object($res);
				$allField=array();
				$allQuery=array();
				foreach($row as $key=>$value){
					if($key!='id_agenda' && $key!='edit_date' & $key!='edit_creation' && $key!='edit_user_fk'){
						$allField[]=$key;
						if($key=='agenda_titre'){
							$value="*".$value;
						}
						$allQuery[]="'".addSlashesCheckMagic($value)."'";
					}
				}
				$allField[]="edit_date";
				$allQuery[]="now()";
				$allField[]="edit_creation";
				$allQuery[]="now()";
				$allField[]="edit_user_fk";
				$allQuery[]=$_SESSION[sessionName]['user']['id'];
				$query=("INSERT INTO agenda (".implode(", ",$allField).") VALUES (".implode(", ",$allQuery).")");	
				if(mysql_query($query)){
					$success="1";
				}else{
					echo mysql_error();
					$success="0";
				}
				break;	
		}
	}  

	print "[{success:".$success.",count:".$count."}]";

?>