<?php

	class ext2php{
		
		var $id=false;
		var $langue=false;
		
		function ext2php($langue=false,$info=false,$id=false){
			$this->info				= ($info!=false) ? $info : array();
			$this->id				= $id;
			$this->langue			= $langue;
			$this->allfielditems	= array();
			$this->allBeforeSaved	= array();
			$this->allInitItems		= array();
		}
		
		function getFieldTyped($id,$value){
			if (function_exists("Ext_".$value['type'])) 				return call_user_func("Ext_".$value['type'], $id,$value);
		}
		function getSaveTyped($id,$value){
			if (function_exists("Ext_".$value['type']."_save")) 		return call_user_func("Ext_".$value['type']."_save", $id);
		}
		function getInitTyped($id,$value){
			if (function_exists("Ext_".$value['type']."_init"))  		return call_user_func("Ext_".$value['type']."_init", $id,$value);
		}
		function getBeforeSaveTyped($id,$value){
			if (function_exists("Ext_".$value['type']."_beforesave")) 	return call_user_func("Ext_".$value['type']."_beforesave", $id,$value);
		}
		function generateExtForm(){
			
			ini_set("display_errors",1);
			error_reporting(E_ALL);
			
			$count			= 1;
			$buffer			= "allPanel=new Array();\n";
			$this->allfielditems	= array();
			$this->allInitItems		= array();
			$this->allBeforeSaved	= array();
			
			foreach($this->info['items'] as $key=>$value){
				$buffer.="var pan".$key." = new Array();\n";
				$allitems=array();
				foreach($value as $_key=>$_value){
					if($_key=="onglet"){
						// avec sous-sous onglet
						foreach($_value as $__key=>$__value){
							foreach($__value as $___key=>$___value){
 								if($___key=="items"){
	 								$allfields=array();
	 								foreach($___value as $____key=>$____value){
		 								if(!isset($____value['form']) || (isset($____value['form']) && $____value['form']==true)){
					 		 				$id 					= $key.$_key.$__key.$___key.$____key;
							 				$this->allfielditems[]	= $this->getSaveTyped			($id,$____value);
							 				$this->allBeforeSaved[]	= $this->getBeforeSaveTyped		($id,$____value);
							 				$this->allInitItems[]	= $this->getInitTyped			($id,$____value);
							 				$allfields[]			= $this->getFieldTyped			($id,$____value);
						 				}
	 								}
	 								$allitems[]="new Ext.Panel({title: '".$__value['title']."',frame:true,height:355,autoScroll:true,labelWidth: 130,layout:'form',items:[\n\t".implode(",\n\t",$allfields)."]})";							 								
	 							}
							}
						}
					}else if($_key=="items"){
 		 				// avec sous onglet
						foreach($_value as $__key=>$__value){
							if(!isset($__value['form']) || (isset($__value['form']) && $__value['form']==true)){
		 		 				$id 				= $key.$_key.$__key;
				 				$this->allfielditems[]	= $this->getSaveTyped			($id,$__value);
				 				$this->allBeforeSaved[]	= $this->getBeforeSaveTyped		($id,$__value);
				 				$this->allInitItems[]	= $this->getInitTyped			($id,$__value);
				 				$extObject				= $this->getFieldTyped			($id,$__value);
				 				
				 				$buffer.= "pan".$key.".push(".$extObject.");\n";
			 				}
		 				}
					}
				}
				
				if(count($allitems)>0) $buffer.="pan".$key.".push(new Ext.TabPanel({autoScroll:true,enableTabScroll:true,xtype:'tabpanel',activeTab: 0,deferredRender:true,border:false,items: [\n\t".implode(",\n",$allitems)."]}));\n";
				
				$buffer.="allPanel.push(Ext.chewingCom.addPanel('".$value['title']."',pan".$key.",'panel".$count."'));\n\n\n";
	
				
				$count++;
 		
			}
			
			return $buffer;
		}
		
		function cleanValue($str){
			$str = str_replace('"','\"',$str);
			$str = str_replace("\n","\\n",$str);
			$str = str_replace("\r","\\r",$str);
			return $str;
		}
		
		
		
		function getExtJsonGridColumn(){
			$allColumn=array();
			foreach($this->info['items'] as $key=>$value){
				foreach($value as $_key=>$_value){
					if($_key=="onglet"){
						foreach($_value as $__key=>$__value){
							foreach($__value as $___key=>$___value){
								if($___key=="items"){
	 								$allfields=array();
	 								foreach($___value as $____key=>$____value){
		 								if(!isset($____value['grid']) || (isset($____value['grid']) && $____value['grid']==true)){
			 								$config=array();
			 								$config[]="dataIndex:'".$____value['field']."'";
			 								$config[]="header:'".$____value['label']."'";
			 								if(isset($____value['order']) && $____value['order']==true)
			 									$config[]="sortable:".$____value['order'];
			 								
			 								$allColumn[]="{".implode(",",$config)."}";
		 								}
									}
								}
							}
						}
					}else if($_key=="items"){
						foreach($_value as $__key=>$__value){
							if(!isset($__value['grid']) || (isset($__value['grid']) && $__value['grid']==true)){
								$config=array();
 								$config[]="dataIndex:'".$__value['field']."'";
 								$config[]="header:'".$__value['label']."'";
 								if(isset($__value['order']) && $__value['order']==true)
 									$config[]="sortable:".$__value['order'];
 								
 								$allColumn[]="{".implode(",",$config)."}";
							}
						}
					}
				}
			}
			
			$config=array();
 			$config[]="dataIndex:'edit_user'";
 			$config[]="header:'Edit by'";
 			$config[]="sortable:1";
 			$allColumn[]="{".implode(",",$config)."}";
			
			return "[".implode(",\n",$allColumn)."]";
		}
		function getExtJsonGridRecord(){
			$allRecord=array();
			foreach($this->info['items'] as $key=>$value){
				foreach($value as $_key=>$_value){
					if($_key=="onglet"){
						foreach($_value as $__key=>$__value){
							foreach($__value as $___key=>$___value){
								if($___key=="items"){
	 								$allfields=array();
	 								foreach($___value as $____key=>$____value){
		 								if(!isset($____value['grid']) || (isset($____value['grid']) && $____value['grid']==true)){
			 								$allRecord[]="{name:'".$____value['field']."'}";
		 								}
									}
								}
							}
						}
					}else if($_key=="items"){
						foreach($_value as $__key=>$__value){
							if(!isset($__value['grid']) || (isset($__value['grid']) && $__value['grid']==true)){
								$allRecord[]="{name:'".$__value['field']."'}";
							}
						}
					}
				}
			}
			
			
			$allRecord[]="{name:'edit_user'}";
			return "[".implode(",\n",$allRecord)."]";
		}
		
		function getExtJsonGridData(){
			$countsqlquery="SELECT count(".$this->info['orderby'].") FROM ".$this->info['table']." ORDER BY ".$_POST['sort']." ".$_POST['dir'];

			$countsqlres=mysql_query($countsqlquery);
			$countrow = mysql_fetch_array($countsqlres);
			$arrayObj=array(
				'meta'=>array(
					"code"=>1,
					"exception"=>array(),
					"success"=>true,
					"message"=>null
				),
				"data"=>array(
					"total"=>$countrow["count(".$this->info['orderby'].")"],
					"results"=>array()
				)
			);
			
			$allField=array();
			foreach($this->info['items'] as $key=>$value){
				foreach($value as $_key=>$_value){
					if($_key=="onglet"){
						foreach($_value as $__key=>$__value){
							foreach($__value as $___key=>$___value){
								if($___key=="items"){
	 								$allfields=array();
	 								foreach($___value as $____key=>$____value){
		 								if(!isset($____value['grid']) || (isset($____value['grid']) && $____value['grid']==true)){
			 								$allField[]=$____value['field'];
		 								}
									}
								}
							}
						}
					}else if($_key=="items"){
						foreach($_value as $__key=>$__value){
							if(!isset($__value['grid']) || (isset($__value['grid']) && $__value['grid']==true)){
								$allField[]=$__value['field'];
							}
						}
					}
				}
			}
			
			$contentItem  = $this->info['table'].".*,cms_users.nom,cms_users.prenom";
			$contentItem .= ",DATE_FORMAT(edit_date,'%d/%m/%Y %H:%i:%s') as edit_date";
			$contentItem .= ",DATE_FORMAT(edit_creation,'%d/%m/%Y %H:%i:%s') as edit_creation";
	
			$sqlquery="SELECT ".$contentItem." FROM ".$this->info['table']." LEFT JOIN cms_users ON (".$this->info['table'].".edit_user=cms_users.id)";
			$sqlquery.=" ORDER BY ".$_POST['sort']." ".$_POST['dir']." LIMIT ".$_POST['start'].",".($_POST['limit']);
			
			$sqlres=mysql_query($sqlquery);
			//print mysql_error();
			$count=0;
			while($row = mysql_fetch_array($sqlres)){
				$arrayObj['data']['results'][$count]=array(
					'edit_creation'=>$row['edit_creation'],
					'edit_date'=>$row['edit_date'],
					'edit_user'=>(strtoupper($row['nom'])." ".$row['prenom'])
				);
				foreach($allField as $value){
					$arrayObj['data']['results'][$count][$value]=$row[$value];
				}
				
				$count++;
			}
			$json = new Services_JSON();
			return $json->encode($arrayObj);
		}
		
		function generateExtSaveVar(){
			if($this->id!=false){
				$this->allfielditems[]="id:".$this->id;
				$this->allfielditems[]="action:'update'";
			}else{
				$this->allfielditems[]="action:'add'";
			}
			
			$this->allfielditems[]="langue:'".$this->langue."'";
			return implode(",\n",$this->allfielditems);
		}
		
		function generateExtBeforeSave(){ return implode("\n",$this->allBeforeSaved); }
		function generateExtInit(){ return implode("\n",$this->allInitItems); }
		
		function savebdd($info = NULL){
			$allValue=array();			
			foreach($this->info['items'] as $key=>$value){
				foreach($value as $_key=>$_value){
					if($_key=="onglet"){
						foreach($_value as $__key=>$__value){
							foreach($__value as $___key=>$___value){
								if($___key=="items"){
	 								$allfields=array();
	 								foreach($___value as $____key=>$____value){
		 								if(!isset($____value['form']) || (isset($____value['form']) && $____value['form']==true)){
			 								$id 				= $key.$_key.$__key.$___key.$____key;
			 								if(isset($_POST[$id])){
				 								if($____value['type']=='checkbox'){
													$queryValue=$_POST[$id]=='true' ? 1 : 0;
												}else{
													$var = $_POST[$id];
													$var = stripslashes($_POST[$id]);
													/*
													$var = str_replace("\n","\\n",$var);
													$var = str_replace("\r","\\r",$var);
													$var = str_replace('"','\"',$var);
													*/
													$queryValue=$var;
												}
												$this->info['items'][$key][$_key][$__key][$___key][$____key]['value']=$queryValue;
											}
		 								}
									}
								}
							}
						}
					}else if($_key=="items"){
						foreach($_value as $__key=>$__value){
							if(!isset($__value['form']) || (isset($__value['form']) && $__value['form']==true)){
								$id 				= $key.$_key.$__key;
								if(isset($_POST[$id])){
									if($__value['type']=='checkbox'){
										$queryValue=$_POST[$id]=='true' ? 1 : 0;
									}else{
										$var = $_POST[$id];
										$var = stripslashes($_POST[$id]);
										/*
										$var = str_replace("\n","\\n",$var);
										$var = str_replace("\r","\\r",$var);
										$var = str_replace('"','\"',$var);
										*/
										$queryValue=$var;
									}
									$this->info['items'][$key][$_key][$__key]['value']=$queryValue;
								}
							}
						}
					}
				}
			}
			$query = "SELECT traduction_id FROM ".$this->info['table']." WHERE pays_libelle_fk='".$_POST['langue']."' AND ".$this->info['orderby']."='".$_POST['id']."'";
			$resss = mysql_query($query );
			
			if($rowww = mysql_fetch_array($resss)){
				$id=$rowww['traduction_id'];
				$action="update";
			}else{
				$action="add";
			}
			
			$serialize=base64_encode(serialize($this->info));
			
			switch($action){
				case "add":
					$allField[]="traduction_obj";
					$allQuery[]="'".$serialize."'";
					$allField[]="edit_creation";
					$allQuery[]="now()";
					$allField[]="edit_date";
					$allQuery[]="now()";
					$allField[]="edit_user_fk";
					$allQuery[]=$_SESSION[sessionName]['user']['id'];
					$allField[]="pays_libelle_fk";
					$allQuery[]="'".$_POST['langue']."'";
					$allField[]=$this->info['orderby'];
					$allQuery[]="'".$_POST['id']."'";
	 				$query="INSERT INTO ".$this->info['table']." (".implode(", ",$allField).") VALUES (".implode(", ",$allQuery).")";	
	 				if(mysql_query($query)){
						$success=1;
					}else{
						//print mysql_error();
						$success=0;
					}
					break;
				case "update":
					$allQuery=array();
					$allQuery[]="traduction_obj='".$serialize."'";
					$allQuery[]="edit_date=now()";
					$allQuery[]="edit_user_fk=".$_SESSION[sessionName]['user']['id'];
	 				$query="UPDATE ".$this->info['table']." SET ".implode(", ",$allQuery)." WHERE traduction_id=".$id;
	 				if(mysql_query($query)){
						$success=1;
					}else{
						//print mysql_error();
						$success=0;
					}
					break;
			}
			
			return $success;
			
		}
	}
	
?>