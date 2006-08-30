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

	var $imagestore;

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
	function GetImagePath($image)
	{
		global $database,$mosConfig_live_site,$mosConfig_absolute_path;

		// load vfield mambot info
		if(!isset($this->imagestore) || $this->imagestore == "") {
  			$query = "SELECT id FROM #__mambots WHERE element = 'vfield' AND folder = 'content'";
  			$database->setQuery( $query );
  			$id = $database->loadResult();
  			$mambot = new mosMambot( $database );
  			$mambot->load( $id );
  			$mambotParams =& new mosParameters( $mambot->params );

  			$this->imagestore = $mambotParams->get( 'vtiger_picture_store', 'basic' );
		}

		// file is already wrote
		if(is_file($mosConfig_absolute_path."/".$this->imagestore."/".$image)) {
			// we should do an md5 check to see if we need to re-write
			return $mosConfig_live_site.'/'.$this->imagestore.$image;
		} else {
			// We need to write the file.
			$this->data = array('imagepath'=>$image);
			$directory = dirname($mosConfig_absolute_path.'/'.$this->imagestore.$image);
			$filename = basename($image);

			if(!is_dir($directory))
				mkdir($directory, 0700, true);

			if (!$handle = fopen($directory.'/'.$filename, 'a')) {
         			return "Cannot open file ($filename)";
   			} else {
				$this->data = array('image'=>$image);
				$file_data = $this->execCommand('download_image');
				if (fwrite($handle, base64_decode($file_data)) === FALSE) {
					fclose($handle);
					return "Cannot write to file";
				} else {
					fclose($handle);
					return $mosConfig_live_site.'/'.$this->imagestore.$image;
				}
			}
		}
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
