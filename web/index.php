<?php

  header('location:admin/');
	
	// ******************************** BOOTSTRAP ******************************

	//$mode = "production"; 
	$mode = "development";
	
	$rootPath = dirname(__FILE__);
	
	// Permet de proteger zend et les applications en ligne
	$rootPath = str_replace("web","private",$rootPath);
	

	$confPath = $rootPath  . DIRECTORY_SEPARATOR . 'config';
	
	// Define path to application directory
	defined('APPLICATION_PATH')
	    || define('APPLICATION_PATH', realpath($rootPath . '/application'));
	
	// Define application environment
	defined('APPLICATION_ENV')
	    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : $mode));
	       
	// Ensure library/ is on include_path
	set_include_path(implode(PATH_SEPARATOR, array(
		APPLICATION_PATH,
		APPLICATION_PATH . '/models',
	  	APPLICATION_PATH.'/../library',
	    get_include_path(),
	)));
	
	require_once($rootPath.'/ZendCacheExtended.class.php');
	require_once($rootPath.'/mainlib.php');
	
	/** Zend_Application */
	
	require_once 'Zend/Application.php'; 
	require_once('Zend/Loader.php'); 
	require_once "Zend/Db/Table/Abstract.php";
	
	/** configuration */
	
	$application = new Zend_Application(
	    APPLICATION_ENV, 
	    APPLICATION_PATH . '/configs/application.ini'
	);
	
	
	$config		 	= new Zend_Config_Ini( APPLICATION_PATH . '/configs/application.ini',   $mode);
	$configRoute 	= new Zend_Config_Ini( APPLICATION_PATH . '/configs/routes.ini',   		$mode);
	$configSession	= new Zend_Config_Ini( APPLICATION_PATH . '/configs/session.ini',  		$mode);
	
	/** Class Model */
	Zend_Loader::loadClass('TPages');
	Zend_Loader::loadClass('TAgenda');
	Zend_Loader::loadClass('TVideos');
								
	// ******************************** SESSION ***********************************
	
	Zend_Session::setOptions($configSession->toArray());
	Zend_Session::setOptions(array('save_path' => APPLICATION_PATH . $configSession->save_path));
	Zend_Registry::set('session', $session = new Zend_Session_Namespace($configSession->name));
	
 	/** cache */
 	$cache = ZendCacheExtended::factory();
 	
 	$filepagename = str_replace("/","",$_SERVER['REQUEST_URI']);
 	$filepagename = str_replace("-","",$filepagename);
 	$filepagename = str_replace(".html","",$filepagename);
 	
 	if(isset($_SESSION['cotestade']['user'])){
	 	$cache->clean(Zend_Cache::CLEANING_MODE_ALL);
 	}else{	
	 	$cache->start($filepagename);
	}
 	
	// ******************************** LOG ******************************
	
	$log = new Zend_Log($writer = new Zend_Log_Writer_Stream(APPLICATION_PATH . "/logs/log.log"));
	$log->setEventItem('user_agent',$_SERVER['HTTP_USER_AGENT']);
	$log->setEventItem('client_ip',$_SERVER['REMOTE_ADDR']);
	$log->addPriority('USER', 8);
								
	$format = '%client_ip% %user_agent%' . Zend_Log_Formatter_Simple::DEFAULT_FORMAT;
	$writer->setFormatter(new Zend_Log_Formatter_Simple($format));
	Zend_Registry::set('log', $log);	
	
	// ******************************** FRONT ******************************
	
	$frontController = Zend_Controller_Front::getInstance()
								->setControllerDirectory(APPLICATION_PATH . '/controllers')
								->throwExceptions(false)
								->setParam('debug', $config->debug)
								->setParam('locale', $locale)
								->setParam('config', $config);

	// ******************************** ROUTEUR ******************************
	
	$router = $frontController->getRouter()
								->removeDefaultRoutes()
								->addConfig($configRoute, 'routes');

	// ******************************** ACL *************************************
	
	if (! isset($session->acl)) {
	    $acl = new Zend_Acl();
	    $acl->addRole(new Zend_Acl_Role('user'));
	    $acl->add(new Zend_Acl_Resource('signin'));
	    $session->acl = $acl;
	}	
	
	// ******************************** DATABASE *******************************
	
	try {
	    $db = Zend_Db::factory('Pdo_Mysql',$paramsBDD);
		$db->getConnection();
	    Zend_Db_Table::setDefaultAdapter($db);
    	$db->getConnection();
	} catch (Zend_Db_Adapter_Exception $e) {
		$log->crit($e);
	} catch (Zend_Db_Exception $e) {
		$log->crit($e);
	}
	
	// ******************************** MVC ******************************
	
	Zend_Layout::startMvc(array('layoutPath' => APPLICATION_PATH . '/views/layouts'));
	$view = new Zend_View();
	$view->setEncoding('UTF-8');
	
	// ******************************** DISPATCH ******************************
	
	try {
	    $frontController->dispatch();
	    
	    //$filepagename
	    
	} catch (Zend_Exception $e) {
		$log->crit($e);
	}
