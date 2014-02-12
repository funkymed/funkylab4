<?php

class RssController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
	    // action body
    }
    
    public function agendaAction()
    {
	    Zend_Layout::getMvcInstance ()->disableLayout();
	    $response = $this->getResponse();
        $response->setHeader( 'Content-Type', 'text/xml; utf-8' );
        
		$agenda 					= new TAgenda();
		$this->view->evenements 	= $agenda->getEvenements(0,10);
		
		$this->view->url			= "http://".$_SERVER['HTTP_HOST'].$this->_request->getBaseUrl();

    }

    public function actualitesAction()
    {
	    Zend_Layout::getMvcInstance ()->disableLayout();
	    $response = $this->getResponse();
        $response->setHeader( 'Content-Type', 'text/xml; utf-8' );
        
		$agenda 					= new TAgenda();
		$this->view->evenements 	= $agenda->getEvenements(0,10,"Actualite");
		
		$this->view->url			= "http://".$_SERVER['HTTP_HOST'].$this->_request->getBaseUrl();

    }    
    

}

