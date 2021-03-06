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

class VTigerLead {
	var $conn;
	var $data;

	function VtigerLead()
	{
		$this->conn = new VtigerConnection("webforms");
	}
	function addLead($lastname,$email,$phone,$company,$country,$description='')
	{
		$this->data = array('lastname' 	=> $lastname,
				    'email' 	=> $email,
				    'phone'	=> $phone,
				    'company'	=> $company,
				    'country'	=> $country,
				    'description' => $description,
				    'assigned_user_id' => '');
		$this->conn->setData($this->data);
		return $this->conn->execCommand('create_lead_from_webform');
	}
}
?>
