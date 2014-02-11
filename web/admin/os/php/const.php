<?php
	require_once("../../../php/bddconf.php");
	require_once("../../../php/ext2php.php");
	require_once("../../../php/fonctions.php");
	session_start();
	
	if(!isset($_SESSION[sessionName])){
		print 'window.location.reload( false );';
		exit();
	}

	
	function addSlashesCheckMagic($data){
		if (!get_magic_quotes_gpc()){
			$data = addslashes($data);
		}
		return $data;
	}

	function LoadObjectWithBdd($info,$_info)
	{
		$hData=array();
		$hTmp=$_info['items'];
		
		foreach($_info['items'] AS $k1 => $h1)
		{
			foreach($h1 AS $k2 => $a2)
			{
				if($k2=='items')
				{
					for($i=0;$i<count($a2);$i++){$hData[$a2[$i]['field']]=$a2[$i]['value'];	}
				}
				if($k2=='onglet')
				{
					foreach($a2 AS $k3 => $h3)
					{
						$a3=$h3['items'];
						for($i=0;$i<count($a3);$i++){$hData[$a3[$i]['field']]=$a3[$i]['value'];}
					}
				}
			}
		}
		
		foreach($info['items'] AS $k1 => $h1)
		{
			foreach($h1 AS $k2 => $a2)
			{
				if($k2=='items')
				{
					for($i=0;$i<count($a2);$i++)
					{
						if(isset($hData[$a2[$i]['field']])){$info['items'][$k1][$k2][$i]['value']=$hData[$a2[$i]['field']];}
					}
				}
				if($k2=='onglet')
				{
					foreach($a2 AS $k3 => $h3)
					{
						$a3=$h3['items'];
						for($i=0;$i<count($a3);$i++)
						{
							if(isset($hData[$a3[$i]['field']])){$info['items'][$k1][$k2][$k3]['items'][$i]['value']=$hData[$a3[$i]['field']];}
						}
					}
				}
			}
		}
		
		return $info;
	}

	function getFilter($filtername){
		$where = " WHERE 0 = 0 ";
		if(isset($_POST['filter'])){
			$filter=$_POST['filter'];
			if (is_array($filter)) {
				$qs="";
				for ($i=0;$i<count($filter);$i++){
					switch($filter[$i]['data']['type']){
						case 'string' : $qs .= " AND ".$filter[$i]['field']." LIKE '%".$filter[$i]['data']['value']."%'"; Break;
						case 'list' :
							if (strstr($filter[$i]['data']['value'],',')){
								$fi = explode(',',$filter[$i]['data']['value']);
								for ($q=0;$q<count($fi);$q++){
									$fi[$q] = "'".$fi[$q]."'";
								}
								$filter[$i]['data']['value'] = implode(',',$fi);
								$qs .= " AND ".$filter[$i]['field']." IN (".$filter[$i]['data']['value'].")";
							}else{
								$qs .= " AND ".$filter[$i]['field']." = '".$filter[$i]['data']['value']."'";
							}
						Break;
						case 'boolean' : $qs .= " AND ".$filter[$i]['field']." = ".($filter[$i]['data']['value']); Break;
						case 'numeric' :
							switch ($filter[$i]['data']['comparison']) {
								case 'ne' : $qs .= " AND ".$filter[$i]['field']." != ".$filter[$i]['data']['value']; Break;
								case 'eq' : $qs .= " AND ".$filter[$i]['field']." = ".$filter[$i]['data']['value']; Break;
								case 'lt' : $qs .= " AND ".$filter[$i]['field']." < ".$filter[$i]['data']['value']; Break;
								case 'gt' : $qs .= " AND ".$filter[$i]['field']." > ".$filter[$i]['data']['value']; Break;
							}
						Break;
						case 'date' :
							switch ($filter[$i]['data']['comparison']) {
								case 'ne' : $qs .= " AND ".$filter[$i]['field']." != '".date('Y-m-d',strtotime($filter[$i]['data']['value']))."'"; Break;
								case 'eq' : $qs .= " AND ".$filter[$i]['field']." = '".date('Y-m-d',strtotime($filter[$i]['data']['value']))."'"; Break;
								case 'lt' : $qs .= " AND ".$filter[$i]['field']." < '".date('Y-m-d',strtotime($filter[$i]['data']['value']))."'"; Break;
								case 'gt' : $qs .= " AND ".$filter[$i]['field']." > '".date('Y-m-d',strtotime($filter[$i]['data']['value']))."'"; Break;
							}
						Break;
					}
				}
				$where .= $qs;
				$_SESSION['filter'][$filtername]=$where;
				return $where;
			}else{
				if(isset($_SESSION['filter'][$filtername]))
					unset($_SESSION['filter'][$filtername]);
			}
		}else{
			if(isset($_SESSION['filter'][$filtername]))
				unset($_SESSION['filter'][$filtername]);
		}
	}
	
	//fields config
	function listedir($rep){
		$buffer=array();
		$dir = opendir($rep);
		while ($f = readdir($dir)) {
			 if(!is_dir($rep.$f) && $f!="." && $f!="..") {
				 
				$buffer[]=$f;
			 }
	  	}
		closedir($dir);
		return($buffer);
	}
	
	foreach(listedir("../../../php/fields/") as $value){
		require_once("../../../php/fields/".$value);
	}

	$langue = (isset($_GET['langue'])) ? $_GET['langue'] :  $_SESSION[sessionName]['user']['languecms'];
	
	$pathImage="../image/";
	
	$sqlquery="SELECT * FROM cms_pays ORDER BY pays_name";
	$sqlres=mysql_query($sqlquery);
	$allLangueItems=array();
	while($row=mysql_fetch_array($sqlres)){ $allLangueItems[]=array($row['pays_libelle'],($row['pays_name']),$row['pays_class']); }
	$allLangueItems=json_encode($allLangueItems);
	
	
	
?>