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
  		$vtiger_lead_soapserver = $mambotParams->get( 'vtiger_lead_soapserver', 'basic' );
		$file="joomla";
  
		$this->client = new soapclient2($vtiger_lead_soapserver."/vtigerservice.php?service=".$file);
	}
	function setData($data)
	{
		$this->data = $data;
	}
	function execCommand($command)
	{
		if(!$this->client)
			$this->VtigerConnection();

		$this->result = $this->client->call($command, $this->data);
		if(($this->client->getError()) || ($this->result == "failed"))
			//return "failed";
			return $this->client->getError();
		else
			return $this->result;
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
  		$vtiger_lead_soapserver = $mambotParams->get( 'vtiger_lead_soapserver', 'basic' );
		return $vtiger_lead_soapserver;
	}
}
?>
