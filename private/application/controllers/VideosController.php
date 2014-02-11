<?php

class VideosController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
	    $this->view->url			= "http://".$_SERVER['HTTP_HOST'].$this->_request->getBaseUrl();
    }
    
    
    public function xmlvideosAction()
    {
		Zend_Layout::getMvcInstance ()->disableLayout();
	    $response = $this->getResponse();
        $response->setHeader( 'Content-Type', 'text/xml; utf-8' );
		
        $request 			= $this->getRequest();
    	$page		 		= ($request->getParam('page')) ? $request->getParam('page') : 0; 
		$videos 			= new TVideos();
		$this->view->videos	= $videos->getVideosByPage($page);
    }
    
    public function xmlpagesAction()
    {
	    
		Zend_Layout::getMvcInstance ()->disableLayout();
	    $response = $this->getResponse();
        $response->setHeader( 'Content-Type', 'text/xml; utf-8' );
        
        $videos 			= new TVideos();
		$this->view->value 	= $videos->getNbPages();

    }  
    

}

