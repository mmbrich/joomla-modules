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
require_once($mainframe->getCfg('absolute_path').'/components/com_vtigerregistration/vtiger/VTigerField.class.php');
class VTigerForm extends VtigerField {
        var $data;

        function VtigerForm()
        {
                $this->conn = $this->VtigerConnection("fields");
        }
        function SaveFormFields($entityid,$module,$fields)
        {
                $this->data = array(
                                'entityid' => $entityid,
                                'module' => $module,
                                'fields' => $fields
                );
                $this->setData($this->data);
                $result = $this->execCommand('save_form_fields');
                return $result;
        }
	function SaveVtigerForm($module,$entityid)
	{
	        global $mosConfig_absolute_path;
        	$j=0;
        	foreach($_POST as $key=>$value) {
                	if(preg_match("/vtiger_/",$key)) {
                        	$columnname = substr( $key, (strpos($key,"_")+1), strlen($key) );
                        	$fields[$j]["columnname"] = $columnname;
                        	$fields[$j]["value"] = $value;
                        	//echo $columnname. " ".$value."<br>";
                        	$j++;
               		}
        	}
        	return $this->SaveFormFields($entityid,$module,$fields);
	}
	function BuySubscription($module,$entityid)
	{

	}
	function BuyProduct($module,$entityid)
	{

	}
	function BuyDownload($module,$entityid)
	{

	}
	private function _create_salesorder()
	{

	}
	private function _create_potential()
	{

	}
	private function _convert_so_to_invoice()
	{

	}
	private function _current_salesorder()
	{

	}
}
?>
