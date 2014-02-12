<?php
/**
 * TAgenda - Passerelle vers la table user
 * 
 * @package application
 * @subpackage models
 */
class TAgenda extends Zend_Db_Table_Abstract
{
	//Recuperation d'une actualité par son ID
	function getAgendaById(/*int*/ $id){
	 	$query = "SELECT agenda.*,";
		$query.= "DATE_FORMAT(agenda_debut,'%d/%m/%Y') as agenda_debut,";
		$query.= "DATE_FORMAT(agenda_debut,'%H') as agenda_debutH,";
		$query.= "DATE_FORMAT(agenda_debut,'%i') as agenda_debutM,";
		$query.= "DATE_FORMAT(agenda_fin,'%d/%m/%Y') as agenda_fin,";
		$query.= "DATE_FORMAT(agenda_fin,'%H') as agenda_finH,";
		$query.= "DATE_FORMAT(agenda_fin,'%i') as agenda_fintM ";
		$query.= "FROM agenda WHERE agenda_online=1 AND agenda_state='published' AND id_agenda='".$id."' AND agenda_online=1";
	
	 	$stmt = $this->_db->query($query);
		$rows = $stmt -> fetchAll(Zend_Db::FETCH_CLASS);

		if(count($rows)>0){
			$row = $rows[0];
			$row->agenda_chapeau = nl2br($row->agenda_chapeau);
			$row->url 		= "/agenda/".strtolower($row->agenda_type)."/".$row->id_agenda."-".$this->clean_urlrewriting($row->agenda_titre).".html";
			$row->contacts=array();
			if(trim($row->id_contact_fk)!=""){
				$contacts = explode(",",$row->id_contact_fk);
				foreach($contacts as $contact){
					if($C = $this->getContactById($contact)){
						$row->contacts[]=$C;
					}
				}	
			}
			return $row;
		}else{
			return false;
		}
	}		
	
	//Recuperation des categories de l'agenda
	function getCategories(){
		$c = array();
		//$query 	= "SELECT agenda_type FROM agenda WHERE agenda_online=1 AND  agenda_state='published' AND agenda_type!='' GROUP BY agenda_type ORDER BY agenda_type";
		$query 	= "SELECT agenda_type FROM agenda WHERE agenda_online=1 AND agenda_state='published' AND agenda_type!='' AND agenda_fin>=now() GROUP BY agenda_type ORDER BY agenda_type";
		$stmt = $this->_db->query($query);
		$rows = $stmt -> fetchAll(Zend_Db::FETCH_CLASS);
		foreach($rows as $row){
			if(trim($row->agenda_type)!="")
				$c[]=$row->agenda_type;
		}
		return $c;
	 	
	}
	
	//Verifis les elements par date
	function checkItemByDate(/*Y-m-d*/ $date){
		
		$a_date = explode("-",$date);
		if(count($a_date)==3){
			$query 	= "SELECT id_agenda FROM agenda WHERE agenda_online=1 AND  agenda_state='published' AND DATE_FORMAT(agenda_debut,'%Y')='".$a_date[0]."' AND DATE_FORMAT(agenda_debut,'%m')='".$a_date[1]."' AND DATE_FORMAT(agenda_debut,'%d')<='".$a_date[2]."'";
		}else if(count($a_date)==2){
			$query 	= "SELECT id_agenda FROM agenda WHERE agenda_online=1 AND  agenda_state='published' AND DATE_FORMAT(agenda_debut,'%Y')='".$a_date[0]."' AND DATE_FORMAT(agenda_debut,'%m')='".$a_date[1]."'";
		}else{
			$query 	= "SELECT id_agenda FROM agenda WHERE agenda_online=1 AND  agenda_state='published' AND DATE_FORMAT(agenda_debut,'%Y')='".$date."'";
		}
	 	
		$stmt = $this->_db->query($query);
		$rows = $stmt -> fetchAll(Zend_Db::FETCH_CLASS);

		if(count($rows)>0){
			$row = $rows[0];
			return true;
		}
		return false;
		
	}
	
