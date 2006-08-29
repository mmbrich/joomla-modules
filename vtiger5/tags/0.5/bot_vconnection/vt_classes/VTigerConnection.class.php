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
	var $username;
	var $password;
	var $key = "1234567";
	var $hostname = "myjoomla";
	var $sessionid;

	function VTigerConnection($file='joomla')
	{
		global $database;
		// load bot_vconnection mambot parameters
  		$query = "SELECT id FROM #__mambots WHERE element = 'bot_vconnection' AND folder = 'system'";
  		$database->setQuery( $query );
  		$id = $database->loadResult();
  		$mambot = new mosMambot( $database );
  		$mambot->load( $id );
  		$mambotParams =& new mosParameters( $mambot->params );
  		$vtiger_soapserver = $mambotParams->get( 'vtiger_soapserver', 'basic' );
		$file="joomla";
  
		$this->client = new soapclient2($vtiger_soapserver."/vtigerservice.php?service=".$file);
	}
	function setData($data)
	{
		$this->data = $data;
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
	function GoSecure()
	{
		global $database;

		// load bot_vconnection mambot parameters
  		$query = "SELECT id FROM #__mambots WHERE element = 'bot_vconnection' AND folder = 'system'";
  		$database->setQuery( $query );
  		$id = $database->loadResult();
  		$mambot = new mosMambot( $database );
  		$mambot->load( $id );
  		$mambotParams =& new mosParameters( $mambot->params );
  		$vtiger_soapserver = $mambotParams->get( 'vtiger_soapserver', 'basic' );

		$this->client = new soapclient2(preg_replace('/http/i','https',$vtiger_soapserver)."/vtigerservice.php?service=joomla");
	}
	function GetCRMServer()
	{
		global $database;

		// load bot_vconnection mambot parameters
  		$query = "SELECT id FROM #__mambots WHERE element = 'bot_vconnection' AND folder = 'system'";
  		$database->setQuery( $query );
  		$id = $database->loadResult();
  		$mambot = new mosMambot( $database );
  		$mambot->load( $id );
  		$mambotParams =& new mosParameters( $mambot->params );
  		$vtiger_soapserver = $mambotParams->get( 'vtiger_soapserver', 'basic' );
		return $vtiger_soapserver;
	}
}
?>
