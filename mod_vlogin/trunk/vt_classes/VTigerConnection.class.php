<?php
/*
 *  This program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; version 2 of the License.
 *    
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 * (c) 2005 Matthew Brichacek <mmbrich@fosslabs.com.
 *
 */
require_once("includes/nusoap/lib/nusoap.php");
class VTigerConnection
{
	var $client;
	var $result;
	var $data;
	var $command;
	var $server = "http://netxccel.fosslabs.com/vtiger";

	function VTigerConnection()
	{
		$this->client = new soapclient($this->server."/userserialize.php");
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
