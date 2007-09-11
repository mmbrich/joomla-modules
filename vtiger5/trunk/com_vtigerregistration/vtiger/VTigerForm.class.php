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
		VTigerConnection::VTigerConnection();
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
				$search = array(
					'/\$/i',
					'/\^/i',
					'/http:\/\//i',
                                	'/https:\/\//i',
                                	'/ftp:\/\//i',
                                	'/mailto:/i',
					'/,/i',
					'/\'/',
					'/\"/'
				);
				$replace = array(
					'',
					'',
					'',
					'',
					'',
					'',
					'',
					'\\\'',
					'\\\"'
				);
                        	$fields[$j]["columnname"] = $columnname;
                        	$fields[$j]["value"] = preg_replace($search,$replace,$value);
                        	$j++;
                        	//echo $columnname. " ".$value." ".$entityid."<br><BR>";
               		}
        	}
		// upload the file
		if(file_exists($_FILES['vtiger_imagename']['tmp_name'])) {
			if (($_FILES["vtiger_imagename"]["type"] == "image/gif") || ($_FILES["vtiger_imagename"]["type"] == "image/jpeg") || ($_FILES["vtiger_imagename"]["type"] == "image/pjpeg")) {
				$filename = $_FILES['vtiger_imagename']['tmp_name'];
				if($handle = fopen($filename, "r")) {
					$fcontents = fread($handle, filesize($filename));
					fclose($handle);
					$fields[$j]["columnname"] = "imagename|".$_FILES["vtiger_imagename"]["name"]."|imagetype|".$_FILES["vtiger_imagename"]["type"];
					$fields[$j]["value"] = base64_encode($fcontents);
				} else {
					echo "<script type='text/javascript'>alert('File upload error, cannot write to file.');</script>";
					flush();
				}
			} else {
					echo "<script type='text/javascript'>alert('File upload error, wrong file type.');</script>";
					flush();
			}
		//} else {
			//echo "<script type='text/javascript'>alert('File upload error, file not registered. (too big?)');</script>";
			//flush();
		}
		//exit();
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
	function BuyProduct($productid)
	{
		global $my,$mosConfig_absolute_path;
		require_once($mosConfig_absolute_path . "/components/com_vtigerregistration/vtiger/VTigerSalesOrder.class.php");
		$SO = new VtigerSalesOrder();
		if(!$SO->Checkid($my->id) && !isset($_COOKIE["current_salesorder"])) {
			$soid = @$SO->CreateNewSalesOrder('');
			if($soid != "Response not of type text/xml") 
				setcookie("current_salesorder", $soid, time()+3600, '/');
		} else if (isset($_COOKIE["current_salesorder"]))
			$soid = $_COOKIE["current_salesorder"];

		if(!$soid || $soid == 0 || $soid == "Response not of type text/xml")
			$tmp = $SO->GetCurrentSalesOrders($my->id);

		if(is_array($tmp)) {
			$soid = $tmp[0]["salesorderid"];
			setcookie("current_salesorder", $soid, time()+3600, '/');
		}

		if($soid == 0 || $soid == "" || $soid == "Response not of type text/xml") {
			$soid = $SO->CreateNewSalesOrder($SO->contact->id);
			setcookie("current_salesorder", $soid, time()+3600, '/');
		}
		if($soid != "Response not of type text/xml") {
			if(!$my->id)
				$SO->PopulateSalesOrder($soid,'');
			else 
				$SO->PopulateSalesOrder($soid,$my->id);
		}

		return $soid;
	}
	function RelateContact($contactid,$entityid,$entitymodule)
	{
		global $my,$mosConfig_absolute_path;
		if(!$my->id)
			return;

		require_once($mosConfig_absolute_path."/components/com_vtigerregistration/vtiger/VTigerContact.class.php");
		$Contact = new VtigerContact($my->id);
                if($entitymodule == "Events")
			$Contact->RelateToEvent($entityid);

                else if($entitymodule == "Potentials")
			$Contact->RelateToPotential($entityid);

                else if($entitymodule == "Campaigns")
			$Contact->RelateToCampaign($entityid);

                else if($entitymodule == "Accounts")
			$Contact->RelateToAccount($entityid);
	}
	function SendFormEmail($mailto,$subject)
	{
		$from = "noreply@vtigerjoomla.com";
		$fromname = "Vtiger Forms";
		$mode = true;

		$body = "Data was submitted from a vtiger<->Joomla! form<br>";
		$body .= "The following information was submitted in the form:<br><br>";
        	foreach($_POST as $key=>$value) {
                	if(preg_match("/vtiger_/",$key)) {
                        	$columnname = substr( $key, (strpos($key,"_")+1), strlen($key) );
                        	$body .= "Column Name: ".$columnname."<br>";
				if(is_array($value))
					$body .= "Value: ".implode(', ',$value)."<br><br>";
				else
					$body .= "Value: ".$value."<br><br>";
               		}
        	}
		$body .= "<br>This information will be stored in your CRM System";

		mosMail($from,$fromname,$mailto,$subject,$body,$mode);
		return;
	}
}
?>
