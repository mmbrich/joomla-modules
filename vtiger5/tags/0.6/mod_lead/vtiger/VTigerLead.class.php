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
 * (c) 2005 Foss Labs <mmbrich@fosslabs.com>
 *
 */
global $mosConfig_absolute_path;
require_once($mosConfig_absolute_path.'/mambots/system/vt_classes/VTigerConnection.class.php');

class VTigerLead extends VTigerConnection {
	var $data;

	function VtigerLead()
	{
		VTigerConnection::VTigerConnection();
	}
	function addLead($lastname,$email,$phone,$company,$country,$description='')
	{
		$this->data = array(
			'lastname' 	=> $lastname,
			'email' 	=> $email,
			'phone'	=> $phone,
			'company'	=> $company,
			'country'	=> $country,
			'description' => $description,
			'assigned_user_id' => '');
		$this->setData($this->data);
		return $this->execCommand('create_lead_from_webform');
	}
}
?>
