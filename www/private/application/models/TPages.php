<?php
/**
 * TPages - Passerelle vers la table user
 * 
 * @package application
 * @subpackage models
 */
class TPages extends Zend_Db_Table_Abstract
{
	var $ChemindeFer = array();
	
	//Recuperation de l'arbo des pages
	public function getMenu(/*boolean*/ $menufooter=false){
		return $this->getPageByParentID(0,false,$menufooter);
	}	
	
	//Recuperation d'une page par son ID
	public function getPageById(/*int*/ $id){
		
		$select = $this->getDefaultAdapter()->select()
					                        ->from('site_page')
					                        ->where("id_page=?",$id)
					                        //->where("online=?",1)
					                        ->limit(1);
					                        
		$stmt 				= $select->query();
	 	$rows 				= $stmt->fetchAll(Zend_Db::FETCH_CLASS);
	 	$row 				= $rows[0];
	 	
	 	//Initialisation du chemin de fer
	 	$this->ChemindeFer	= array();
	 	if($row->id_page!=1){
		 	//Ajout au chemin de fer de la page courante
		 	$i					= $item = new subobj();
		 	$i->id_page			= $row->id_page;
		 	$i->parent_id		= $row->parent_id;
		 	$i->titre			= $row->titre;
		 	$this->ChemindeFer[]= $i;	 	
		 	
			//Recuperation du chemin de fer à partir de la page	 	
		 	$this->getCheminFerToId($row->parent_id);
		}
	 	//Format de base 1c-100
	 	$row->format		= ($row->format=="") ? '1c-100' : $row->format;
	 	$format 			= explode("-",$row->format);
	 	
	 	//Nombre de colonne
	 	$row->nbcolonne 	= sizeof($format)-1;
	 	//Recuperation du format en fonction du type et de la mise en page
	 	$colonnes 			= $this->getFormat($row->mise_en_page,$row->format,$row->actualites);
	 	
	 	
	 	$row->mise_en_page 	= ($row->mise_en_page=="") ? json_encode($colonnes) : $row->mise_en_page;
	 	
	 	//Recuperation de l'arbo des enfants
	 	$row->children		= $this->getPageByParentID($row->id_page,true);
	 	$row->brothers		= $this->getPageByParentID($row->parent_id,true);

	 	foreach($this->ChemindeFer as $k=>$v)
			$this->ChemindeFer[$k]->url="/pages/".$v->id_page."-".$this->clean_urlrewriting($v->titre).".html"; 	
	 		 	
	 	//Ajout de l'accueil au chemin de fer
	 	if($v->id_page!=1){
		 	$i					= $item = new subobj();
		 	$i->url				= "/index.html";
		 	$i->titre			= "Accueil";
		 	array_unshift($this->ChemindeFer, $i);
		}	 	
	 	
	 	
	 	
	 	$row->chemindefer=$this->ChemindeFer;
	 	
	 	//Attribution des largeurs de chaque colonne
	 	for($x=0;$x<$row->nbcolonne;$x++) 
	 		$colonnes[$x]->width=$format[$x+1];
	 	
	 	$row->colonnes = $colonnes;
	 	$row->url 	   = "/pages/".$row->id_page."-".$this->clean_urlrewriting($row->titre).".html";
	 	
	 	
	 	if(trim($row->id_contact_fk)!=""){
			$contacts = explode(",",$row->id_contact_fk);
			foreach($contacts as $contact){
				if($C = $this->getContactById($contact)){
					$row->contacts[]=$C;
				}
			}	
		}

	 	
	 	
		return $row;
	}
	
