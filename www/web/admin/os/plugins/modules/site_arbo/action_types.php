<?php
	require_once("../../../php/const.php");
	
	$items=array(
		'query'=>array(
			'typeprincipal','typesecondaire'
		),
		'typeSearch'=>array(
			'str','str'
		)
	);


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
				
				$query=("UPDATE site_page SET ".implode(", ",$allQuery)." WHERE id_page=".$_POST['id_page']);		
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