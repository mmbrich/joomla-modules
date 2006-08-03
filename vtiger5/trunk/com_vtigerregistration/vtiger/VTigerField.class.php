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
class VTigerField extends VtigerConnection {
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
                $this->conn = $this->VtigerConnection("fields");
	}
	function listAllowedFields($module_name) {
                $this->data = array('module_name' => $module_name);
                $this->setData($this->data);

		$result = $this->execCommand('get_portal_register_fields');
		return $result;
	}
	function GetFieldDetails($module,$columnname,$entityid)
	{
                $this->data = array(	'module'=>$module,
					'columnname'=>$columnname,
					'entityid'=>$entityid
		);
                $this->setData($this->data);
		return $this->execCommand('get_field_details');
	}
	function CreateFieldHTML($module,$columnname,$viewtype,$showlabel=true,$entityid='')
	{
		$tmp = $this->GetFieldDetails($module,$columnname,$entityid);
		$field = $tmp[0];

		if($viewtype == "edit")
			return $this->_buildEditField($field,$showlabel);
		else
			return $this->_buildDetailField($field,$showlabel);
	}
	function _buildEditField($field,$showlabel)
	{
		switch($field["uitype"]) {
 			case '55':
                        case '51':
                        case '2':
                        case '5': // Date
                        case '13': // Email
                        case '1': // Text
                        case '7': // number
                        case '9': // percent
                        case '71': // currency
                        case '17': // URL
                        case '11':
                        	$out = '<input class="inputbox" name="'.$field["columnname"].'" value="'.$field["value"].'" maxlength="'.$field["maximumlength"].'" type="text">';
                        break;
                        case '21':
                        case '19':
                        	$out = '<textarea name="'.$field["columnname"].'" value="" class="inputbox" ></textarea>';
                        break;
                        case '69': // picture
                                $out = '<input name="'.$field["columnname"].'" value="" maxlength="'.$field["maximumlength"].'" type="file" class="inputbox" >';
                        break;
                        case '56': // checkbox
                                $out = '<input name="'.$field["columnname"].'" value="" maxlength="'.$field["maximumlength"].'" type="checkbox" class="inputbox" >';
                        break;
                        case '15': // Picklist
				$values = explode(",",$this->GetPicklistValues($field["fieldid"]));
                                $out = '<select name="'.$field["columnname"].'" class="inputbox" >';
                                foreach($values as $key=>$value) {
					if($field["value"] == $value)
                                		$out .= '<option value="'.$value.'" SELECTED >'.$value.'</option>';
					else
                                		$out .= '<option value="'.$value.'">'.$value.'</option>';
                                }
                                $out .= '</select>';
                        break;
                        case '33': // Multi Picklist
				$values = explode(",",$this->GetPicklistValues($field["fieldid"]));
                                $out .= '<select MULTIPLE name="'.$field["columnname"].'[]" class="inputbox" >';
                                foreach($values as $key=>$value) {
					if(preg_match("/".$value."/",$field["value"]) && $value != "")
                                		$out .= '<option value="'.$value.'" SELECTED >'.$value.'</option>';
					else
                                		$out .= '<option value="'.$value.'">'.$value.'</option>';
                                }
                                $out .= '</select>';
                        break;
                        default:
                                $out = $field->uitype;
                        break;

		}
		if($showlabel)
			return $field["fieldlabel"]." ".$out;
		else
			return $out;
	}
	function _buildDetailField($field,$showlabel)
	{
		switch($field["uitype"]) {
 			case '55':
                        case '51':
                        case '2':
                        case '5': // Date
                        case '13': // Email
                        case '1': // Text
                        case '7': // number
                        case '9': // percent
                        case '71': // currency
                        case '17': // URL
                        case '11':
                        case '15': // Picklist
                        case '33': // Multi Picklist
                        	$out = '<span name="'.$field["columnname"].'">'.$field["value"].'</span>';
                        break;
                        case '21':
                        case '19':
                        	$out = '<textarea name="'.$field["columnname"].'" class="inputbox" DISABLED>'.$field["value"].'</textarea>';
                        break;
                        case '69': // picture
                                $out = '<img name="'.$field["columnname"].'" alt="'.$field["name"].'" src="'.$field["value"].'" />';
                        break;
                        case '56': // checkbox
                                $out = '<input name="'.$field["columnname"].'" value="" maxlength="'.$field["maximumlength"].'" type="checkbox" class="inputbox" DISABLED>';
                        break;
                        default:
                                $out = "Unknown UI Type --> ".$field->uitype;
                        break;

		}
		if($showlabel)
			return $field["fieldlabel"]." ".$out;
		else
			return $out;

	}
        function GetPicklistValues($fieldid)
        {
                $this->data = array('fieldid' => $fieldid);
                $this->setData($this->data);

		$result = $this->execCommand('get_picklist_values');
		return $result;
        }
}
?>
