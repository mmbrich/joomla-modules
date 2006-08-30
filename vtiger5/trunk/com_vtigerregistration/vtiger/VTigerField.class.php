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
	var $id;
	var $field_cache = array();
	
	function VtigerField()
	{
		VTigerConnection::VTigerConnection();
	}
	function listAllowedFields($module_name) 
	{
                $this->data = array('module_name' => $module_name);
                $this->setData($this->data);

		$result = $this->execCommand('get_portal_register_fields');
		return $result;
	}
	function getNewRegisterFields($current) 
	{
                $this->data = array('fields' => $current);
                $this->setData($this->data);

		$result = $this->execCommand('get_new_register_fields');
		return $result;
	}
	function GetSingleFieldDetails($module,$columnname,$entityid)
	{
                $this->data = array(	'module'=>$module,
					'columnname'=>$columnname,
					'entityid'=>$entityid
		);
                $this->setData($this->data);
		$field = $this->execCommand('get_field_details');

		$this->field_cache[$module][$columnname] = $field[0];
		return $field;
	}
	function GetMultipleFieldDetails($fields) 
	{
                $this->data = array( 'fields'=>$fields );
                $this->setData($this->data);
		$fielddetails = $this->execCommand('get_multiple_field_details');

		return $fielddetails;
	}
	function CreateFieldHTML($module,$columnname,$viewtype,$showlabel=true,$entityid='',$picnum='all')
	{
		$tmp = $this->GetSingleFieldDetails($module,$columnname,$entityid);
		$field = $tmp[0];

		if($viewtype == "edit")
			return $this->_buildEditField($field,$showlabel);
		else
			return $this->_buildDetailField($field,$showlabel,$picnum);
	}
	function _buildEditField($field,$showlabel,$required=false)
	{
        	$classname  = 'inputbox';

		if($required == 'true')
			$out = "<span class='required'>";
		else
			$out = '';

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
                        case '75':
                        case '22': 
                        case '6': // Date
                        	$out .= '<input class="'.$classname.'" name="vtiger_'.$field["columnname"].'" value="'.$field["value"].'" maxlength="'.$field["maximumlength"].'" type="text">';
                        break;
                        case '21':
                        case '19':
                        	$out .= '<textarea name="vtiger_'.$field["columnname"].'" class="'.$classname.'" >'.$field["value"].'</textarea>';
                        break;
                        case '69': // picture
				// Get the path
				$pics = $this->GetPicturePaths($field);
				if(is_array($pics)) {
				  	if($field["picnum"] == 'all') {
						for($i=0;$i<count($pics);$i++) {
							if($i != 0)
                                				$out .= '<br><input name="vtiger_'.$field["columnname"].'" value="'.$pics[$i].'" maxlength="'.$field["maximumlength"].'" type="file" class="'.$classname.'" >';
							else
                                				$out .= '<input name="vtiger_'.$field["columnname"].'" value="'.$pics[$i].'" maxlength="'.$field["maximumlength"].'" type="file" class="'.$classname.'" >';
						}
				    	} else {
                                		$out .= '<input name="vtiger_'.$field["columnname"].'" type="file" /> &nbsp; &nbsp; <img src="'.$pics[($field["picnum"]-1)].'" border="0" border="0" height="50" hspace="6" width="50" />';
					}
				} else 
                                	$out .= '<input name="vtiger_'.$field["columnname"].'" value="" maxlength="'.$field["maximumlength"].'" type="file" class="'.$classname.'" >';
                        break;
                        case '56': // checkbox
				if($field["value"] == "on" || $field["value"] == 1) {
                                	$out .= '<input name="vtiger_'.$field["columnname"].'_el" CHECKED type="checkbox" class="'.$classname.'"  onclick="toggle_cb(\'vtiger_'.$field["columnname"].'\');" />';
                                	$out .= '<input name="vtiger_'.$field["columnname"].'" type="hidden" value="on" />';
				} else {
                                	$out .= '<input name="vtiger_'.$field["columnname"].'" type="checkbox" class="'.$classname.'" />';
				}
                        break;
                        case '15': // Picklist
                        case '111': // Picklist
				if(is_array($field["values"]))
					$values = $field["values"];
				else 
					$values = explode(",",$field["values"]);
                                $out .= '<select name="vtiger_'.$field["columnname"].'" class="'.$classname.'" >';
				$j=0;
                                foreach($values as $key=>$value) {
					if($value != "" && $field["value"] == $value)
                                		$out .= '<option value="'.$value.'" SELECTED >'.$value.'</option>';
					else if($value != "")
                                		$out .= '<option value="'.$value.'">'.$value.'</option>';
					$j++;
                               }
                               $out .= '</select>';
                        break;
                        case '33': // Multi Picklist
				$vals = explode(",",$field["value"]);
				if(is_array($field["values"]))
					$values = $field["values"];
				else 
					$values = explode(",",$field["values"]);

				$out .='';
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
                                $out .= "";
                        break;

		}
		if($required)
			$out .= "</span>";
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
                        case '111': // Picklist
                        case '22': // Picklist
                        case '75': // Picklist
                        case '6': // Date
                        	$out = '<span name="'.$field["columnname"].'">'.$field["value"].'</span>';
                        break;
                        case '21':
                        case '19':
                        	$out = '<p name="'.$field["columnname"].'" class="vtiger_prod_desc">'.nl2br($field["value"]).'</p>';
                        break;
                        case '69': // picture
				// Get the path
				$pics = $this->GetPicturePaths($field);
				if(!is_array($pics))
					$out = "";
				else {
				    if($picnum == 'all') {
					for($i=0;$i<count($pics);$i++) {
				    	$out = '<a href="javascript:;" onclick="window.open(\''.$pics[$i].'\',\'Image\',\'resizable=yes,width=400px,height=400px\')"><div class="mosimage" style="border-width: 1px; float: left; width: 120px;" align="center">';
						if($i != 0)
                                			$out .= '<br><img name="'.$field["columnname"].'" alt="product image" src="'.$pics[$i].'"  border="0" height="67" hspace="6" width="100px" />';
						else
                                			$out .= '<img name="'.$field["columnname"].'" alt="product image" src="'.$pics[$i].'"  border="0" height="67" hspace="6" width="100px" />';
					}
				    } else {
                                	$out = '<a href="javascript:;" onclick="window.open(\''.$pics[($picnum-1)].'\',\'Image\',\'resizable=yes,width=400px,height=400px\')"><div class="mosimage" style="border-width: 1px; float: left; width: 120px;" align="center"><img name="'.$field["columnname"].'" alt="product image" src="'.$pics[($picnum-1)].'"  border="0" height="67" hspace="6" width="100px" />';
				    }
            			    $out .= '</div></a>';
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
	function GetPicturePaths($field)
	{
		$pics = explode("|",$field["value"]);
		foreach($pics as $path) {
			//echo $path."<br>";
			if($path != "") {
				$pic[] = $this->GetImagePath($path);
			}
		}
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
