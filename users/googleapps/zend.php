<?php

global $gdata;

function init_zend_gapps() {
	global $gdata;
	
	set_include_path(dirname(__FILE__).'/library/'. PATH_SEPARATOR . get_include_path());
	require_once('Zend/Loader.php');
	
	Zend_Loader::loadClass('Zend_Gdata');
	Zend_Loader::loadClass('Zend_Gdata_ClientLogin');
	Zend_Loader::loadClass('Zend_Gdata_Gapps');
	
	//--------------------------------------------------------//
	
	$domain = LOWREZ_DOMAIN;
	$emailaddr = "GETOPTION".'@'.LOWREZ_DOMAIN; //FIXME
	$emailpw = "GETOPTION"; //FIXME
	
	$service = Zend_Gdata_Gapps::AUTH_SERVICE_NAME;
	
	$client = Zend_Gdata_ClientLogin::getHttpClient($emailaddr, $emailpw, $service);
	$gdata = new Zend_Gdata_Gapps($client, $domain);
	
	//--------------------------------------------------------//
	
}

class GAppsObject {
	
	private $obj;
	public $props = array();
	
	function __construct($group) {
		$this->obj = $group;
		
		foreach ($group->property as $p) {
			$this->props[$p->name] = $p->value;
		}
		
	}
	
	function __get($prop) {
		return $this->props[$prop];
	}
	
}