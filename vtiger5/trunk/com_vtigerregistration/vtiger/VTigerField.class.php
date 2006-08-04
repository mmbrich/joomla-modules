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
	function CreateFieldHTML($module,$columnname,$viewtype,$showlabel=true,$entityid='',$picnum='all')
	{
		$tmp = $this->GetFieldDetails($module,$columnname,$entityid);
		$field = $tmp[0];

		if($viewtype == "edit")
			return $this->_buildEditField($field,$showlabel);
		else
			return $this->_buildDetailField($field,$showlabel,$picnum);
	}
	function _buildEditField($field,$showlabel)
	{
        	$classname  = 'inputbox';

		switch($field["uitype"]) {
 			case '55':
                        case '51': // Acount ID
                        case '2':
                        case '5': // Date
                        case '13': // Email
                        case '1': // Text
                        case '7': // number
                        case '9': // percent
                        case '71': // currency
                        case '17': // URL
                        case '11':
                        	$out = '<input class="'.$classname.'" name="vtiger_'.$field["columnname"].'" value="'.$field["value"].'" maxlength="'.$field["maximumlength"].'" type="text">';
                        break;
                        case '21':
                        case '19':
                        	$out = '<textarea name="vtiger_'.$field["columnname"].'" class="'.$classname.'" >'.$field["value"].'</textarea>';
                        break;
                        case '69': // picture

                                $out = '<input name="vtiger_'.$field["columnname"].'" value="" maxlength="'.$field["maximumlength"].'" type="file" class="'.$classname.'" >';
                        break;
                        case '56': // checkbox
				if($field["value"] == "on" || $field["value"] == 1)
                                	$out = '<input name="vtiger_'.$field["columnname"].'" CHECKED type="checkbox" class="'.$classname.'"  onclick="toggle_cb(this);">';
				else
                                	$out = '<input name="vtiger_'.$field["columnname"].'" type="checkbox" class="'.$classname.'">';
                        break;
                        case '15': // Picklist
				$values = explode(",",$field["values"]);
                                $out = '<select name="vtiger_'.$field["columnname"].'" class="'.$classname.'" >';
				$j=0;
                                foreach($values as $key=>$value) {
					if($value != "" && ($field["value"] == $value))
                                		$out .= '<option value="'.$value.'" SELECTED >'.$value.'</option>';
					else if($j==0 && $field["value"] == "")
                                		$out .= '<option value="'.$value.'" SELECTED >'.$value.'</option>';
					else if($value != "")
                                		$out .= '<option value="'.$value.'">'.$value.'</option>';
					$j++;
                               }
                               $out .= '</select>';
                        break;
                        case '33': // Multi Picklist
				$values = explode(",",$field["values"]);
                                $out .= '<select MULTIPLE name="vtiger_'.$field["columnname"].'[]" class="'.$classname.'" >';
                                foreach($values as $key=>$value) {
					if(preg_match("/".$value."/",$field["value"]) && $value != "")
                                		$out .= '<option value="'.$value.'" SELECTED >'.$value.'</option>';
					else if($value != "")
                                		$out .= '<option value="'.$value.'">'.$value.'</option>';
                                }
                                $out .= '</select>';
                        break;
                        default:
                                $out = "";
                        break;

		}
		if($showlabel)
			return $field["fieldlabel"]." ".$out;
		else
			return $out;
	}
	function _buildDetailField($field,$showlabel,$picnum)
	{
		switch($field["uitype"]) {
 			case '55':
                        case '51': // ID
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
                        	$out = '<p name="'.$field["columnname"].'" class="vtiger_prod_desc">'.nl2br($field["value"]).'</p>';
                        break;
                        case '69': // picture
				// Get the path
				$pics = $this->GetPicturePaths($field["fieldid"]);
				if($picnum == 'all') {
					for($i=0;$i<count($pics);$i++) {
						if($i != 0)
                                			$out .= '<br><img name="'.$field["columnname"].'" alt="product image" src="'.$pics[$i].'" />';
						else
                                			$out = '<img name="'.$field["columnname"].'" alt="product image" src="'.$pics[$i].'" />';
					}
				} else {
                                	$out = '<img name="'.$field["columnname"].'" alt="product image" src="'.$pics[($picnum-1)].'" />';
				}
                        break;
                        case '56': // checkbox
                                $out = '<input name="'.$field["columnname"].'" value="" maxlength="'.$field["maximumlength"].'" type="checkbox" class="inputbox" DISABLED>';
                        break;
                        default:
                                $out = "";
                        break;

		}
		if($showlabel)
			return $field["fieldlabel"]." ".$out;
		else
			return $out;

	}
	function GetPicturePaths($fieldid)
	{
		$pic[] = "http://vtiger-demo.fosslabs.com/sandbox/mmbrich/vtigercrm/storage/2006/August/week1/9_193769.jpg";
		$pic[] = "http://vtiger-demo.fosslabs.com/sandbox/mmbrich/vtigercrm/storage/2006/August/week1/9_193769.jpg";
		return $pic;
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
