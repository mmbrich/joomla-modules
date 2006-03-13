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
require_once("VTigerConnection.class.php");
class VTigerLead {
	var $conn;
	var $data;

	function VtigerLead($servername)
	{
		$this->conn = new VtigerConnection($servername,"contactserialize.php");
	}
	function addLead($lastname,$email,$phone,$company,$country,$description='')
	{
		$this->data = array('lastname' 	=> $lastname,
				    'email' 	=> $email,
				    'phone'	=> $phone,
				    'company'	=> $company,
				    'country'	=> $country,
				    'description' => $description);
		$this->conn->setData($this->data);
		return $this->conn->execCommand('create_lead_from_webform');
	}
}
?>
