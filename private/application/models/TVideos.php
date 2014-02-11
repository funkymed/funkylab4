<?php
/**
 * TVideos - Passerelle vers la table user
 * 
 * @package application
 * @subpackage models
 */
class TVideos extends Zend_Db_Table_Abstract
{

	
		

	
	public function getVideosByPage(/*int*/ $page=0){
		
		$maxpage	= 15;
		$page		= $page*$maxpage;
		$videos 	= array();
		$query		="SELECT date_publi,title,filename FROM videos WHERE online='Y' ORDER by date_publi DESC LIMIT ".$page.",".$maxpage;
		$stmt 		= $this->_db->query($query);
		$rows 		= $stmt -> fetchAll(Zend_Db::FETCH_CLASS);
		
		foreach($rows as $row){
			if($row->filename!=""){
				$datetab=explode(" ",$row->date_publi);
				$videos[]=array(
					"date"=>$this->decodedate($datetab[0]),
					"titre"=>stripslashes($row->title),
					"file"=>"directory/".stripslashes($row->filename)
				);
			}
		}
		return $videos;
	}
	


	private function decodedate(/*date*/ $date){
		if (trim($date)!="" && $date!="0000-00-00"){
			$dayFrancais=array('Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi','Dimanche');
			$dayEnglish=array('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday');
			$mois=array('Janvier','F&eacute;vrier','Mars','Avril','Mai','Juin','Juillet','Aout','Septembre','Octobre','Novembre','D&eacute;cembre');
			$explodate=explode("-",$date);
			$theday=date("l", mktime (0,0,0,$explodate[1],$explodate[2],$explodate[0]));
			for ($aa=0;$aa<=7;$aa++){
				if ($dayEnglish[$aa]==$theday){
					break;
				}			
			}			
			if ($explodate[2]==0){
				$newdate=$mois[$explodate[1]-1]." ".$explodate[0];
			}else{
				$newdate=$dayFrancais[$aa]." ".$explodate[2]." ".$mois[$explodate[1]-1]." ".$explodate[0];
			}			
			return($newdate);
		}
	}

		
	


	public function getNbPages(){
	
		$maxpage 	= 15;
 		$query 		= "SELECT count(1) as nb FROM videos WHERE online='Y' ORDER by date_publi";
 		$stmt 		= $this->_db->query($query);
 		$rows 		= $stmt -> fetchAll(Zend_Db::FETCH_CLASS);
		$row 		= $rows[0];
		$nbpage		= $row->nb/$maxpage;
		
		$testeurpage=floor($nbpage);
		
		if ($testeurpage<$nbpage)
			$nbpage=$testeurpage+1;
		
			
		$v = array(
			"nbpages"=>$nbpage,
			"nbitems"=>$row->nb
		);	
		
		return $v;
	}
	
}
