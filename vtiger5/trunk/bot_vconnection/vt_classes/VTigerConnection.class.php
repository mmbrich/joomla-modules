<?php
/*
 * The contents of this file are subject to the vtiger CRM Public License 
 * Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 *
 * Portions created by Matthew Brichacek are Copyright (C) 2005 netXccel.
 *
 * All Rights Reserved.
 *
 * Contributors: _________________
 *
 */
require_once("nusoap/lib/nusoap.php");
class VTigerConnection
{
	var $client;
	var $result;
	var $data;
	var $command;

	var $server;
	var $secure_server;
	var $proxyuser;
	var $proxyport;
	var $proxyusername;
	var $proxypassword;
	var $defaultItemid;

	var $sessionid;

	function VTigerConnection()
	{
		global $database;
		// load bot_vconnection mambot parameters
  		$query = "SELECT id FROM #__mambots WHERE element = 'bot_vconnection' AND folder = 'system'";
  		$database->setQuery( $query );
  		$id = $database->loadResult();
  		$mambot = new mosMambot( $database );
  		$mambot->load( $id );
  		$mambotParams =& new mosParameters( $mambot->params );

  		$this->server = $mambotParams->get( 'vtiger_server', 'basic' );
  		$this->secure_server = $mambotParams->get( 'vtiger_secure_server', 'basic' );
  		$this->proxyhost = $mambotParams->get( 'vtiger_proxyhost', 'basic' );
  		$this->proxyport = $mambotParams->get( 'vtiger_proxyport', 'basic' );
  		$this->proxyusername = $mambotParams->get( 'vtiger_proxyuser', 'basic' );
  		$this->proxypassword = $mambotParams->get( 'vtiger_proxypass', 'basic' );

  		$this->defaultItemid = $mambotParams->get( 'vtiger_default_itemid', 'basic' );

		$this->client = new soapclient2($this->server."/vtigerservice.php?service=joomla");
	}
	function setData($data)
	{
		$this->data = $data;
	}
	function GoSecure()
	{
		$this->client = new soapclient2($this->secure_server."/vtigerservice.php?service=joomla");
	}
	function GetCRMServer()
	{
		return $this->server;
	}
	function CheckConnection()
	{
		$this->data = array('alive'=>'checking');
		$ret = $this->execCommand('check_connection');
		if($ret == "hello")
			return true;
		else
			return false;
	}
	function execCommand($command,$secure='0')
	{
		session_start();
		if($secure != '0')
			$this->GoSecure();

		if(($_SESSION["j_secure_mode"] != '0' && isset($_SESSION["j_secure_mode"])))
			$this->GoSecure();

		if(!$this->client)
			$this->VtigerConnection();

		$this->result = $this->client->call($command, $this->data);
		if(($this->client->getError()) || ($this->result == "failed"))
			return $this->client->getError();
		else
			return $this->result;
	}
}
?>
