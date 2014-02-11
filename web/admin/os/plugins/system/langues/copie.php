<?php
	require_once("../../../php/const.php");
	require_once("../../../php/fonctions.php");
	
	function makeQuery($row,$table,$dest_langue=false,$debug=false){
		global $not_included;
		if($dest_langue==false)
			global $dest_langue;
		$fields=array();
		$query=array();
		
		foreach($row as $key=>$value){
			if($table['id']!=$key && !in_array($key,$not_included)){
				$fields[]="`".$key."`";
				
				if($key==$table['langue']){
					$query[]="'".$dest_langue."'";
				}else if(is_int($value)){
					$query[]=$value;
				}else if(gettype($value)=='NULL' && $value==''){
					$query[]='0';
				}else{
					$value = stripslashes($value);
					$value = stripslashes($value);
					$query[]="'".str_replace("'","\'",$value)."'";
				}
			}
		}
		$fields[]="`edit_creation`";
		$query[]='now()';
		$fields[]="`edit_date`";
		$query[]='now()';
		$fields[]="`edit_user_fk`";
		$query[]='1';
		$query = 'INSERT INTO '.$table['name'].' ('.implode(",",$fields).') VALUES ('.implode(",",$query).')';
		if($debug!=false)
			print $query."\n";
		
		return $query;
	}
	
	//-------------------------------------------
	// Copie des traductions
	//-------------------------------------------
	
	$tables = array(
		array(
			'name'=>'cms_traductions',
			'id'=>'traduction_id',
			'langue'=>'pays_libelle_fk'
		),array(
			'name'=>'cms_actualites',
			'id'=>'id_actualites',
			'langue'=>'pays_libelle_fk'
		)
	);
	
	$not_included = array(
		'edit_date','edit_user_fk','edit_creation'
	);
	
	$source_langue=$_POST['source'];
	$dest_langue=$_POST['destination'];;
	
	foreach($tables as $table){
		
		mysql_query("DELETE FROM ".$table['name']." WHERE ".$table['langue']."='".$dest_langue."'");
		
		$res  = mysql_query("SELECT * FROM ".$table['name']." WHERE ".$table['langue']."='".$source_langue."'");
		while($row = mysql_fetch_object($res)){
			$insertQuery =	makeQuery($row,$table);
			if(!mysql_query($insertQuery)){
				print mysql_error();
				print "<hr/>";
			}
		}
	}
	
	//-------------------------------------------
	// Copie des contenus
	//-------------------------------------------
	
	// Remove All contents DEST LANGUE
	$Rres = mysql_query("SELECT id_rubrique FROM cms_rubriques WHERE pays_libelle_fk='".$dest_langue."'");
	while($Rrow = mysql_fetch_object($Rres)){
		$Tres = mysql_query("SELECT id_template FROM cms_templates WHERE rubrique_id_fk='".$Rrow->id_rubrique."'");
		while($Trow = mysql_fetch_object($Tres)){
			$Pres = mysql_query("SELECT id_photo FROM cms_photo WHERE id_template_fk='".$Trow->id_template."'");
			while($Prow = mysql_fetch_object($Pres)){
				mysql_query("DELETE FROM cms_photo WHERE id_photo='".$Prow->id_photo."'");	
			}
			if(!mysql_query("DELETE FROM cms_templates WHERE id_template='".$Trow->id_template."'")){
				print mysql_error();
			}
		}
	}
	
	mysql_query("DELETE FROM cms_rubriques WHERE pays_libelle_fk='".$dest_langue."'");
	
	$not_included[]='id_rubrique';
	$not_included[]='id_template';
	$not_included[]='id_photo';
	
	// Recuperation des données et insertion
	$res  = mysql_query("SELECT * FROM cms_rubriques WHERE pays_libelle_fk='".$source_langue."' ORDER BY id_rubrique ASC");
	while($row = mysql_fetch_object($res)){
		$insertQuery=makeQuery($row,array('name'=>'cms_rubriques','id'=>'id_rubrique','langue'=>'pays_libelle_fk'));
		if(!mysql_query($insertQuery)){
			print mysql_error();
			print "<hr/>";
		}else{
			
			$id_rubrique = mysql_insert_id();
			
			$Tres = mysql_query("SELECT * FROM cms_templates WHERE rubrique_id_fk='".$row->id_rubrique."' ORDER BY id_template ASC");
			print mysql_error();
			while($Trow = mysql_fetch_object($Tres)){
				$insertQuery=makeQuery($Trow,array('name'=>'cms_templates','id'=>'id_template','langue'=>'rubrique_id_fk'),$id_rubrique,true);
				if(!mysql_query($insertQuery)){
					print mysql_error();
					print "<hr/>";
				}else{
					$id_template = mysql_insert_id();
					
					$Pres = mysql_query("SELECT * FROM cms_photo WHERE id_template_fk='".$Trow->id_template."' ORDER BY id_photo ASC");
					print mysql_error();
					while($Prow = mysql_fetch_object($Pres)){
						$insertQuery=makeQuery($Prow,array('name'=>'cms_photo','id'=>'id_photo','langue'=>'id_template_fk'),$id_template,true);
						if(!mysql_query($insertQuery)){
							print mysql_error();
						}
					}
				}
			}
		}
	}
	
	

?>	