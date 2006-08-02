<?php
/*
 * The contents of this file are subject to the vtiger CRM Public License
 * Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 *
 * Portions created by Matthew Brichacek are Copyright (C) 2006 FOSS Labs.
 *
 * All Rights Reserved.
 *
 * Contributors: _________________
 *
 */
global $mainframe;
require_once($mainframe->getCfg('absolute_path').'/mambots/system/vt_classes/VTigerConnection.class.php');
class VTigerField {
	var $conn;
	var $data;
	var $username;
	var $password;
	var $id;
	var $customer_name;
	var $first_name;
	var $last_name;
	var $bday;
	

	function VtigerField()
	{
		$this->conn = new VtigerConnection("fields");
	}
	function listAllowedFields($module_name) {
                $this->data = array('module_name' => $module_name);
                $this->conn->setData($this->data);

		$result = $this->conn->execCommand('get_portal_register_fields');
		return $result;
	}
        function GetPicklistValues($fieldid)
        {
                $this->data = array('fieldid' => $fieldid);
                $this->conn->setData($this->data);

		$result = $this->conn->execCommand('get_picklist_values');
		return $result;
        }
}
?>
