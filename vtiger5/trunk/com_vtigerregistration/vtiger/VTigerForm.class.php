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
	var $file = "fields";

        function VtigerForm()
        {
                $this->conn = $this->VtigerConnection($this->file);
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
                        	$j++;
               		}
                        //echo $columnname. " ".$value." ".$entityid."<br><BR>";
        	}
		// upload the file
		if(file_exists($_FILES['vtiger_imagename']['tmp_name'])) {
			$filename = $_FILES['vtiger_imagename']['tmp_name'];
			$handle = fopen($filename, "r");
			$fcontents = fread($handle, filesize($filename));
			fclose($handle);
			$fields[$j]["columnname"] = "imagename|".$_FILES["vtiger_imagename"]["name"];
			$fields[$j]["value"] = base64_encode($fcontents);
		}
        	return $this->SaveFormFields($entityid,$module,$fields);
	}
	function GetModuleFields($module) 
	{
                $this->data = array( 'module'=>$module );
                $this->setData($this->data);
                return $this->execCommand('get_module_fields');
	}
	function GetModules() 
	{
                $this->data = array( 'module'=>'' );
                $this->setData($this->data);
                return $this->execCommand('get_modules');
	}
	function BuySubscription($module,$entityid)
	{
		return 10;
	}
	function BuyProduct($module,$entityid)
	{

	}
	function BuyDownload($module,$entityid)
	{

	}
}
?>
