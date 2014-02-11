<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }
	public function plandusiteAction(){
	}
	public function rechercheAction(){
		
		$request			= $this->getRequest();
		$words 				= $request->getparam('word');
		$this->view->words  = str_replace(' ','+',$request->getparam('word'));
		$pages 				= new tpages();
		$this->view->res 	= $pages->getpagesbywords($words);
		
		 
	}
    public function indexAction()
    {
	    
    	$id		 						= 1; // ID de la page d'accueil par defaut
    	
	    $pages 							= new TPages();
	    $page							= $pages->getPageById($id);
	    $this->view->titre_page			= $page->titre;
	    $this->view->page				= $page;
	    
	    if(!isset($_SESSION['ville_saint_denis']['user']) && $this->view->page->urlredirect!=''){
		    header('location:'.$this->view->page->urlredirect);
	    }
	    
 	    $infoPratique 				= array();
 	    $info	 					= explode(",",$page->infopratique_ids);
     	foreach($info as $id) {	
 	    	if((int) $id && $id!="")
    			$infoPratique[]=$pages->getPageById($id);
		}
 	    $this->view->infoPratique 	= $infoPratique;

	    $liensInterne 				= array();
	    $internes 					= explode(",",$page->lieninterne_ids);
    	foreach($internes as $id)  {
			if((int) $id && $id!="")
	    		$liensInterne[]=$pages->getPageById($id);
   		}
	    $this->view->liensInterne	= $liensInterne;
	    
	    $demarches 					= array();
	    $demarchesids				= explode(",",$page->demarches_ids);
    	foreach($demarchesids as $id)  {
			if((int) $id && $id!="")
	    		$demarches[]=$pages->getPageById($id);
   		}
	    $this->view->demarches	= $demarches;
	    
	    $this->view->meta_title			= $page->meta_title;
	    $this->view->meta_tags			= $page->meta_tags;
	    $this->view->meta_description	= $page->meta_description;
	    $this->view->viewsidebar		= $page->sidebar_display;
		$this->view->autorised			= $pages->isAutorised($id);
		
		
		if($page->agenda_display==1){
			$agenda 					= new TAgenda();
			$this->view->evenements 	= $agenda->getEvenements(0,3);
			
		}
		if($page->actualites==1){
			$agenda 					= new TAgenda();
 			$type		 				= "Actualite";
 			$this->view->type			= $type; 
 			$this->view->actuevenements = $agenda->getEvenements(0,10,$type);	 
 			$this->view->actucategories = $agenda->getCategories();
		
		}
		
	    
    }

}