	//Recuperation des actualites par date
	function getItemByDate(/*Y-m-d*/ $date,/*int*/ $start=null, /*int*/ $limit=null){
		$evenements			= array();

	 	$a_date = explode("-",$date);
		if(count($a_date)==3){
			$query 	= "SELECT id_agenda FROM agenda WHERE agenda_online=1 AND  agenda_state='published' AND DATE_FORMAT(agenda_debut,'%Y')='".$a_date[0]."' AND DATE_FORMAT(agenda_debut,'%m')='".$a_date[1]."' AND DATE_FORMAT(agenda_debut,'%d')<='".$a_date[2]."' ORDER BY agenda_debut ASC".$this->makeLimiter($start,$limit);
		}else if(count($a_date)==2){
			$query 	= "SELECT id_agenda FROM agenda WHERE agenda_online=1 AND  agenda_state='published' AND DATE_FORMAT(agenda_debut,'%Y')='".$a_date[0]."' AND DATE_FORMAT(agenda_debut,'%m')='".$a_date[1]."' ORDER BY agenda_debut ASC".$this->makeLimiter($start,$limit);
		}else{
			$query 	= "SELECT id_agenda FROM agenda WHERE agenda_online=1 AND  agenda_state='published' AND DATE_FORMAT(agenda_debut,'%Y')='".$date."' ORDER BY agenda_debut ASC".$this->makeLimiter($start,$limit);
		}
		
		$stmt = $this->_db->query($query);
		$rows = $stmt -> fetchAll(Zend_Db::FETCH_CLASS);
		foreach($rows as $row){
			if($A = $this->getAgendaById($row->id_agenda)){
				$evenements[]= $A;
			}
		}
		return $evenements;
		
	}
	
	//Recuperation des evenements
	function getEvenements(/*int*/ $start=null, /*int*/ $limit=null,/*string*/ $categorie=null){
		$evenements			= array();
		$option = ($categorie!=null && trim($categorie)!="") ? " AND agenda_type='".$categorie."'" : "";
		
		//Cas special pour les actualites, lister a l'envers
		if($categorie!=null && strtolower($categorie)=="actualite"){
			$query 	= "SELECT id_agenda FROM agenda WHERE agenda_online=1 AND  agenda_state='published' AND agenda_fin>=now() ".$option." ORDER BY agenda_debut DESC".$this->makeLimiter($start,$limit);
		}else{
			$query 	= "SELECT id_agenda FROM agenda WHERE agenda_online=1 AND  agenda_state='published' AND agenda_fin>=now() ".$option." ORDER BY agenda_debut ASC".$this->makeLimiter($start,$limit);
		}
	 	
		$stmt = $this->_db->query($query);
		$rows = $stmt -> fetchAll(Zend_Db::FETCH_CLASS);
		foreach($rows as $row){
			if($A = $this->getAgendaById($row->id_agenda)){
				$evenements[]= $A;
			}
		}
		return $evenements;
	}
	
	//Recuperation du nombre des evenements
	function getNbevenements(/*string*/ $categorie=null){
		$option = ($categorie!=null && trim($categorie)!="") ? "  AND agenda_type='".$categorie."'" : "";
		$query = "SELECT count(1) as nb FROM agenda WHERE agenda_online=1 AND agenda_state='published' AND agenda_fin>=now() ".$option;
		$stmt = $this->_db->query($query);
		$rows = $stmt -> fetchAll(Zend_Db::FETCH_CLASS);
		if(count($rows)>0){
			$row = $rows[0];
			return $row->nb;
		}else{
			return 0;
		}
		
	}
	
	//Recuperation d'un contact par ID
	function getContactById(/*int*/ $id){
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
	function makeLimiter(/*int*/ $start=null, /*int*/ $limit=null){
		if(!empty($start) && empty($limit)){
			return " LIMIT ".$start;
		}else if(!empty($start) && !empty($limit)){
			return " LIMIT ".($start*$limit).",".$limit;
		}else if(empty($start) && !empty($limit)){
			return " LIMIT 0,".$limit;
		}else{
			return "";
		}
	}
	
	
}
