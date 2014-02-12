<?php
	
	require_once("../../../php/const.php");
	require_once("arraytoxml.php");
	
	function getPage($id){
		$item = array();
		$res = mysql_query("SELECT id_page,parent_id,titre,open FROM site_page WHERE parent_id=".$id." ORDER BY ordre ASC");
		print mysql_error();
		while($row = mysql_fetch_object($res)){
			$row->pages = getPage($row->id_page);
			if(count($row->pages)==0){
				unset($row->pages);
				$row->leaf="1";
			}else{
				$row->leaf="0";
			}
			$row->id="page-".$row->id_page;
			$item[]['page']=$row;
		}
		return $item;
	}
	$pages = array();
	if($_SESSION[sessionName]['user']['admin']!='admin' && $_SESSION[sessionName]['user']['admin']!='sadmin'){
		$pages = getPage($_SESSION[sessionName]['user']['id_arbo_fk']);
	}else{
		$pages = getPage(0);
	}
	header("Content-type: text/xml");
	print ArrayToXML::toXml($pages,"pages");