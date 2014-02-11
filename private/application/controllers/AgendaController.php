<?php

class AgendaController extends Zend_Controller_Action
{

    public function init()
    {
    }

    public function indexAction()
    {
	    $this->view->titre_page	= "Agenda";
	    $request 			= $this->getRequest();
    	$type		 		= ($request->getParam('type')) ? $request->getParam('type') : null;
    	$start		 		= ($request->getParam('start')) ? $request->getParam('start') : 0;
    	$limit		 		= 10;    	
    	$this->view->type	= $type; 
    	$agenda 			= new TAgenda();
    	
	    if($type!=null){
			$this->view->type = $type;
			$this->view->evenements = $agenda->getEvenements($start,$limit,$type);	 
		}else{
			$this->view->evenements = $agenda->getEvenements($start,$limit);
		}
    	
	    $this->view->categories = $agenda->getCategories();
	    
		//Pagination
		$nbsujets = $agenda->getNbEvenements($type);
		$nbpage = $nbsujets/$limit-1;
		$nbpage = (round($nbpage)<$nbpage) ? round($nbpage)+1 : round($nbpage); 
		
		if($nbpage>0){
			$prev = ($start-1<0) ? 0 : $start-1;
			$next = ($start+1>$nbpage) ? $nbpage : $start+1;
			$pagination = array();
			$url = ($type!=null) ? "/agenda/".$type : "/agenda";
			
			if($start>0)
				$pagination[]= '<a class="prevpage" href="'.$url.'/'.$prev.'/">page pr&eacute;c&eacute;dente</a>';
				
			if($start<$nbpage) 
				$pagination[]='<a class="nextpage" href="'.$url.'/'.$next.'/">page suivante</a>';
		
			$pagination = (count($pagination)>1) ? implode(" ",$pagination) : implode(" ",$pagination);
			$this->view->pagination = $pagination;
		}	
	    
    }
    
    public function pageAction()
    {
	    
	    $request 				= $this->getRequest();
    	$type		 			= ($request->getParam('type')) ? $request->getParam('type') : null;
    	$id		 				= $request->getParam('id');
    	
    	$this->view->type		= $type; 
    	$agenda 				= new TAgenda();
    	
	    $this->view->categories = $agenda->getCategories();
	  	$currentevent = $agenda->getAgendaById($id);  
	  	//print_r($currentevent);
	  	$this->view->titre_page	= $currentevent->agenda_titre;
		$this->view->currentevent = $currentevent;
    }
    
}

