<?php
	require_once("../../../php/const.php");
	
	function deleteRecursif($id){
		$res = mysql_query("SELECT id_page FROM site_page WHERE parent_id=".$id);
		while($row=mysql_fetch_object($res)){
			deleteRecursif($row->id_page);
		}
		mysql_query("DELETE FROM site_page WHERE id_page='".$id."'");
		
	}
	
	function copyrecursif($id,$newparent=null){
		$res = mysql_query("SELECT * FROM site_page WHERE id_page=".$id);
		
		if($row = mysql_fetch_object($res)){
			$allField	= array();
			$allQuery	= array();
			foreach($row as $key=>$value){
				if($key!="id_page" && $key!="parent_id" && $key!="titre"){
					$allField[]=$key;		$allQuery[]="'".$value."'";
				}
			}
			if($newparent==null){
				$allField[]="parent_id";		$allQuery[]="'".$row->parent_id."'";
				$allField[]="titre";			$allQuery[]="'*".$row->titre."'";
			}else{
				$allField[]="parent_id";		$allQuery[]="'".$newparent."'";
				$allField[]="titre";			$allQuery[]="'".$row->titre."'";
			}
			
			mysql_query("INSERT INTO site_page (".implode(", ",$allField).") VALUES (".implode(", ",$allQuery).")");
			print mysql_error();	
			$newparent=mysql_insert_id();
			
			$res = mysql_query("SELECT id_page FROM site_page WHERE parent_id=".$row->id_page);
			
			while($row=mysql_fetch_object($res)){
				copyrecursif($row->id_page,$newparent);
			}
		}
	}
		
	
	if(isset($_POST['action'])){
		switch($_POST['action']){
			case "savenodeorder":
				$parent	= json_decode(stripslashes($_POST['nodeparent']));
				$node 	= json_decode(stripslashes($_POST['node']));
				
				$idvalue	= explode("-",$node->id);
				$idvalue	= $idvalue[1];
				
				$idparent	= explode("-",$parent->id);
				$idparent	= ($idparent[0]=="page") ? $idparent[1] : "0";
				
				$allQuery[]	= "parent_id=".$idparent;
				
				$allQuery[]="edit_date=now()";
				$allQuery[]="edit_user_fk=".$_SESSION[sessionName]['user']['id'];
				$query=("UPDATE site_page SET ".implode(", ",$allQuery)." WHERE id_page=".$idvalue);		
				if(mysql_query($query)){
					for($x=0;$x<count($_POST['neworder']);$x++){
						$query=("UPDATE site_page SET ordre=".($x+1)." WHERE id_page=".$_POST['neworder'][$x]);		
						mysql_query($query);
					}
					print "1";
				} else{
					print $query;
					print mysql_error();
				}
				
				break;
				
			case "addnode":
				$strTable	= "";
				$allField	= array();
				$allQuery	= array();
				
				$idparent	= explode("-",$_POST['id']);
				$idparent	= $idparent[1];
				
				$allField[]="titre";		$allQuery[]="'new item'";
				$allField[]="parent_id";	$allQuery[]=$idparent;
					
				$allField[]="edit_date";
				$allQuery[]="now()";
				$allField[]="edit_creation";
				$allQuery[]="now()";
				$allField[]="edit_user_fk";
				
				$allQuery[]=$_SESSION[sessionName]['user']['id'];
				
				if(mysql_query("INSERT INTO site_page (".implode(", ",$allField).") VALUES (".implode(", ",$allQuery).")")){
					print "page-".mysql_insert_id();
				}
				break;
			
			case "deletenode":
				deleteRecursif($_POST['id_page']);
				print 1;
				break;
				
			case "rename":
				$allQuery[]="titre='".$_REQUEST['newname']."'";
				$allQuery[]="edit_date=now()";
				$allQuery[]="edit_creation=now()";
				$allQuery[]="edit_user_fk=".$_SESSION[sessionName]['user']['id'];
				
				$id = explode("-",$_REQUEST['id']);
				
				$query=("UPDATE site_page SET ".implode(", ",$allQuery)." WHERE id_page=".$id[1]);		
				if(mysql_query($query)){
					print 1;
				}else{
					print $query."\n\n";
					print mysql_error();
					print 0;
				}
				break;
			case "copynode":
				copyrecursif($_POST['id_page']);
				break;
				
			case "saveexpand":
				$node	= json_decode(stripslashes($_POST['node']));
				
				$query="UPDATE site_page SET open=".$_POST['expanded']." WHERE id_page=".$node->id_page;		
				if(mysql_query($query)){
					print 1;
				}else{
					print $query."\n\n";
					print mysql_error();
					print 0;
				}
				break;
			case "online":
				$query="UPDATE site_page SET online=".$_POST['online']." WHERE id_page=".$_POST['id'];		
				if(mysql_query($query)){
					print 1;
				}else{
					print $query."\n\n";
					print mysql_error();
					print 0;
				}
				break;
			case "brother":
				$query="UPDATE site_page SET brother_display=".$_POST['brother']." WHERE id_page=".$_POST['id'];		
				if(mysql_query($query)){
					print 1;
				}else{
					print $query."\n\n";
					print mysql_error();
					print 0;
				}
				break;
			case "sidebar":
				$query="UPDATE site_page SET sidebar_display=".$_POST['sidebar']." WHERE id_page=".$_POST['id'];		
				if(mysql_query($query)){
					print 1;
				}else{
					print $query."\n\n";
					print mysql_error();
					print 0;
				}
				break;
				
			case "childview":
				$query="UPDATE site_page SET child_display=".$_POST['childview']." WHERE id_page=".$_POST['id'];		
				if(mysql_query($query)){
					print 1;
				}else{
					print $query."\n\n";
					print mysql_error();
					print 0;
				}
				break;		
				
				
			case "actualites":
				$query="UPDATE site_page SET actualites=".$_POST['actualites']." WHERE id_page=".$_POST['id'];		
				if(mysql_query($query)){
					print 1;
				}else{
					print $query."\n\n";
					print mysql_error();
					print 0;
				}
				break;			
				
			case "menufooter":
				$query="UPDATE site_page SET menu_footer=".$_POST['menufooter']." WHERE id_page=".$_POST['id'];		
				if(mysql_query($query)){
					print 1;
				}else{
					print $query."\n\n";
					print mysql_error();
					print 0;
				}
				break;					
					
			case "infoview":
				$query="UPDATE site_page SET info_display=".$_POST['infoview']." WHERE id_page=".$_POST['id'];		
				if(mysql_query($query)){
					print 1;
				}else{
					print $query."\n\n";
					print mysql_error();
					print 0;
				}
				break;					
			case "lienview":
				$query="UPDATE site_page SET lien_display=".$_POST['lienview']." WHERE id_page=".$_POST['id'];		
				if(mysql_query($query)){
					print 1;
				}else{
					print $query."\n\n";
					print mysql_error();
					print 0;
				}
				break;	
			case "demarcheview":
				$query="UPDATE site_page SET demarches_display=".$_POST['demarcheview']." WHERE id_page=".$_POST['id'];		
				if(mysql_query($query)){
					print 1;
				}else{
					print $query."\n\n";
					print mysql_error();
					print 0;
				}
				break;			
			case "agendaview":
				$query="UPDATE site_page SET agenda_display=".$_POST['agendaview']." WHERE id_page=".$_POST['id'];		
				if(mysql_query($query)){
					print 1;
				}else{
					print $query."\n\n";
					print mysql_error();
					print 0;
				}
				break;
						
				
			case "format":
				$query="UPDATE site_page SET format='".$_POST['format']."',mise_en_page='' WHERE id_page=".$_POST['id'];		
				if(mysql_query($query)){
					print 1;
				}else{
					print $query."\n\n";
					print mysql_error();
					print 0;
				}
				break;
			case "config":
				$query="UPDATE site_page SET mise_en_page='".$_POST['config']."' WHERE id_page=".$_POST['id'];		
				if(mysql_query($query)){
					print 1;
				}else{
					print $query."\n\n";
					print mysql_error();
					print 0;
				}
				break;
		}
	}
?>