<?php

require_once('Zend/Cache.php');

class ZendCacheExtended extends Zend_Cache {
	
	private static $defaultLifetime;
	private static $defaultCacheDir;
	private static $frontendOptions;
	private static $backendOptions;
	private static $searchInFilename;
	private static $replaceInFilename;
	
	public function __construct() {
		$this->setFrontendOptions(self::getDefaultLifetime());
		$this->setBackendOptions(self::getDefaultCacheDir());
	}
	
	private function setDefaultLifetime() {
		self::$defaultLifetime = 7200;	// 2 heures
	}
	
	private function getDefaultLifetime() {
		if ( is_null(self::$defaultLifetime) ) {
			self::setDefaultLifetime();
		}
		return self::$defaultLifetime;
	}
	
	private function setDefaultCacheDir() {
		global $rootPath;
		if ( $rootPath == '' || is_null($rootPath) ) {
			throw new Exception('You must provide ROOT variable in mainlib file');
		}
		self::$defaultCacheDir = (substr($rootPath, -1) != '/' && substr($rootPath, -1) != '\\') ? $rootPath . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR : 'cache' . DIRECTORY_SEPARATOR;
	}
	
	private function getDefaultCacheDir() {
		if ( is_null(self::$defaultCacheDir) ) {
			self::setDefaultCacheDir();
		}
		return self::$defaultCacheDir;
	}
	
	public function setFrontendOptions($lifetime) {
		if ( is_numeric($lifetime) ) {
			self::$frontendOptions = array(
				'lifetime'					=> $lifetime,	// temps de vie du cache
				'automatic_serialization'	=> true,
			   'regexps' => array(
			    	'^/$' 		=> array('cache' => true),
			       '^/index/'	=> array('cache' => true),
			       '^/agenda/' 	=> array('cache' => true),
			       '^/pages/' 	=> array('cache' => true),
			       '^/rss/' 	=> array('cache' => true),
			       '^/video/' 	=> array('cache' => true),
			       '^/index/recherche/' => array(
						'cache' => true,
						'cache_with_post_variables' => true,
						'make_id_with_post_variables' => true
					)
			   )
			);	
			
		} else {
			throw new Exception('lifetime argument must be an integer');
		}
	}
	
	public function getFrontendOptions() {
		if ( !is_array(self::$frontendOptions) || count(self::$frontendOptions) == 0 ) {
			self::setFrontendOptions(self::getDefaultLifetime());
		}
		return self::$frontendOptions;
	}
	
	public function setBackendOptions($cache_dir) {
		if ( !empty($cache_dir) ) {
			if ( !is_dir($cache_dir) ) {
				mkdir($cache_dir, 0777, true);
			}
			self::$backendOptions = array(
				'cache_dir' => $cache_dir
			);
		} else {
			throw new Exception('cache_dir argument cannot be null');
		}
	}
	
	public function getBackendOptions() {
		if ( !is_array(self::$backendOptions) || count(self::$backendOptions) == 0 ) {
			self::setBackendOptions(self::getDefaultCacheDir());
		}
		return self::$backendOptions;
	}
	
	public static function setSearchInFilename($v) {
		self::$searchInFilename = $v;
	}
	
	public static function getSearchInFilename() {
		if ( self::$searchInFilename == '' || is_null(self::$searchInFilename) ) {
			self::setSearchInFilename(array("/","&","?","-","=","’"));
		}
		return self::$searchInFilename;
	}
	
	public static function setReplaceInFilename($v) {
		self::$replaceInFilename = $v;
	}
	
	public static function getReplaceInFilename() {
		if ( self::$replaceInFilename == '' || is_null(self::$replaceInFilename) ) {
			self::setReplaceInFilename("_","_","_","_","_","'");
		}
		return self::$replaceInFilename;
	}
	
	public static function factory() {
		// créer un objet Zend_Cache_Core
		return parent::factory('Page', 'File', self::getFrontendOptions(), self::getBackendOptions());
	}
	
}

?>