	//Structure de base
	private function getFormat($mise_en_page,$format,$actualites=false){
		
		if($mise_en_page==""){
			
			$colonnes=array();
			//2 colonnes
			if($format=="2c-25-75"){
				$item = new objStructure();
				$item->addItem("video","video");
				$item->addItem("fichiers","file");
				$colonnes[]=$item;
				
				$item = new objStructure();
				$item->addItem("chapeau","htmleditor");
				$item->addItem("html","htmleditor");
				$colonnes[]=$item;
				
			}else if($format=="2c-75-25"){
				$item = new objStructure();
				$item->addItem("chapeau","htmleditor");
				$item->addItem("html","htmleditor");
				$colonnes[]=$item;
				
				$item = new objStructure();
				$item->addItem("video","video");
				$item->addItem("fichiers","file");
				$colonnes[]=$item;
				
			}else if($format=="2c-50-50"){
				$item = new objStructure();
				$item->addItem("chapeau","htmleditor");
				$item->addItem("html","htmleditor");
				$colonnes[]=$item;
				
				$item = new objStructure();
				$item->addItem("video","video");
				$item->addItem("fichiers","file");
				$colonnes[]=$item;
				
			//3 colonnes
			}else if($format=="3c-50-25-25"){
				$item = new objStructure();
				$item->addItem("html","htmleditor");
				$colonnes[]=$item;
				
				$item = new objStructure();
				$item->addItem("chapeau","htmleditor");
				$colonnes[]=$item;
				
				$item = new objStructure();
				$item->addItem("video","video");
				$item->addItem("fichiers","file");
				$colonnes[]=$item;
				
			}else if($format=="3c-25-50-25"){
				$item = new objStructure();
				$item->addItem("html","htmleditor");
				$colonnes[]=$item;
				
				$item = new objStructure();
				$item->addItem("chapeau","htmleditor");
				$colonnes[]=$item;
				
				$item = new objStructure();
				$item->addItem("video","video");
				$item->addItem("fichiers","file");
				$colonnes[]=$item;
				
			}else if($format=="3c-25-25-50"){
				$item = new objStructure();
				$item->addItem("html","htmleditor");
				$colonnes[]=$item;
				
				$item = new objStructure();
				$item->addItem("chapeau","htmleditor");
				$colonnes[]=$item;
				
				$item = new objStructure();
				$item->addItem("video","video");
				$item->addItem("fichiers","file");
				$colonnes[]=$item;
				
			}else if($format=="3c-33-33-33"){
				
				$item = new objStructure();
				$item->addItem("html","htmleditor");
				$colonnes[]=$item;
				
				$item = new objStructure();
				$item->addItem("chapeau","htmleditor");
				$colonnes[]=$item;
				
				$item = new objStructure();
				$item->addItem("video","video");
				$item->addItem("fichiers","file");
				$colonnes[]=$item;
				
			//1 colonne
			}else{ //1c-100
				$item = new objStructure();
				$item->addItem("chapeau","htmleditor");
				$item->addItem("html","htmleditor");
				$item->addItem("fichiers","file");
				$item->addItem("video","video");
				$colonnes[]=$item;
			}
			
			if($actualites==true)
	 			$colonnes[0]->addItem("actualites","actualites");
	 			
		}else{
			$colonnes  = json_decode($mise_en_page);
		}
		return $colonnes;
	}

	//Recuperation recursive de toutes les pages
	private function getPageByParentID(/*int*/ $id=0,$stop=false,$menufooter=false){
		$item = array();
		$select = $this->getDefaultAdapter()->select()
					                        ->from('site_page',array('id_page','parent_id','titre'))
					                        ->where("parent_id=?",$id)
					                        ->where("online=?",1)
					                        ->where("menu_footer=?",$menufooter)
					                        ->order('ordre ASC');
		$stmt = $select->query();
	 	$rows = $stmt -> fetchAll(Zend_Db::FETCH_CLASS);
		foreach($rows as $row){
			if($stop==false){
				$row->pages 	= $this->getPageByParentID($row->id_page);
			}
			$row->url 		= "/pages/".$row->id_page."-".$this->clean_urlrewriting($row->titre).".html";
			$row->leaf 		= (count($row->pages)==0) ? true : false;
			$item[]			= $row;
		}	
		return $item;
	}
	
	//Recuperation recursive de toutes les pages à partir de
	private function getAllIdByParentID(/*int*/ $id=0,$started=false){
		if($started==false){
			$this->validateID = array();
			$this->validateID[]=$id;
		}
		$item = array();
		$select = $this->getDefaultAdapter()->select()
					                        ->from('site_page',array('id_page'))
					                        ->where("parent_id=?",$id)
					                        ->where("online=?",1)
					                        ->order('ordre ASC');
		$stmt = $select->query();
	 	$rows = $stmt -> fetchAll(Zend_Db::FETCH_CLASS);
		foreach($rows as $row){
			$this->validateID[]	= $row->id_page;
			$this->getAllIdByParentID($row->id_page,true);
		}	
		if(isset($this->validateID))
			return $this->validateID;
	}	
	
