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

	function VTigerConnection($file)
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
  
		$this->client = new soapclient($vtiger_lead_soapserve."/".$file);
	}
	function setData($data)
	{
		$this->data = $data;
	}
	function execCommand($command)
	{
		$this->result = $this->client->call($command, $this->data);
		if(($this->client->getError()) || ($this->result == "failed"))
			return "failed";
		else
			return $this->result;
	}
}
?>