	public function isAutorised(/*int*/ $id){
		if(isset($_SESSION['cotestade']['user'])){
			
			if($_SESSION['cotestade']['user']['admin']!='admin' && $_SESSION['cotestade']['user']['admin']!='sadmin'){
				$IDS = $this->getAllIdByParentID($_SESSION['cotestade']['user']['id_arbo_fk']);
				return (in_array($id,$IDS)) ? true : false;
			}
			return	true;
		}
		return	false;
	}
	
	//Recuperation du chemin de fer recursif inversé
	public function getCheminFerToId(/*int*/ $id,/*string*/ $mode="start"){
		
		$select = $this->getDefaultAdapter()->select()
					                        ->from('site_page',array('id_page','parent_id','titre'))
					                        ->where("id_page=?",$id)
					                        ->where("online=?",1)
					                        ->limit(1);
		$stmt 	= $select->query();
	 	$rows 	= $stmt -> fetchAll(Zend_Db::FETCH_CLASS);
	 	
		if($row	= $rows[0]){
			array_unshift($this->ChemindeFer, $row);
			$this->getCheminFerToId($row->parent_id,"continue");
		}
	}
	
	//Generation du chemin de fer
	public function getChemindeFer(){
		return $this->ChemindeFer;
	}
	
	//Tools
	private function clean_urlrewriting(/*string*/ $Txt){
		$Txt = utf8_decode($Txt);
		$Txt=$this->ReplaceAccent(strtolower($Txt));
		//$Txt=ereg_replace("-","",$Txt);
		$Txt=ereg_replace(" ","-",$Txt);
		$Txt=ereg_replace("_","-",$Txt);
		$Txt=ereg_replace("[^[:alnum:]-]","",$Txt);
		$Txt=ereg_replace("-+","-",$Txt);
		$Txt=ereg_replace("^(-+)","",$Txt);
		$Txt=ereg_replace("(-+)$","",$Txt);
		
		return $Txt;
	}
	private function ReplaceAccent(/*string*/ $Txt){
		
		$a='ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËéèêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ';
		$na='AAAAAAaaaaaaOOOOOOooooooEEEEeeeeCcIIIIiiiiUUUUuuuuyNn';
		$Txt = strtr($Txt,$a,$na);
		return $Txt;
	}
	public function truncate($string, $length = 80, $etc = '...', $break_words = false, $middle = false){
	    if ($length == 0)
	        return '';
	
	    if (strlen($string) > $length) {
	        $length -= min($length, strlen($etc));
	        if (!$break_words && !$middle) {
	            $string = preg_replace('/\s+?(\S+)?$/', '', substr($string, 0, $length+1));
	        }
	        if(!$middle) {
	            return substr($string, 0, $length) . $etc;
	        } else {
	            return substr($string, 0, $length/2) . $etc . substr($string, -$length/2);
	        }
	    } else {
	        return $string;
	    }
	}
	//Recuperation d'un contact par ID
	public function getContactById(/*int*/ $id){
		$query = "SELECT * FROM contacts WHERE id_contact='".$id."'";
		$stmt = $this->_db->query($query);
		$rows = $stmt -> fetchAll(Zend_Db::FETCH_CLASS);
		if(count($rows)>0){
			$row = $rows[0];
			return $row;
		}else{
			return false;
		}
	}	
	
	//Recherche
	public function getPagesByWords(/*string*/ $words){
		if($words!=""){
			$item = array();
			$res = array();
			
			$where  = array();
			$where[]= "online=1";
			
			$words = explode(' ',$words);          
			
			foreach($words as $word){
				$where[]= "(chapeau like '%".$word."%' OR html like '%".$word."%' OR titre like '%".$word."%' OR titre_complet like '%".$word."%')";
			}
			
			$query = "SELECT id_page FROM site_page WHERE ".implode(' AND ',$where)." ORDER BY id_page ASC";

 			$stmt = $this->_db->query($query);
 		
		 	$rows = $stmt -> fetchAll(Zend_Db::FETCH_CLASS);
			foreach($rows as $row){
				$res[] = $this->getPageById($row->id_page);
			}	
			
			return $res;

		}
	}
	
}
/*OBJ Speciaux*/
class subobj {}
class objStructure{
	function objStructure(){
		$this->items = array();
	}
	function addItem($field,$type,$default=""){
		
		$obj = new subobj();
		$obj->field=$field;
		$obj->type=$type;
		$obj->default=$default;
		$this->items[]=$obj;
	}
}